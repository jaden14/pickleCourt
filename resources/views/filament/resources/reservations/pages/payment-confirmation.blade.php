<x-filament-panels::page>
    @if ($payment)
        <style>
            .confirmation-wrap { min-height:65vh; display:grid; place-items:center; padding:2rem 0; }
            .confirmation-card { width:min(100%,42rem); padding:2.5rem; border:1px solid rgb(229 231 235); border-radius:1.2rem; background:white; box-shadow:0 20px 50px rgb(0 0 0 / .12); text-align:center; }
            .dark .confirmation-card { border-color:rgb(55 65 81); background:rgb(17 24 39); }
            .confirmation-icon { display:grid; width:5rem; height:5rem; margin:0 auto 1.25rem; place-items:center; border-radius:50%; background:#fef3c7; color:#b45309; }
            .confirmation-icon svg { width:2.7rem; height:2.7rem; }
            .confirmation-card h2 { margin:0; color:rgb(17 24 39); font-size:1.8rem; }
            .dark .confirmation-card h2 { color:white; }
            .confirmation-copy { max-width:32rem; margin:.75rem auto 1.5rem; color:rgb(100 116 139); }
            .confirmation-summary { display:grid; grid-template-columns:repeat(3,1fr); gap:.6rem; padding:1rem; border-radius:.8rem; background:rgb(248 250 252); text-align:left; }
            .dark .confirmation-summary { background:rgb(31 41 55); }
            .confirmation-summary span { display:block; color:rgb(100 116 139); font-size:.7rem; text-transform:uppercase; }
            .confirmation-summary strong { display:block; margin-top:.15rem; color:rgb(17 24 39); }
            .dark .confirmation-summary strong { color:white; }
            .confirmation-redirect { margin-top:1.5rem; color:rgb(100 116 139); font-size:.82rem; }
            .confirmation-count { color:#d97706; font-size:1.05rem; font-weight:900; font-variant-numeric:tabular-nums; }
            .confirmation-button { display:inline-flex; margin-top:.8rem; padding:.7rem 1rem; border:0; border-radius:.6rem; background:#d97706; color:white; cursor:pointer; font-weight:800; }
            @media(max-width:600px) { .confirmation-card { padding:1.5rem; } .confirmation-summary { grid-template-columns:1fr; text-align:center; } }
        </style>

        <div class="confirmation-wrap">
            <section
                class="confirmation-card"
                x-data="{ seconds: 8, timer: null }"
                x-init="timer = setInterval(() => { if (seconds <= 1) { clearInterval(timer); seconds = 0; $wire.redirectToReservations(); } else { seconds--; } }, 1000)"
            >
                <div class="confirmation-icon"><x-heroicon-o-clock /></div>
                <h2>Payment submitted for confirmation</h2>
                <p class="confirmation-copy">Thank you! Your payment is being reviewed by our staff. We will notify you as soon as it is confirmed.</p>

                <div class="confirmation-summary">
                    <div><span>Total payment</span><strong>₱{{ number_format((float) $payment->total_amount, 2) }}</strong></div>
                    <div><span>Payment method</span><strong>{{ $payment->paymentMethod->name }}</strong></div>
                    <div><span>Reserved hours</span><strong>{{ $payment->reservations->count() }}</strong></div>
                </div>

                <p class="confirmation-redirect">Returning to reservations in <span class="confirmation-count" x-text="seconds">8</span> seconds…</p>
                <button type="button" class="confirmation-button" wire:click="redirectToReservations">Return now</button>
            </section>
        </div>
    @endif
</x-filament-panels::page>
