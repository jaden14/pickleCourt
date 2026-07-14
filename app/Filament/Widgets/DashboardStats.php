<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Court;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class DashboardStats extends StatsOverviewWidget
{
    protected ?string $heading = 'Booking overview';

    protected ?string $description = 'Live totals from the booking and Open Play system.';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();
        $isStaff = $user->isStaff();

        $reservations = Reservation::query()
            ->when(! $isStaff, fn (Builder $query): Builder => $query->where('customer_email', $user->email));
        $payments = Payment::query()
            ->when(! $isStaff, fn (Builder $query): Builder => $query->where('customer_email', $user->email));

        $paidRevenue = (clone $payments)->where('status', 'paid')->sum('total_amount');
        $booked = (clone $reservations)->whereIn('status', ['paid', 'occupied', 'completed', 'no_show'])->count();
        $cancelled = (clone $reservations)->where('status', 'cancelled')->count();
        $today = (clone $reservations)
            ->whereDate('date', today())
            ->whereIn('status', ['paid', 'occupied', 'completed', 'no_show'])
            ->count();
        $pending = (clone $payments)->where('status', 'submitted')->count();
        $openPlaySessions = $user->openPlaySessions()->count();

        $stats = [
            Stat::make('Total payments', '₱'.number_format((float) $paidRevenue, 2))
                ->description('Confirmed payments')
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->color('success')
                ->url($isStaff ? PaymentResource::getUrl('index') : ReservationResource::getUrl('my-reservations')),
            Stat::make('Total booked', number_format($booked))
                ->description('Paid and completed bookings')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('primary')
                ->url($isStaff ? ReservationResource::getUrl('index') : ReservationResource::getUrl('my-reservations')),
            Stat::make('Total cancelled', number_format($cancelled))
                ->description('Cancelled reservations')
                ->descriptionIcon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->url($isStaff ? ReservationResource::getUrl('index') : ReservationResource::getUrl('my-reservations')),
            Stat::make("Today's bookings", number_format($today))
                ->description(today()->format('F j, Y'))
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color('info')
                ->url($isStaff ? ReservationResource::getUrl('index') : ReservationResource::getUrl('my-reservations')),
            Stat::make('Open Play sessions', number_format($openPlaySessions))
                ->description($user->canUseOpenPlay() ? 'Open PB Queue' : 'Request module access')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('warning')
                ->url(route('open-play')),
        ];

        if ($isStaff) {
            $stats[] = Stat::make('Pending payments', number_format($pending))
                ->description('Waiting for review')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($pending > 0 ? 'warning' : 'gray')
                ->url(PaymentResource::getUrl('index'));
            $stats[] = Stat::make('Active courts', number_format(Court::query()->where('status', 'active')->count()))
                ->description('Available court inventory')
                ->descriptionIcon(Heroicon::OutlinedBuildingStorefront)
                ->color('success');
            $stats[] = Stat::make('Customers', number_format(User::query()->where('role', 'customer')->count()))
                ->description('Registered customer accounts')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('info');
        }

        return $stats;
    }
}
