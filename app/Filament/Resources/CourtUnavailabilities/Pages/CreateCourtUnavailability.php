<?php

namespace App\Filament\Resources\CourtUnavailabilities\Pages;

use App\Filament\Resources\CourtUnavailabilities\CourtUnavailabilityResource;
use App\Models\CourtUnavailability;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateCourtUnavailability extends CreateRecord
{
    protected static string $resource = CourtUnavailabilityResource::class;

    protected bool $isWholeDay = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->isWholeDay = (bool) ($data['whole_day'] ?? false);
        unset($data['whole_day']);

        if ($this->isWholeDay) {
            $data['court_id'] = null;
            $data['action'] = 'disable';
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        if (! $this->isWholeDay) {
            return parent::handleRecordCreation($data);
        }

        return DB::transaction(function () use ($data): CourtUnavailability {
            $firstOverride = null;

            foreach (array_keys(CourtUnavailabilityResource::hourOptions()) as $timeSlot) {
                $override = CourtUnavailability::query()->updateOrCreate(
                    [
                        'court_id' => null,
                        'date' => $data['date'],
                        'time_slot' => $timeSlot,
                    ],
                    [
                        'action' => 'disable',
                        'reason' => $data['reason'] ?? null,
                    ],
                );

                $firstOverride ??= $override;
            }

            return $firstOverride;
        });
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->isWholeDay
            ? 'Whole-day maintenance scheduled.'
            : parent::getCreatedNotificationTitle();
    }
}
