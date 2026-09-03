<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Court;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Carbon\CarbonImmutable;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getHeading(): ?string
    {
        return auth()->user()?->isStaff() ? 'Booking overview' : 'Your activity';
    }

    protected function getDescription(): ?string
    {
        return auth()->user()?->isStaff()
            ? 'Live totals from the booking and Open Play system.'
            : 'Your court bookings and hosted Open Play sessions.';
    }

    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();
        $isStaff = $user->isStaff();

        if (! $isStaff) {
            return $this->getCustomerStats($user);
        }

        $reservations = Reservation::query();
        $payments = Payment::query();

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
                ->url(PaymentResource::getUrl('index')),
            Stat::make('Total booked', number_format($booked))
                ->description('Paid and completed bookings')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('primary')
                ->url(ReservationResource::getUrl('index')),
            Stat::make('Total cancelled', number_format($cancelled))
                ->description('Cancelled reservations')
                ->descriptionIcon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->url(ReservationResource::getUrl('index')),
            Stat::make("Today's bookings", number_format($today))
                ->description(today()->format('F j, Y'))
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color('info')
                ->url(ReservationResource::getUrl('index')),
            Stat::make('Open Play sessions', number_format($openPlaySessions))
                ->description($user->canUseOpenPlay() ? 'Open PB Queue' : 'Request module access')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('warning')
                ->url(route('open-play')),
        ];

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

        return $stats;
    }

    private function getCustomerStats(User $user): array
    {
        $confirmed = Reservation::query()
            ->where('customer_email', $user->email)
            ->whereIn('status', ['paid', 'occupied', 'completed', 'no_show']);
        $hours = (clone $confirmed)->count();
        $upcoming = (clone $confirmed)
            ->with('court')
            ->where(function ($query): void {
                $query->whereDate('date', '>', today())
                    ->orWhere(function ($todayQuery): void {
                        $todayQuery->whereDate('date', today())
                            ->whereTime('time_slot', '>=', now()->format('H:i:s'));
                    });
            })
            ->orderBy('date')
            ->orderBy('time_slot')
            ->first();

        $stats = [
            Stat::make('Total hours booked', number_format($hours).' '.str('hour')->plural($hours))
                ->description('Confirmed court time')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color('primary')
                ->url(ReservationResource::getUrl('my-reservations')),
            Stat::make('Upcoming booking', $upcoming?->date?->format('M j, Y') ?? 'No upcoming booking')
                ->description($upcoming
                    ? CarbonImmutable::parse($upcoming->time_slot)->format('g:i A').' · '.($upcoming->court?->name ?? 'Court')
                    : 'Book a court to see it here')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color($upcoming ? 'success' : 'gray')
                ->url(ReservationResource::getUrl($upcoming ? 'my-reservations' : 'create')),
        ];

        if ($user->canUseOpenPlay()) {
            $stats[] = Stat::make('Open Play hosted', number_format($user->openPlaySessions()->count()))
                ->description('View your PB Queue sessions')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('warning')
                ->url(route('open-play'));
        }

        return $stats;
    }
}
