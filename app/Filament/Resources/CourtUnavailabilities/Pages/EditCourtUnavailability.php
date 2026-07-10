<?php

namespace App\Filament\Resources\CourtUnavailabilities\Pages;

use App\Filament\Resources\CourtUnavailabilities\CourtUnavailabilityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCourtUnavailability extends EditRecord
{
    protected static string $resource = CourtUnavailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
