<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class ReservationCheckout extends Page
{
    use WithFileUploads;

    protected static string $resource = ReservationResource::class;

    protected string $view = 'filament.resources.reservations.pages.reservation-checkout';

    protected static ?string $title = 'Reservation Checkout';

    public string $customerName = '';

    public string $customerEmail = '';

    public string $customerPhone = '';

    public string $paymentMethodId = '';

    public string $paymentReference = '';

    public $paymentProof;

    /** @var array<int, array{court_id: int, court_name: string, date: string, time_slot: string, hourly_rate: string}> */
    public array $bookings = [];

    /** @var array<int, int> */
    public array $reservationIds = [];

    public string $expiresAt = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->customerName = $user?->name ?? '';
        $this->customerEmail = $user?->email ?? '';
        $this->customerPhone = $user?->phone ?? '';

        $checkout = session()->get('reservation_checkout', []);
        $this->bookings = $checkout['bookings'] ?? [];
        $this->reservationIds = $checkout['reservation_ids'] ?? [];
        $this->expiresAt = $checkout['expires_at'] ?? '';

        if (($this->bookings === []) || ($this->reservationIds === [])) {
            Notification::make()
                ->warning()
                ->title('Select at least one booking hour first.')
                ->send();

            $this->redirect(ReservationResource::getUrl('index'), navigate: true);

            return;
        }

        if (now()->greaterThanOrEqualTo($this->expiresAt)) {
            $this->expireCheckout();
        }
    }

    public function cancelCheckout(): void
    {
        $this->releaseHolds();

        Notification::make()
            ->info()
            ->title('Checkout cancelled. Your selected hours are available again.')
            ->send();

        $this->redirect(ReservationResource::getUrl('index'), navigate: true);
    }

    public function expireCheckout(): void
    {
        $this->releaseHolds();

        Notification::make()
            ->warning()
            ->title('Checkout expired. Your selected hours were released.')
            ->send();

        $this->redirect(ReservationResource::getUrl('index'), navigate: true);
    }

    public function submitPayment(): void
    {
        $validated = $this->validate([
            'customerName' => ['required', 'string', 'max:255'],
            'customerEmail' => ['required', 'email', 'max:255'],
            'customerPhone' => ['required', 'string', 'max:30'],
            'paymentMethodId' => ['required', 'integer', 'exists:payment_methods,id'],
            'paymentReference' => ['nullable', 'required_without:paymentProof', 'string', 'max:255'],
            'paymentProof' => ['nullable', 'required_without:paymentReference', 'image', 'max:5120'],
        ]);

        $paymentMethod = PaymentMethod::query()
            ->whereKey($validated['paymentMethodId'])
            ->where('is_active', true)
            ->first();

        if (! $paymentMethod) {
            $this->addError('paymentMethodId', 'This payment method is no longer available.');

            return;
        }

        $paymentProofPath = $this->paymentProof?->store('payment-proofs', 'public');
        $payment = null;

        try {
            DB::transaction(function () use ($validated, $paymentMethod, $paymentProofPath, &$payment): void {
                $holds = Reservation::query()
                    ->whereKey($this->reservationIds)
                    ->lockForUpdate()
                    ->get();

                if (
                    ($holds->count() !== count($this->reservationIds)) ||
                    $holds->contains(fn (Reservation $reservation): bool => ($reservation->status !== 'pending_payment') ||
                        (! $reservation->expires_at) ||
                        $reservation->expires_at->isPast())
                ) {
                    throw new \RuntimeException;
                }

                $payment = Payment::create([
                    'payment_method_id' => $paymentMethod->getKey(),
                    'customer_name' => $validated['customerName'],
                    'customer_email' => $validated['customerEmail'],
                    'customer_phone' => $validated['customerPhone'],
                    'total_amount' => $holds->sum(fn (Reservation $reservation): float => (float) $reservation->hourly_rate),
                    'reference_number' => $validated['paymentReference'],
                    'proof_image' => $paymentProofPath,
                    'status' => 'submitted',
                ]);

                foreach ($holds as $reservation) {
                    $reservation->update([
                        'status' => 'pending_payment',
                        'expires_at' => null,
                        'customer_name' => $validated['customerName'],
                        'customer_email' => $validated['customerEmail'],
                        'customer_phone' => $validated['customerPhone'],
                        'payment_method_id' => $paymentMethod->getKey(),
                        'payment_id' => $payment->getKey(),
                        'payment_reference' => $validated['paymentReference'],
                        'payment_proof' => $paymentProofPath,
                    ]);
                }
            });
        } catch (\RuntimeException) {
            Notification::make()
                ->danger()
                ->title('One or more selected hours are no longer available.')
                ->send();

            return;
        }

        session()->forget('reservation_checkout');
        session()->put('submitted_payment_id', $payment->getKey());

        $adminNotification = Notification::make()
            ->title('New payment submitted')
            ->body($validated['customerName'].' submitted ₱'.number_format((float) $payment->total_amount, 2).' for verification.')
            ->viewData(['payment_id' => $payment->getKey()])
            ->icon('heroicon-o-banknotes')
            ->warning()
            ->actions([
                Action::make('view')
                    ->label('Review payment')
                    ->url(PaymentResource::getUrl('view', ['record' => $payment])),
            ]);

        User::query()->whereIn('role', ['admin', 'staff'])->each(
            fn (User $user) => $user->notifyNow($adminNotification->toDatabase()),
        );

        Notification::make()
            ->success()
            ->title('Payment submitted for verification.')
            ->body('The reservation is pending until an administrator verifies the payment.')
            ->send();

        $this->redirect(ReservationResource::getUrl('confirmation'), navigate: true);
    }

    protected function releaseHolds(): void
    {
        Reservation::query()
            ->whereKey($this->reservationIds)
            ->where('status', 'pending_payment')
            ->whereNotNull('expires_at')
            ->update([
                'status' => 'cancelled',
                'expires_at' => null,
            ]);

        session()->forget('reservation_checkout');
        $this->bookings = [];
        $this->reservationIds = [];
    }

    protected function getViewData(): array
    {
        $paymentMethods = PaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return [
            'paymentMethods' => $paymentMethods,
            'selectedPaymentMethod' => $paymentMethods->firstWhere('id', (int) $this->paymentMethodId),
            'totalPayment' => array_sum(array_map(
                fn (array $booking): float => (float) $booking['hourly_rate'],
                $this->bookings,
            )),
        ];
    }
}
