<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Payment;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\Url;

class MyReservations extends Page
{
    protected static string $resource = ReservationResource::class;

    protected string $view = 'filament.resources.reservations.pages.my-reservations';

    protected static ?string $title = 'My Reservations';

    #[Url]
    public ?int $payment = null;

    protected function getViewData(): array
    {
        $payments = Payment::query()
            ->with([
                'paymentMethod',
                'reservations' => fn ($query) => $query
                    ->with('court')
                    ->orderBy('date')
                    ->orderBy('time_slot'),
            ])
            ->where('customer_email', auth()->user()?->email)
            ->whereIn('status', ['submitted', 'paid'])
            ->latest()
            ->get();

        return [
            'payments' => $payments,
        ];
    }
}
