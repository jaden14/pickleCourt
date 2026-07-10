<?php

namespace App\Filament\Resources\Reservations;

use App\Filament\Resources\Reservations\Pages\CreateReservation;
use App\Filament\Resources\Reservations\Pages\EditReservation;
use App\Filament\Resources\Reservations\Pages\ListReservations;
use App\Filament\Resources\Reservations\Pages\MyReservations;
use App\Filament\Resources\Reservations\Pages\PaymentConfirmation;
use App\Filament\Resources\Reservations\Pages\ReservationCheckout;
use App\Filament\Resources\Reservations\Pages\WeeklyReservations;
use App\Models\Reservation;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('court_id')
                    ->relationship('court', 'name', modifyQueryUsing: fn ($query) => $query
                        ->where('status', 'active')
                        ->where('is_reservable', true))
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('date')
                    ->native(false)
                    ->minDate(today())
                    ->required(),
                TimePicker::make('time_slot')
                    ->label('Time slot')
                    ->seconds(false)
                    ->minutesStep(60)
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending_payment' => 'Pending payment',
                        'paid' => 'Booked / Paid',
                        'occupied' => 'Occupied',
                        'completed' => 'Completed',
                        'no_show' => 'No show',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('paid')
                    ->required()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('court.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('time_slot')
                    ->label('Time slot')
                    ->formatStateUsing(function (string $state): string {
                        $start = CarbonImmutable::parse($state);

                        return $start->format('g:i A').' - '.$start->addHour()->format('g:i A');
                    })
                    ->sortable(),
                TextColumn::make('hourly_rate')
                    ->label('Rate')
                    ->money('PHP')
                    ->placeholder('Not recorded')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending_payment' => 'Pending payment',
                        'paid' => 'Booked / Paid',
                        'occupied' => 'Occupied',
                        'completed' => 'Completed',
                        'no_show' => 'No show',
                        'cancelled' => 'Cancelled',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending_payment' => 'warning',
                        'paid' => 'success',
                        'occupied' => 'info',
                        'completed' => 'gray',
                        'no_show' => 'danger',
                        'cancelled' => 'danger',
                    }),
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->placeholder('Admin booking')
                    ->toggleable(),
                TextColumn::make('paymentMethod.name')
                    ->label('Payment method')
                    ->placeholder('Not specified')
                    ->toggleable(),
                TextColumn::make('payment_reference')
                    ->label('Reference')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('payment_proof')
                    ->label('Payment proof')
                    ->disk('public')
                    ->square()
                    ->url(fn (Reservation $record): ?string => $record->payment_proof
                        ? Storage::disk('public')->url($record->payment_proof)
                        : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Payment status')
                    ->options([
                        'pending_payment' => 'Pending payment',
                        'paid' => 'Booked / Paid',
                        'occupied' => 'Occupied',
                        'completed' => 'Completed',
                        'no_show' => 'No show',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_method_id')
                    ->label('Payment method')
                    ->relationship('paymentMethod', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('markOccupied')
                    ->label('Check in')
                    ->icon(Heroicon::OutlinedUserPlus)
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalDescription('Confirm that the customer has arrived and is now using the court.')
                    ->visible(fn (Reservation $record): bool => $record->status === 'paid')
                    ->action(fn (Reservation $record) => $record->update(['status' => 'occupied']))
                    ->successNotificationTitle('Court marked as occupied.'),
                Action::make('markCompleted')
                    ->label('Complete')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->visible(fn (Reservation $record): bool => $record->status === 'occupied')
                    ->action(fn (Reservation $record) => $record->update(['status' => 'completed']))
                    ->successNotificationTitle('Reservation marked as completed.'),
                Action::make('markNoShow')
                    ->label('No show')
                    ->icon(Heroicon::OutlinedUserMinus)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Reservation $record): bool => $record->status === 'paid')
                    ->action(fn (Reservation $record) => $record->update(['status' => 'no_show']))
                    ->successNotificationTitle('Reservation marked as no show.'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => WeeklyReservations::route('/'),
            'list' => ListReservations::route('/list'),
            'create' => CreateReservation::route('/create'),
            'checkout' => ReservationCheckout::route('/checkout'),
            'confirmation' => PaymentConfirmation::route('/payment-confirmation'),
            'my-reservations' => MyReservations::route('/my-reservations'),
            'edit' => EditReservation::route('/{record}/edit'),
        ];
    }
}
