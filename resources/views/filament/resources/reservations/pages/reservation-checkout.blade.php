<x-filament-panels::page>
    <style>
        .checkout-grid { display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(20rem, .75fr); gap: 1.25rem; align-items: start; }
        .checkout-card { padding: 1.25rem; border: 1px solid rgb(229 231 235); border-radius: .85rem; background: white; box-shadow: 0 1px 3px rgb(0 0 0 / .06); }
        .dark .checkout-card { border-color: rgb(55 65 81); background: rgb(17 24 39); }
        .checkout-title { margin: 0 0 1rem; color: rgb(17 24 39); font-size: 1rem; font-weight: 800; }
        .dark .checkout-title { color: white; }
        .checkout-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .checkout-field { display: grid; gap: .35rem; }
        .checkout-field.full { grid-column: 1 / -1; }
        .checkout-field label { color: rgb(55 65 81); font-size: .8rem; font-weight: 700; }
        .dark .checkout-field label { color: rgb(209 213 219); }
        .checkout-input { width: 100%; padding: .65rem .75rem; border: 1px solid rgb(209 213 219); border-radius: .55rem; background: white; color: rgb(17 24 39); }
        .dark .checkout-input { border-color: rgb(75 85 99); background: rgb(31 41 55); color: white; }
        .checkout-error { color: #dc2626; font-size: .75rem; }
        .checkout-or { display: flex; align-items: center; gap: .75rem; margin: 1rem 0; color: rgb(107 114 128); font-size: .75rem; font-weight: 700; text-transform: uppercase; }
        .checkout-or::before, .checkout-or::after { content: ''; flex: 1; height: 1px; background: rgb(229 231 235); }
        .checkout-upload { padding: 1rem; border: 1px dashed rgb(209 213 219); border-radius: .65rem; background: rgb(249 250 251); }
        .dark .checkout-upload { border-color: rgb(75 85 99); background: rgb(31 41 55); }
        .checkout-proof-preview { display: block; width: min(100%, 20rem); max-height: 18rem; margin: .75rem auto 0; object-fit: contain; border-radius: .5rem; }
        .checkout-qr { display: grid; justify-items: center; gap: .75rem; margin-top: 1rem; padding: 1rem; border: 1px dashed #f59e0b; border-radius: .75rem; background: #fffbeb; text-align: center; }
        .dark .checkout-qr { background: rgb(120 53 15 / .15); }
        .checkout-qr img { width: min(100%, 20rem); aspect-ratio: 1; object-fit: contain; border-radius: .5rem; background: white; }
        .checkout-qr p { margin: 0; color: rgb(75 85 99); font-size: .82rem; white-space: pre-line; }
        .dark .checkout-qr p { color: rgb(209 213 219); }
        .checkout-bookings { display: grid; gap: .65rem; }
        .checkout-booking { display: flex; justify-content: space-between; gap: 1rem; padding-bottom: .65rem; border-bottom: 1px solid rgb(229 231 235); color: rgb(55 65 81); font-size: .8rem; }
        .dark .checkout-booking { border-color: rgb(55 65 81); color: rgb(209 213 219); }
        .checkout-total { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin-top: 1rem; }
        .checkout-total span { color: rgb(107 114 128); font-size: .8rem; }
        .checkout-total strong { color: rgb(17 24 39); font-size: 1.6rem; }
        .dark .checkout-total strong { color: white; }
        .checkout-submit { width: 100%; margin-top: 1rem; padding: .8rem 1rem; border: 0; border-radius: .6rem; background: #d97706; color: white; cursor: pointer; font-weight: 800; }
        .checkout-submit:hover { background: #b45309; }
        .checkout-submit:disabled { cursor: wait; opacity: .65; }
        .checkout-timer { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; padding: .85rem 1rem; border: 1px solid #f59e0b; border-radius: .75rem; background: #fffbeb; color: #92400e; }
        .dark .checkout-timer { background: rgb(120 53 15 / .18); color: #fcd34d; }
        .checkout-countdown { font-size: 1.25rem; font-variant-numeric: tabular-nums; font-weight: 900; }
        .checkout-actions { display: grid; grid-template-columns: 1fr auto; gap: .65rem; margin-top: 1rem; }
        .checkout-actions .checkout-submit { margin-top: 0; }
        .checkout-cancel { padding: .8rem 1rem; border: 1px solid #dc2626; border-radius: .6rem; background: transparent; color: #dc2626; cursor: pointer; font-weight: 800; }
        .checkout-cancel:hover { background: #fef2f2; }
        @media (max-width: 900px) { .checkout-grid { grid-template-columns: 1fr; } }
        @media (max-width: 600px) { .checkout-fields { grid-template-columns: 1fr; } }
    </style>

    <div
        class="checkout-timer"
        x-data="{
            remaining: Math.max(0, Math.floor((Date.parse(@js($expiresAt)) - Date.now()) / 1000)),
            timer: null,
            format() {
                const hours = String(Math.floor(this.remaining / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((this.remaining % 3600) / 60)).padStart(2, '0');
                const seconds = String(this.remaining % 60).padStart(2, '0');
                return `${hours}:${minutes}:${seconds}`;
            },
        }"
        x-init="timer = setInterval(() => { if (remaining <= 1) { remaining = 0; clearInterval(timer); $wire.expireCheckout(); } else { remaining--; } }, 1000)"
    >
        <div>
            <strong>Complete checkout before the hold expires</strong><br>
            <span style="font-size:.78rem;">Your selected court hours are temporarily unavailable to other customers.</span>
        </div>
        <span class="checkout-countdown" x-text="format()">00:05:00</span>
    </div>

    <form wire:submit="submitPayment" class="checkout-grid">
        <div style="display:grid; gap:1.25rem;">
            <section class="checkout-card">
                <h2 class="checkout-title">Personal information</h2>
                <div class="checkout-fields">
                    <div class="checkout-field full">
                        <label for="customer-name">Full name</label>
                        <input id="customer-name" class="checkout-input" type="text" wire:model="customerName" autocomplete="name">
                        @error('customerName') <span class="checkout-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="checkout-field">
                        <label for="customer-email">Email address</label>
                        <input id="customer-email" class="checkout-input" type="email" wire:model="customerEmail" autocomplete="email">
                        @error('customerEmail') <span class="checkout-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="checkout-field">
                        <label for="customer-phone">Phone number</label>
                        <input id="customer-phone" class="checkout-input" type="tel" wire:model="customerPhone" autocomplete="tel">
                        @error('customerPhone') <span class="checkout-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="checkout-card">
                <h2 class="checkout-title">Payment method</h2>
                <div class="checkout-field">
                    <label for="payment-method">Choose how to pay</label>
                    <select id="payment-method" class="checkout-input" wire:model.live="paymentMethodId">
                        <option value="">Select a payment method</option>
                        @foreach ($paymentMethods as $paymentMethod)
                            <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                        @endforeach
                    </select>
                    @error('paymentMethodId') <span class="checkout-error">{{ $message }}</span> @enderror
                </div>

                @if ($selectedPaymentMethod)
                    <div class="checkout-qr">
                        <strong>{{ $selectedPaymentMethod->name }}</strong>
                        <img src="{{ Storage::disk('public')->url($selectedPaymentMethod->qr_code) }}" alt="{{ $selectedPaymentMethod->name }} payment QR code">
                        @if ($selectedPaymentMethod->instructions)
                            <p>{{ $selectedPaymentMethod->instructions }}</p>
                        @endif
                    </div>
                @endif

                <div class="checkout-field" style="margin-top:1rem;">
                    <label for="payment-reference">Payment reference number</label>
                    <input id="payment-reference" class="checkout-input" type="text" wire:model="paymentReference" placeholder="Enter the transaction/reference number">
                    @error('paymentReference') <span class="checkout-error">{{ $message }}</span> @enderror
                </div>

                <div class="checkout-or">or</div>

                <div class="checkout-field checkout-upload">
                    <label for="payment-proof">Upload proof of payment</label>
                    <input id="payment-proof" type="file" wire:model="paymentProof" accept="image/png,image/jpeg,image/webp">
                    <span style="color:rgb(107 114 128);font-size:.72rem;">PNG, JPG, or WebP · maximum 5 MB</span>
                    <span wire:loading wire:target="paymentProof" style="color:#d97706;font-size:.75rem;">Uploading screenshot…</span>
                    @error('paymentProof') <span class="checkout-error">{{ $message }}</span> @enderror

                    @if ($paymentProof)
                        <img src="{{ $paymentProof->temporaryUrl() }}" alt="Payment proof preview" class="checkout-proof-preview">
                    @endif
                </div>
            </section>
        </div>

        <aside class="checkout-card">
            <h2 class="checkout-title">Booking summary</h2>
            <div class="checkout-bookings">
                @foreach ($bookings as $booking)
                    @php($start = Carbon\CarbonImmutable::createFromFormat('H:i:s', $booking['time_slot']))
                    <div class="checkout-booking">
                        <div>
                            <strong>{{ $booking['court_name'] }}</strong><br>
                            {{ Carbon\CarbonImmutable::parse($booking['date'])->format('D, M j, Y') }}<br>
                            {{ $start->format('g:i A') }} - {{ $start->addHour()->format('g:i A') }}
                        </div>
                        <strong>₱{{ number_format((float) $booking['hourly_rate'], 2) }}</strong>
                    </div>
                @endforeach
            </div>
            <div class="checkout-total">
                <span>Total payment</span>
                <strong>₱{{ number_format($totalPayment, 2) }}</strong>
            </div>
            <div class="checkout-actions">
                <button type="submit" class="checkout-submit" wire:loading.attr="disabled" wire:target="submitPayment">
                    <span wire:loading.remove wire:target="submitPayment">Submit Payment</span>
                    <span wire:loading wire:target="submitPayment">Submitting…</span>
                </button>
                <button type="button" class="checkout-cancel" wire:click="cancelCheckout" wire:loading.attr="disabled">Cancel</button>
            </div>
        </aside>
    </form>
</x-filament-panels::page>
