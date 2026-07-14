<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Court;
use App\Models\CourtUnavailability;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class WeeklyReservations extends Page
{
    protected static string $resource = ReservationResource::class;

    protected string $view = 'filament.resources.reservations.pages.weekly-reservations';

    protected static ?string $title = 'Weekly Reservations';

    #[Url(as: 'week')]
    public string $weekStart = '';

    #[Url(as: 'date')]
    public string $selectedDate = '';

    /** @var array<string, array{court_id: int, court_name: string, date: string, time_slot: string, hourly_rate: string}> */
    public array $selectedBookings = [];

    public function mount(): void
    {
        $this->weekStart = $this->normalizeWeekStart($this->weekStart)->toDateString();
        $this->selectedDate = $this->normalizeSelectedDate($this->selectedDate)->toDateString();
    }

    public function previousWeek(): void
    {
        $this->weekStart = $this->startDate()->subWeek()->toDateString();
        $this->selectedDate = $this->weekStart;
    }

    public function nextWeek(): void
    {
        $this->weekStart = $this->startDate()->addWeek()->toDateString();
        $this->selectedDate = $this->weekStart;
    }

    public function currentWeek(): void
    {
        $this->weekStart = today()->toDateString();
        $this->selectedDate = today()->toDateString();
    }

    public function selectDate(string $date): void
    {
        $candidate = CarbonImmutable::parse($date)->startOfDay();

        if ($candidate->betweenIncluded($this->startDate(), $this->startDate()->addDays(6))) {
            $this->selectedDate = $candidate->toDateString();
        }
    }

    public function book(int $courtId, string $date, string $timeSlot): void
    {
        $court = Court::query()
            ->whereKey($courtId)
            ->where('status', 'active')
            ->where('is_reservable', true)
            ->first();

        $date = CarbonImmutable::parse($date)->toDateString();

        if ((! $court) || ($date < today()->toDateString()) || (! in_array($timeSlot, $this->timeSlotsFor($court, $date), true))) {
            Notification::make()
                ->danger()
                ->title('This booking slot is no longer available.')
                ->send();

            return;
        }

        $isBooked = Reservation::query()
            ->where('status', '!=', 'cancelled')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now()))
            ->where('court_id', $court->getKey())
            ->whereDate('date', $date)
            ->whereTime('time_slot', $timeSlot)
            ->exists();

        $disabledOverride = CourtUnavailability::query()
            ->where(fn ($query) => $query
                ->whereNull('court_id')
                ->orWhere('court_id', $court->getKey()))
            ->whereDate('date', $date)
            ->whereTime('time_slot', $timeSlot)
            ->where('action', 'disable')
            ->first(['reason']);

        $isDisabled = $disabledOverride !== null;

        if ($isBooked || $isDisabled) {
            Notification::make()
                ->danger()
                ->title($isDisabled
                    ? 'This hour is unavailable: '.($disabledOverride->reason ?: 'Court closure').'.'
                    : 'Court is already booked for this time.')
                ->send();

            return;
        }

        $slotKey = $this->slotKey($court->getKey(), $date, $timeSlot);

        if (isset($this->selectedBookings[$slotKey])) {
            unset($this->selectedBookings[$slotKey]);

            return;
        }

        $this->selectedBookings[$slotKey] = [
            'court_id' => $court->getKey(),
            'court_name' => $court->name,
            'date' => $date,
            'time_slot' => $timeSlot,
            'hourly_rate' => $court->hourlyRateFor($timeSlot),
        ];
    }

    public function removeSelectedBooking(string $slotKey): void
    {
        unset($this->selectedBookings[$slotKey]);
    }

    public function proceedToCheckout(): void
    {
        if ($this->selectedBookings === []) {
            return;
        }

        $expiresAt = now()->addMinutes(5);
        $reservationIds = [];

        try {
            DB::transaction(function () use ($expiresAt, &$reservationIds): void {
                foreach ($this->selectedBookings as $booking) {
                    $court = Court::query()
                        ->whereKey($booking['court_id'])
                        ->where('status', 'active')
                        ->where('is_reservable', true)
                        ->lockForUpdate()
                        ->firstOrFail();

                    Reservation::query()
                        ->where('status', 'pending_payment')
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '<=', now())
                        ->update(['status' => 'cancelled']);

                    $isUnavailable = Reservation::query()
                        ->where('status', '!=', 'cancelled')
                        ->where(fn ($query) => $query
                            ->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now()))
                        ->where('court_id', $court->getKey())
                        ->whereDate('date', $booking['date'])
                        ->whereTime('time_slot', $booking['time_slot'])
                        ->exists() || CourtUnavailability::query()
                        ->where(fn ($query) => $query
                            ->whereNull('court_id')
                            ->orWhere('court_id', $court->getKey()))
                        ->whereDate('date', $booking['date'])
                        ->whereTime('time_slot', $booking['time_slot'])
                        ->where('action', 'disable')
                        ->exists();

                    if ($isUnavailable) {
                        throw new \RuntimeException;
                    }

                    $reservation = Reservation::create([
                        'court_id' => $court->getKey(),
                        'date' => $booking['date'],
                        'time_slot' => $booking['time_slot'],
                        'hourly_rate' => $booking['hourly_rate'],
                        'status' => 'pending_payment',
                        'expires_at' => $expiresAt,
                    ]);

                    $reservationIds[] = $reservation->getKey();
                }
            });
        } catch (\RuntimeException) {
            Notification::make()
                ->danger()
                ->title('One or more selected hours are no longer available.')
                ->send();

            return;
        }

        session()->put('reservation_checkout', [
            'bookings' => array_values($this->selectedBookings),
            'reservation_ids' => $reservationIds,
            'expires_at' => $expiresAt->toIso8601String(),
        ]);

        $this->redirect(ReservationResource::getUrl('checkout'), navigate: true);
    }

    public function totalPayment(): float
    {
        return array_sum(array_map(
            fn (array $booking): float => (float) $booking['hourly_rate'],
            $this->selectedBookings,
        ));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('myReservations')
                ->label('My reservations')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->visible(fn (): bool => ! (auth()->user()?->isStaff() ?? false))
                ->url(ReservationResource::getUrl('my-reservations')),
            Action::make('list')
                ->label('List view')
                ->icon(Heroicon::OutlinedListBullet)
                ->visible(fn (): bool => auth()->user()?->isStaff() ?? false)
                ->url(ReservationResource::getUrl('list')),
        ];
    }

    protected function getViewData(): array
    {
        Reservation::query()
            ->where('status', 'pending_payment')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'cancelled']);

        $dates = collect(range(0, 6))
            ->map(fn (int $offset): CarbonImmutable => $this->startDate()->addDays($offset));

        $courts = Court::query()
            ->where('status', 'active')
            ->where('is_reservable', true)
            ->orderBy('name')
            ->get();

        $fullDayClosures = CourtUnavailability::query()
            ->whereNull('court_id')
            ->where('action', 'disable')
            ->whereBetween('date', [
                $dates->first()->toDateString(),
                $dates->last()->toDateString(),
            ])
            ->get(['date', 'time_slot', 'reason'])
            ->groupBy(fn (CourtUnavailability $override): string => $override->date->toDateString())
            ->mapWithKeys(function ($overrides, string $date): array {
                $coversWholeDay = $overrides
                    ->pluck('time_slot')
                    ->map(fn (string $timeSlot): string => CarbonImmutable::parse($timeSlot)->format('H:i:s'))
                    ->unique()
                    ->count() === 24;

                return $coversWholeDay
                    ? [$date => ($overrides->first()->reason ?: 'Court closure')]
                    : [];
            });

        $bookedSlots = Reservation::query()
            ->where('status', '!=', 'cancelled')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now()))
            ->whereDate('date', $this->selectedDate)
            ->get(['court_id', 'date', 'time_slot', 'status'])
            ->mapWithKeys(fn (Reservation $reservation): array => [
                $this->slotKey(
                    $reservation->court_id,
                    $reservation->date->toDateString(),
                    CarbonImmutable::parse($reservation->time_slot)->format('H:i:s'),
                ) => $reservation->status,
            ]);

        $overrides = CourtUnavailability::query()
            ->whereDate('date', $this->selectedDate)
            ->where('action', 'disable')
            ->get(['court_id', 'date', 'time_slot', 'reason']);

        $disabledSlots = collect();

        foreach ($overrides as $override) {
            $courtIds = $override->court_id ? [$override->court_id] : $courts->modelKeys();

            foreach ($courtIds as $courtId) {
                $disabledSlots->put($this->slotKey(
                    $courtId,
                    $override->date->toDateString(),
                    CarbonImmutable::parse($override->time_slot)->format('H:i:s'),
                ), $override->reason ?: 'Court closure');
            }
        }

        return [
            'dates' => $dates,
            'courts' => $courts,
            'fullDayClosures' => $fullDayClosures,
            'bookedSlots' => $bookedSlots,
            'disabledSlots' => $disabledSlots,
        ];
    }

    public function slotKey(int $courtId, string $date, string $timeSlot): string
    {
        return "{$courtId}|{$date}|{$timeSlot}";
    }

    public function isPastSlot(string $date, string $timeSlot): bool
    {
        return CarbonImmutable::parse("{$date} {$timeSlot}")->isPast();
    }

    /** @return array<int, string> */
    public function timeSlotsFor(Court $court, ?string $date = null): array
    {
        $slots = [];
        $time = CarbonImmutable::parse($court->booking_starts_at ?? '08:00:00');
        $closingTime = CarbonImmutable::parse($court->booking_ends_at ?? '20:00:00');

        while ($time->lessThan($closingTime)) {
            $slots[] = $time->format('H:i:s');
            $time = $time->addHour();
        }

        if ($date) {
            $addedSlots = CourtUnavailability::query()
                ->where(fn ($query) => $query
                    ->whereNull('court_id')
                    ->orWhere('court_id', $court->getKey()))
                ->whereDate('date', $date)
                ->where('action', 'add')
                ->pluck('time_slot')
                ->map(fn (string $timeSlot): string => CarbonImmutable::parse($timeSlot)->format('H:i:s'))
                ->all();

            $slots = [...$slots, ...$addedSlots];
        }

        $slots = array_values(array_unique($slots));
        sort($slots);

        return $slots;
    }

    protected function startDate(): CarbonImmutable
    {
        return $this->normalizeWeekStart($this->weekStart);
    }

    protected function normalizeWeekStart(?string $date): CarbonImmutable
    {
        try {
            return filled($date) ? CarbonImmutable::parse($date)->startOfDay() : CarbonImmutable::today();
        } catch (\Throwable) {
            return CarbonImmutable::today();
        }
    }

    protected function normalizeSelectedDate(?string $date): CarbonImmutable
    {
        try {
            $selected = filled($date) ? CarbonImmutable::parse($date)->startOfDay() : CarbonImmutable::today();

            return $selected->betweenIncluded($this->startDate(), $this->startDate()->addDays(6))
                ? $selected
                : $this->startDate();
        } catch (\Throwable) {
            return $this->startDate();
        }
    }
}
