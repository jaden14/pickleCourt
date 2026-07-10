<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Payment;
use Filament\Resources\Pages\Page;

class PaymentConfirmation extends Page
{
    protected static string $resource = ReservationResource::class;

    protected string $view = 'filament.resources.reservations.pages.payment-confirmation';

    protected static ?string $title = 'Payment Submitted';

    public ?Payment $payment = null;

    public function mount(): void
    {
        $paymentId = session()->pull('submitted_payment_id');

        $this->payment = Payment::query()
            ->with(['paymentMethod', 'reservations'])
            ->whereKey($paymentId)
            ->where('customer_email', auth()->user()?->email)
            ->first();

        if (! $this->payment) {
            $this->redirect(ReservationResource::getUrl('index'), navigate: true);
        }
    }

    public function redirectToReservations(): void
    {
        $this->redirect(ReservationResource::getUrl('index'), navigate: true);
    }
}
