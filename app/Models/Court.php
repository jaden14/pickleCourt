<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    protected $fillable = [
        'image',
        'name',
        'status',
        'is_reservable',
        'booking_starts_at',
        'booking_ends_at',
        'day_hourly_rate',
        'night_hourly_rate',
        'day_rate_starts_at',
        'night_rate_starts_at',
    ];

    protected function casts(): array
    {
        return [
            'is_reservable' => 'boolean',
            'day_hourly_rate' => 'decimal:2',
            'night_hourly_rate' => 'decimal:2',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function unavailabilities(): HasMany
    {
        return $this->hasMany(CourtUnavailability::class);
    }

    public function hourlyRateFor(string $timeSlot): string
    {
        $time = CarbonImmutable::parse($timeSlot);
        $dayStartsAt = CarbonImmutable::parse($this->day_rate_starts_at ?? '06:00:00');
        $nightStartsAt = CarbonImmutable::parse($this->night_rate_starts_at ?? '18:00:00');
        $isNightRate = $time->lessThan($dayStartsAt) || (! $time->lessThan($nightStartsAt));

        return $isNightRate
            ? ($this->night_hourly_rate ?? '250.00')
            : ($this->day_hourly_rate ?? '200.00');
    }
}
