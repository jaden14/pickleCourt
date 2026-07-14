<?php

namespace App\Filament\Resources\CourtUnavailabilities;

use App\Filament\Resources\CourtUnavailabilities\Pages\CreateCourtUnavailability;
use App\Filament\Resources\CourtUnavailabilities\Pages\EditCourtUnavailability;
use App\Filament\Resources\CourtUnavailabilities\Pages\ListCourtUnavailabilities;
use App\Models\Court;
use App\Models\CourtUnavailability;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CourtUnavailabilityResource extends Resource
{
    protected static ?string $model = CourtUnavailability::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Court Hour Overrides';

    protected static ?string $modelLabel = 'court hour override';

    public static function canAccess(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('whole_day')
                    ->label('Whole-day maintenance')
                    ->helperText('Disable every schedule for all courts on the selected date.')
                    ->live()
                    ->hiddenOn('edit')
                    ->default(false),
                Select::make('court_id')
                    ->label('Court')
                    ->options(fn (): array => ['all' => 'All courts'] + Court::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->default('all')
                    ->hidden(fn (Get $get): bool => (bool) $get('whole_day'))
                    ->afterStateHydrated(fn (Select $component, mixed $state) => $component->state($state ?? 'all'))
                    ->dehydrateStateUsing(fn (mixed $state): mixed => $state === 'all' ? null : $state),
                DatePicker::make('date')
                    ->native(false)
                    ->minDate(today())
                    ->required(),
                Select::make('time_slot')
                    ->label('Hour')
                    ->options(static::hourOptions())
                    ->searchable()
                    ->native(false)
                    ->hidden(fn (Get $get): bool => (bool) $get('whole_day'))
                    ->required(fn (Get $get): bool => ! $get('whole_day')),
                Select::make('action')
                    ->options([
                        'disable' => 'Disable this hour',
                        'add' => 'Add this hour',
                    ])
                    ->default('disable')
                    ->native(false)
                    ->hidden(fn (Get $get): bool => (bool) $get('whole_day'))
                    ->required(fn (Get $get): bool => ! $get('whole_day')),
                Select::make('reason')
                    ->label('Reason')
                    ->options([
                        'Maintenance' => 'Maintenance',
                        'Private event' => 'Private event',
                        'Tournament' => 'Tournament',
                        'Weather closure' => 'Weather closure',
                    ])
                    ->native(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('court.name')
                    ->formatStateUsing(fn (?string $state): string => $state ?? 'All courts')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('time_slot')
                    ->label('Hour')
                    ->formatStateUsing(fn (string $state): string => static::formatHourRange($state))
                    ->sortable(),
                TextColumn::make('action')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'add' ? 'Added' : 'Disabled')
                    ->color(fn (string $state): string => $state === 'add' ? 'success' : 'danger'),
                TextColumn::make('reason')
                    ->placeholder('No reason provided')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('removeWholeDayMaintenance')
                    ->label('Remove whole-day maintenance')
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Remove whole-day maintenance?')
                    ->modalDescription(fn (CourtUnavailability $record): string => 'This will reopen all courts for every hour on '.$record->date->format('M j, Y').'.')
                    ->modalSubmitActionLabel('Yes, reopen this day')
                    ->visible(fn (CourtUnavailability $record): bool => $record->court_id === null
                        && $record->action === 'disable'
                        && static::isWholeDayMaintenance($record->date->toDateString()))
                    ->action(function (CourtUnavailability $record): void {
                        CourtUnavailability::query()
                            ->whereNull('court_id')
                            ->whereDate('date', $record->date)
                            ->where('action', 'disable')
                            ->delete();

                        Notification::make()
                            ->success()
                            ->title('Whole-day maintenance removed.')
                            ->body('All courts have been reopened for '.$record->date->format('M j, Y').'.')
                            ->send();
                    }),
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
            'index' => ListCourtUnavailabilities::route('/'),
            'create' => CreateCourtUnavailability::route('/create'),
            'edit' => EditCourtUnavailability::route('/{record}/edit'),
        ];
    }

    /** @return array<string, string> */
    public static function hourOptions(): array
    {
        return collect(range(0, 23))
            ->mapWithKeys(function (int $hour): array {
                $start = CarbonImmutable::createFromTime($hour);

                return [$start->format('H:i:s') => $start->format('g:i A').' - '.$start->addHour()->format('g:i A')];
            })
            ->all();
    }

    public static function formatHourRange(string $time): string
    {
        $start = CarbonImmutable::parse($time);

        return $start->format('g:i A').' - '.$start->addHour()->format('g:i A');
    }

    public static function isWholeDayMaintenance(string $date): bool
    {
        static $wholeDayMaintenance = [];

        return $wholeDayMaintenance[$date] ??= CourtUnavailability::query()
            ->whereNull('court_id')
            ->whereDate('date', $date)
            ->where('action', 'disable')
            ->pluck('time_slot')
            ->map(fn (string $timeSlot): string => CarbonImmutable::parse($timeSlot)->format('H:i:s'))
            ->unique()
            ->count() === 24;
    }
}
