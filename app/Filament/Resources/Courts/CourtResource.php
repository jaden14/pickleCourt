<?php

namespace App\Filament\Resources\Courts;

use App\Filament\Resources\Courts\Pages\ListCourts;
use App\Models\Court;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CourtResource extends Resource
{
    protected static ?string $model = Court::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('courts')
                    ->visibility('public')
                    ->imageEditor()
                    ->fetchFileInformation(false)
                    ->maxSize(5120)
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'maintenance' => 'Maintenance',
                    ])
                    ->default('active')
                    ->required()
                    ->native(false),
                Toggle::make('is_reservable')
                    ->label('Reservable')
                    ->helperText('Allow players to reserve this court.')
                    ->default(true),
                TimePicker::make('booking_starts_at')
                    ->label('Booking starts at')
                    ->seconds(false)
                    ->minutesStep(60)
                    ->default('08:00')
                    ->required(),
                TimePicker::make('booking_ends_at')
                    ->label('Booking ends at')
                    ->seconds(false)
                    ->minutesStep(60)
                    ->default('20:00')
                    ->required(),
                TextInput::make('day_hourly_rate')
                    ->label('Day hourly rate')
                    ->prefix('₱')
                    ->numeric()
                    ->minValue(0)
                    ->default(200)
                    ->required(),
                TextInput::make('night_hourly_rate')
                    ->label('Night hourly rate')
                    ->prefix('₱')
                    ->numeric()
                    ->minValue(0)
                    ->default(250)
                    ->required(),
                TimePicker::make('day_rate_starts_at')
                    ->label('Day rate starts at')
                    ->seconds(false)
                    ->minutesStep(60)
                    ->default('06:00')
                    ->required(),
                TimePicker::make('night_rate_starts_at')
                    ->label('Night rate starts at')
                    ->seconds(false)
                    ->minutesStep(60)
                    ->default('18:00')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'maintenance' => 'Maintenance',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'maintenance' => 'warning',
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('4xl'),
                DeleteAction::make(),
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
            'index' => ListCourts::route('/'),
        ];
    }
}
