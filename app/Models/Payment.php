<?php

namespace App\Models;

use App\Filament\Resources\Reservations\ReservationResource;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    protected $fillable = [
        'payment_method_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_amount',
        'reference_number',
        'proof_image',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function markAsPaid(): void
    {
        DB::transaction(function (): void {
            $this->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $this->reservations()->update(['status' => 'paid']);
        });

        $this->clearAdminNotifications();

        if ($customer = User::query()->where('email', $this->customer_email)->first()) {
            $notification = Notification::make()
                ->title('Payment confirmed')
                ->body('Your payment of ₱'.number_format((float) $this->total_amount, 2).' was confirmed. Your court schedule is now booked.')
                ->success()
                ->actions([
                    Action::make('viewReservations')
                        ->label('View reservations')
                        ->url(ReservationResource::getUrl('my-reservations', [
                            'payment' => $this->getKey(),
                        ])),
                ]);

            $customer->notifyNow($notification->toDatabase());
        }
    }

    public function cancelBooking(): void
    {
        DB::transaction(function (): void {
            $this->update([
                'status' => 'rejected',
                'paid_at' => null,
            ]);

            $this->reservations()->update([
                'status' => 'cancelled',
                'expires_at' => null,
            ]);
        });

        $this->clearAdminNotifications();
    }

    public function checkInAllReservations(): void
    {
        $this->reservations()
            ->where('status', 'paid')
            ->update(['status' => 'occupied']);
    }

    public function markAllReservationsAsNoShow(): void
    {
        $this->reservations()
            ->where('status', 'paid')
            ->update(['status' => 'no_show']);
    }

    public function completeAllReservations(): void
    {
        $this->reservations()
            ->where('status', 'occupied')
            ->update(['status' => 'completed']);
    }

    public function canBeCancelled(): bool
    {
        if ($this->status !== 'paid') {
            return false;
        }

        $firstReservation = $this->reservations()
            ->orderBy('date')
            ->orderBy('time_slot')
            ->first();

        if (! $firstReservation) {
            return false;
        }

        $startsAt = CarbonImmutable::parse(
            $firstReservation->date->toDateString().' '.$firstReservation->time_slot,
        );

        return $startsAt->greaterThanOrEqualTo(now()->addHours(24));
    }

    public function canMarkAllAsNoShow(): bool
    {
        if ($this->status !== 'paid') {
            return false;
        }

        $firstReservation = $this->reservations()
            ->where('status', 'paid')
            ->orderBy('date')
            ->orderBy('time_slot')
            ->first();

        if (! $firstReservation) {
            return false;
        }

        $startsAt = CarbonImmutable::parse(
            $firstReservation->date->toDateString().' '.$firstReservation->time_slot,
        );

        return ! $startsAt->isFuture();
    }

    public function canBeRescheduled(): bool
    {
        if ($this->status !== 'paid') {
            return false;
        }

        $reservations = $this->reservations()->get();

        if ($reservations->isEmpty() || $reservations->contains(fn (Reservation $reservation): bool => $reservation->status !== 'paid')) {
            return false;
        }

        return $reservations->every(fn (Reservation $reservation): bool => CarbonImmutable::parse(
            $reservation->date->toDateString().' '.$reservation->time_slot,
        )->isFuture());
    }

    /** @param array<int, array{reservation_id: int|string, court_id: int|string, date: string, time_slot: string}> $slots */
    public function reschedule(array $slots): void
    {
        if (! $this->canBeRescheduled()) {
            throw new \DomainException('Only future paid bookings can be rescheduled.');
        }

        DB::transaction(function () use ($slots): void {
            $reservations = $this->reservations()
                ->whereKey(collect($slots)->pluck('reservation_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if (($reservations->count() !== count($slots)) || ($reservations->count() !== $this->reservations()->count())) {
                throw new \DomainException('Every linked reservation must be included.');
            }

            $updates = [];
            $newTotal = 0.0;
            $newSlotKeys = [];

            foreach ($slots as $slot) {
                $reservation = $reservations->get((int) $slot['reservation_id']);
                $court = Court::query()
                    ->whereKey($slot['court_id'])
                    ->where('status', 'active')
                    ->where('is_reservable', true)
                    ->lockForUpdate()
                    ->first();

                $date = CarbonImmutable::parse($slot['date'])->toDateString();
                $timeSlot = CarbonImmutable::parse($slot['time_slot'])->format('H:i:s');
                $startsAt = CarbonImmutable::parse($date.' '.$timeSlot);
                $newSlotKey = $court?->getKey().'|'.$date.'|'.$timeSlot;

                if ((! $reservation) || (! $court) || (! $startsAt->isFuture()) || isset($newSlotKeys[$newSlotKey])) {
                    throw new \DomainException('A selected court, date, or time is invalid.');
                }

                $newSlotKeys[$newSlotKey] = true;

                $regularStart = CarbonImmutable::parse($court->booking_starts_at);
                $regularEnd = CarbonImmutable::parse($court->booking_ends_at);
                $slotTime = CarbonImmutable::parse($timeSlot);
                $isRegularHour = (! $slotTime->lessThan($regularStart)) && $slotTime->lessThan($regularEnd);
                $isAddedHour = CourtUnavailability::query()
                    ->where(fn ($query) => $query->whereNull('court_id')->orWhere('court_id', $court->getKey()))
                    ->whereDate('date', $date)
                    ->whereTime('time_slot', $timeSlot)
                    ->where('action', 'add')
                    ->exists();
                $isDisabled = CourtUnavailability::query()
                    ->where(fn ($query) => $query->whereNull('court_id')->orWhere('court_id', $court->getKey()))
                    ->whereDate('date', $date)
                    ->whereTime('time_slot', $timeSlot)
                    ->where('action', 'disable')
                    ->exists();
                $isTaken = Reservation::query()
                    ->whereNotIn('id', $reservations->keys())
                    ->where('status', '!=', 'cancelled')
                    ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                    ->where('court_id', $court->getKey())
                    ->whereDate('date', $date)
                    ->whereTime('time_slot', $timeSlot)
                    ->exists();

                if (((! $isRegularHour) && (! $isAddedHour)) || $isDisabled || $isTaken) {
                    throw new \DomainException('One or more selected hours are unavailable.');
                }

                $rate = $court->hourlyRateFor($timeSlot);
                $newTotal += (float) $rate;
                $updates[] = compact('reservation', 'court', 'date', 'timeSlot', 'rate');
            }

            if ((int) round($newTotal * 100) !== (int) round(((float) $this->total_amount) * 100)) {
                throw new \DomainException('The new schedule must have the same total amount as the original payment.');
            }

            foreach ($updates as $update) {
                $update['reservation']->update([
                    'court_id' => $update['court']->getKey(),
                    'date' => $update['date'],
                    'time_slot' => $update['timeSlot'],
                    'hourly_rate' => $update['rate'],
                ]);
            }
        });
    }

    protected function clearAdminNotifications(): void
    {
        DatabaseNotification::query()
            ->where(function ($query): void {
                $query
                    ->where('data->viewData->payment_id', $this->getKey())
                    ->orWhere('data', 'like', '%/payments/'.$this->getKey().'%');
            })
            ->delete();
    }
}
