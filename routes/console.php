<?php

use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    Reservation::query()
        ->whereIn('status', ['paid', 'occupied'])
        ->whereDate('date', '<=', today())
        ->chunkById(100, function ($reservations): void {
            foreach ($reservations as $reservation) {
                $endsAt = CarbonImmutable::parse(
                    $reservation->date->toDateString().' '.$reservation->time_slot,
                )->addHour();

                if ($endsAt->isFuture()) {
                    continue;
                }

                $reservation->update([
                    'status' => $reservation->status === 'occupied' ? 'completed' : 'no_show',
                ]);
            }
        });
})
    ->everyMinute()
    ->name('finalize-court-attendance')
    ->withoutOverlapping();
