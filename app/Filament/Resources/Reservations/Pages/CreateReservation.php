<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Court;
use App\Models\CourtUnavailability;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $court = Court::find($data['court_id']);
        $timeSlot = CarbonImmutable::parse($data['time_slot']);
        $startsAt = CarbonImmutable::parse($court?->booking_starts_at ?? '08:00:00');
        $endsAt = CarbonImmutable::parse($court?->booking_ends_at ?? '20:00:00');

        $isDisabled = CourtUnavailability::query()
            ->where(fn ($query) => $query
                ->whereNull('court_id')
                ->orWhere('court_id', $data['court_id']))
            ->whereDate('date', $data['date'])
            ->whereTime('time_slot', $data['time_slot'])
            ->where('action', 'disable')
            ->exists();

        $isAdded = CourtUnavailability::query()
            ->where(fn ($query) => $query
                ->whereNull('court_id')
                ->orWhere('court_id', $data['court_id']))
            ->whereDate('date', $data['date'])
            ->whereTime('time_slot', $data['time_slot'])
            ->where('action', 'add')
            ->exists();

        $isWithinRegularHours = (! $timeSlot->lessThan($startsAt)) && $timeSlot->lessThan($endsAt);

        if ((! $court) || ($timeSlot->minute !== 0) || ((! $isWithinRegularHours) && (! $isAdded)) || $isDisabled) {
            Notification::make()
                ->danger()
                ->title('This booking hour is not available.')
                ->send();

            throw new Halt;
        }

        $isBooked = Reservation::query()
            ->where('status', '!=', 'cancelled')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now()))
            ->where('court_id', $data['court_id'])
            ->whereDate('date', $data['date'])
            ->whereTime('time_slot', $data['time_slot'])
            ->exists();

        if ($isBooked) {
            Notification::make()
                ->danger()
                ->title('Court is already booked for this time.')
                ->send();

            throw new Halt;
        }

        $data['hourly_rate'] = $court->hourlyRateFor($data['time_slot']);

        return parent::handleRecordCreation($data);
    }
}
