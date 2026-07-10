<?php

namespace App\Filament\Resources\CourtUnavailabilities\Pages;

use App\Filament\Resources\CourtUnavailabilities\CourtUnavailabilityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourtUnavailabilities extends ListRecords
{
    protected static string $resource = CourtUnavailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
