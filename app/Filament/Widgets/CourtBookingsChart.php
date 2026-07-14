<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;

class CourtBookingsChart extends ChartWidget
{
    protected ?string $heading = 'Total Bookings per Day';

    protected ?string $description = 'Confirmed court bookings during the last seven days.';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '320px';

    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }

    protected function getData(): array
    {
        $firstDay = CarbonImmutable::today()->subDays(6);
        $days = collect(range(0, 6))
            ->map(fn (int $offset): CarbonImmutable => $firstDay->addDays($offset));

        $bookingsByDate = Reservation::query()
            ->whereIn('status', ['paid', 'occupied', 'completed', 'no_show'])
            ->whereBetween('date', [
                $days->first()->toDateString(),
                $days->last()->toDateString(),
            ])
            ->selectRaw('date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'Total bookings',
                    'data' => $days
                        ->map(fn (CarbonImmutable $day): int => (int) ($bookingsByDate[$day->toDateString()] ?? 0))
                        ->all(),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $days
                ->map(fn (CarbonImmutable $day): string => $day->format('D, M j'))
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
