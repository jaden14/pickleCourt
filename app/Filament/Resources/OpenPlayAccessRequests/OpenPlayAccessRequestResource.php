<?php

namespace App\Filament\Resources\OpenPlayAccessRequests;

use App\Filament\Resources\OpenPlayAccessRequests\Pages\ListOpenPlayAccessRequests;
use App\Models\OpenPlayAccessRequest;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class OpenPlayAccessRequestResource extends Resource
{
    protected static ?string $model = OpenPlayAccessRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Open Play';

    protected static ?string $navigationLabel = 'Access Requests';

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Customer')->searchable()->sortable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('message')->limit(60)->placeholder('No message')->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')->label('Requested')->since()->sortable(),
                TextColumn::make('reviewer.name')->label('Reviewed by')->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (OpenPlayAccessRequest $record): bool => $record->status !== 'approved')
                    ->action(fn (OpenPlayAccessRequest $record) => $record->update([
                        'status' => 'approved',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ])),
                Action::make('reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (OpenPlayAccessRequest $record): bool => $record->status !== 'rejected')
                    ->action(fn (OpenPlayAccessRequest $record) => $record->update([
                        'status' => 'rejected',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListOpenPlayAccessRequests::route('/')];
    }
}
