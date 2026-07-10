<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\CourtUnavailabilities\CourtUnavailabilityResource;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\Payments\Pages\ViewPayment;
use App\Models\Court;
use App\Models\CourtUnavailability;
use App\Models\Payment;
use App\Models\Reservation;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'customer_name';

    public static function canAccess(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer_name')->label('Customer'),
                TextEntry::make('customer_email')->label('Email'),
                TextEntry::make('customer_phone')->label('Phone'),
                TextEntry::make('paymentMethod.name')->label('Payment method'),
                TextEntry::make('total_amount')->label('Total payment')->money('PHP'),
                TextEntry::make('status')->badge(),
                TextEntry::make('reference_number')->label('Reference number')->placeholder('No reference supplied'),
                ImageEntry::make('proof_image')
                    ->label('Proof of payment')
                    ->disk('public')
                    ->height(400)
                    ->placeholder('No proof image supplied')
                    ->columnSpanFull(),
                RepeatableEntry::make('reservations')
                    ->label('Reserved courts and hours')
                    ->schema([
                        TextEntry::make('court.name')->label('Court'),
                        TextEntry::make('date')->date(),
                        TextEntry::make('time_slot')
                            ->label('Time')
                            ->formatStateUsing(function (string $state): string {
                                $start = CarbonImmutable::parse($state);

                                return $start->format('g:i A').' - '.$start->addHour()->format('g:i A');
                            }),
                        TextEntry::make('hourly_rate')->label('Rate')->money('PHP'),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending_payment' => 'Processing payment',
                                'paid' => 'Booked',
                                'occupied' => 'Occupied',
                                'completed' => 'Completed',
                                'no_show' => 'No show',
                                'cancelled' => 'Cancelled',
                            }),
                    ])
                    ->columns(5)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Submitted')->dateTime()->sortable(),
                TextColumn::make('customer_name')->label('Customer')->searchable(),
                TextColumn::make('paymentMethod.name')->label('Method'),
                TextColumn::make('reservations_count')->counts('reservations')->label('Hours'),
                TextColumn::make('total_amount')->label('Total')->money('PHP')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'submitted' => 'Pending verification',
                        'paid' => 'Paid',
                        'rejected' => 'Cancelled',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'warning',
                        'paid' => 'success',
                        'rejected' => 'danger',
                    }),
                TextColumn::make('reference_number')->label('Reference')->placeholder('—'),
                ImageColumn::make('proof_image')
                    ->label('Proof')
                    ->disk('public')
                    ->square()
                    ->url(fn (Payment $record): ?string => $record->proof_image
                        ? Storage::disk('public')->url($record->proof_image)
                        : null)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'submitted' => 'Pending verification',
                    'paid' => 'Paid',
                    'rejected' => 'Rejected',
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('confirmPayment')
                    ->label('Confirm paid')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('This will mark the payment and every linked court reservation as paid.')
                    ->visible(fn (Payment $record): bool => $record->status === 'submitted')
                    ->action(fn (Payment $record) => $record->markAsPaid())
                    ->successNotificationTitle('Payment and all reservations marked as paid.'),
                Action::make('cancelBooking')
                    ->label('Cancel booking')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel this booking?')
                    ->modalDescription('This will cancel the payment and every linked court reservation. The court hours will become available again.')
                    ->modalSubmitActionLabel('Yes, cancel booking')
                    ->visible(fn (Payment $record): bool => $record->status === 'paid' && $record->reservations()->where('status', 'paid')->exists())
                    ->disabled(fn (Payment $record): bool => ! $record->canBeCancelled())
                    ->tooltip(fn (Payment $record): ?string => $record->canBeCancelled()
                        ? null
                        : 'Cancellation is not allowed within 24 hours of the earliest booked time.')
                    ->action(fn (Payment $record) => $record->cancelBooking())
                    ->successNotificationTitle('Booking cancelled and court hours released.'),
                Action::make('checkInAll')
                    ->label('Check in all')
                    ->icon(Heroicon::OutlinedUserPlus)
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Check in all booked courts?')
                    ->modalDescription('Every paid court and hour linked to this payment will be marked as occupied.')
                    ->modalSubmitActionLabel('Yes, check in all')
                    ->visible(fn (Payment $record): bool => $record->status === 'paid' && $record->reservations()->where('status', 'paid')->exists())
                    ->action(fn (Payment $record) => $record->checkInAllReservations())
                    ->successNotificationTitle('All linked reservations marked as occupied.'),
                Action::make('markAllNoShow')
                    ->label('No show all')
                    ->icon(Heroicon::OutlinedUserMinus)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Mark all as no show?')
                    ->modalDescription('Every still-booked reservation linked to this payment will be marked as no show.')
                    ->modalSubmitActionLabel('Yes, mark no show')
                    ->visible(fn (Payment $record): bool => $record->status === 'paid' && $record->reservations()->where('status', 'paid')->exists())
                    ->disabled(fn (Payment $record): bool => ! $record->canMarkAllAsNoShow())
                    ->tooltip(fn (Payment $record): ?string => $record->canMarkAllAsNoShow()
                        ? null
                        : 'No show can be marked once the first booked hour begins.')
                    ->action(fn (Payment $record) => $record->markAllReservationsAsNoShow())
                    ->successNotificationTitle('All linked reservations marked as no show.'),
                Action::make('completeAll')
                    ->label('Complete all')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Complete all occupied courts?')
                    ->modalDescription('Every occupied reservation linked to this payment will be marked as completed.')
                    ->modalSubmitActionLabel('Yes, complete all')
                    ->visible(fn (Payment $record): bool => $record->status === 'paid' && $record->reservations()->where('status', 'occupied')->exists())
                    ->action(fn (Payment $record) => $record->completeAllReservations())
                    ->successNotificationTitle('All occupied reservations marked as completed.'),
                static::rescheduleAction(),
                ViewAction::make(),
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
            'index' => ListPayments::route('/'),
            'view' => ViewPayment::route('/{record}'),
        ];
    }

    public static function rescheduleAction(): Action
    {
        return Action::make('reschedule')
            ->label('Reschedule')
            ->icon(Heroicon::OutlinedCalendarDays)
            ->color('warning')
            ->modalWidth('5xl')
            ->modalHeading('Move paid booking schedule')
            ->modalDescription('The new courts and hours must be available and must keep the same total payment amount.')
            ->modalSubmitActionLabel('Save new schedule')
            ->visible(fn (Payment $record): bool => $record->canBeRescheduled())
            ->fillForm(fn (Payment $record): array => [
                'reservations' => $record->reservations->map(fn ($reservation): array => [
                    'reservation_id' => $reservation->getKey(),
                    'court_id' => $reservation->court_id,
                    'date' => $reservation->date->toDateString(),
                    'time_slot' => CarbonImmutable::parse($reservation->time_slot)->format('H:i:s'),
                ])->all(),
            ])
            ->schema([
                Repeater::make('reservations')
                    ->label('Paid court hours')
                    ->schema([
                        Hidden::make('reservation_id'),
                        Placeholder::make('original_booking')
                            ->label('Original booking')
                            ->content(function (Get $get, Payment $record): string {
                                $reservation = $record->reservations->firstWhere('id', $get->integer('reservation_id'));

                                if (! $reservation) {
                                    return 'Unavailable';
                                }

                                $start = CarbonImmutable::parse($reservation->time_slot);

                                return $reservation->court->name.' · '.$reservation->date->format('M j, Y').' · '.$start->format('g:i A').' - '.$start->addHour()->format('g:i A');
                            })
                            ->columnSpanFull(),
                        Select::make('court_id')
                            ->label('Court')
                            ->options(fn (): array => Court::query()
                                ->where('status', 'active')
                                ->where('is_reservable', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('time_slot', null))
                            ->required(),
                        DatePicker::make('date')
                            ->native(false)
                            ->minDate(today())
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('time_slot', null))
                            ->required(),
                        Select::make('time_slot')
                            ->label('Hour')
                            ->options(fn (Get $get, Payment $record): array => static::rescheduleHourOptions(
                                $record,
                                $get->integer('reservation_id', true),
                                $get->integer('court_id', true),
                                $get->string('date', true),
                            ))
                            ->disableOptionWhen(fn (string $value, Get $get, Payment $record): bool => static::isRescheduleHourDisabled(
                                $record,
                                $get->integer('reservation_id', true),
                                $get->integer('court_id', true),
                                $get->string('date', true),
                                $value,
                            ) || static::isSelectedInAnotherRescheduleRow(
                                $get('../') ?? [],
                                $get->integer('reservation_id', true),
                                $get->integer('court_id', true),
                                $get->string('date', true),
                                $value,
                            ))
                            ->searchable()
                            ->native(false)
                            ->live()
                            ->required(),
                    ])
                    ->columns(3)
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, Payment $record, Action $action): void {
                try {
                    $record->reschedule($data['reservations']);
                } catch (\DomainException $exception) {
                    Notification::make()
                        ->danger()
                        ->title('Unable to reschedule booking')
                        ->body($exception->getMessage())
                        ->send();

                    $action->halt();
                }
            })
            ->successNotificationTitle('Paid booking schedule moved successfully.');
    }

    /** @return array<string, string> */
    protected static function rescheduleHourOptions(Payment $payment, ?int $reservationId, ?int $courtId, ?string $date): array
    {
        if ((! $courtId) || blank($date)) {
            return [];
        }

        $court = Court::find($courtId);

        if (! $court) {
            return [];
        }

        return collect(CourtUnavailabilityResource::hourOptions())
            ->mapWithKeys(function (string $label, string $timeSlot) use ($payment, $reservationId, $court, $date): array {
                $status = static::rescheduleHourStatus($payment, $reservationId, $court, $date, $timeSlot);
                $rate = number_format((float) $court->hourlyRateFor($timeSlot), 0);

                return [$timeSlot => "{$label} · ₱{$rate} · {$status}"];
            })
            ->filter(fn (string $label): bool => ! str_ends_with($label, 'Outside booking hours'))
            ->all();
    }

    protected static function isRescheduleHourDisabled(
        Payment $payment,
        ?int $reservationId,
        ?int $courtId,
        ?string $date,
        string $timeSlot,
    ): bool {
        $court = $courtId ? Court::find($courtId) : null;

        if ((! $court) || blank($date)) {
            return true;
        }

        return ! in_array(
            static::rescheduleHourStatus($payment, $reservationId, $court, $date, $timeSlot),
            ['Available', 'Current booking'],
            true,
        );
    }

    protected static function rescheduleHourStatus(
        Payment $payment,
        ?int $reservationId,
        Court $court,
        string $date,
        string $timeSlot,
    ): string {
        $date = CarbonImmutable::parse($date)->toDateString();
        $timeSlot = CarbonImmutable::parse($timeSlot)->format('H:i:s');
        $startsAt = CarbonImmutable::parse($date.' '.$timeSlot);
        $currentReservation = $reservationId ? Reservation::find($reservationId) : null;

        if (
            $currentReservation &&
            ($currentReservation->court_id === $court->getKey()) &&
            ($currentReservation->date->toDateString() === $date) &&
            (CarbonImmutable::parse($currentReservation->time_slot)->format('H:i:s') === $timeSlot)
        ) {
            return 'Current booking';
        }

        $regularStart = CarbonImmutable::parse($court->booking_starts_at);
        $regularEnd = CarbonImmutable::parse($court->booking_ends_at);
        $slotTime = CarbonImmutable::parse($timeSlot);
        $isRegularHour = (! $slotTime->lessThan($regularStart)) && $slotTime->lessThan($regularEnd);
        $overrideQuery = CourtUnavailability::query()
            ->where(fn ($query) => $query->whereNull('court_id')->orWhere('court_id', $court->getKey()))
            ->whereDate('date', $date)
            ->whereTime('time_slot', $timeSlot);

        if ((! $isRegularHour) && (! (clone $overrideQuery)->where('action', 'add')->exists())) {
            return 'Outside booking hours';
        }

        if ($startsAt->isPast()) {
            return 'Past';
        }

        if ((clone $overrideQuery)->where('action', 'disable')->exists()) {
            return 'Disabled';
        }

        $isTaken = Reservation::query()
            ->whereNotIn('id', $payment->reservations()->pluck('id'))
            ->where('status', '!=', 'cancelled')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where('court_id', $court->getKey())
            ->whereDate('date', $date)
            ->whereTime('time_slot', $timeSlot)
            ->exists();

        return $isTaken ? 'Booked' : 'Available';
    }

    /** @param array<string, array<string, mixed>> $rows */
    protected static function isSelectedInAnotherRescheduleRow(
        array $rows,
        ?int $currentReservationId,
        ?int $courtId,
        ?string $date,
        string $timeSlot,
    ): bool {
        if ((! $courtId) || blank($date)) {
            return false;
        }

        return collect($rows)->contains(fn (array $row): bool => ((int) ($row['reservation_id'] ?? 0) !== $currentReservationId) &&
            ((int) ($row['court_id'] ?? 0) === $courtId) &&
            (($row['date'] ?? null) === $date) &&
            (($row['time_slot'] ?? null) === $timeSlot));
    }
}
