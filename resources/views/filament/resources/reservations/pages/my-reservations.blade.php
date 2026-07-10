<x-filament-panels::page>
    <style>
        .my-bookings { display:grid; gap:1rem; }
        .my-payment { overflow:hidden; border:1px solid rgb(229 231 235); border-radius:.9rem; background:white; box-shadow:0 1px 3px rgb(0 0 0 / .06); }
        .dark .my-payment { border-color:rgb(55 65 81); background:rgb(17 24 39); }
        .my-payment.is-target { border-color:#f59e0b; box-shadow:0 0 0 2px rgb(245 158 11 / .2); }
        .my-payment-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem 1.2rem; border-bottom:1px solid rgb(229 231 235); }
        .dark .my-payment-head { border-color:rgb(55 65 81); }
        .my-payment-head h2 { margin:0; color:rgb(17 24 39); font-size:1rem; }
        .dark .my-payment-head h2 { color:white; }
        .my-payment-head p { margin:.15rem 0 0; color:rgb(100 116 139); font-size:.75rem; }
        .my-status { padding:.3rem .6rem; border-radius:999px; font-size:.72rem; font-weight:800; }
        .my-status.pending { background:#fef3c7; color:#92400e; }
        .my-status.paid { background:#dcfce7; color:#166534; }
        .my-schedule { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.7rem; padding:1rem; }
        .my-slot { display:grid; grid-template-columns:4.25rem 1fr auto; align-items:center; gap:.8rem; padding:.7rem; border:1px solid rgb(229 231 235); border-radius:.7rem; }
        .dark .my-slot { border-color:rgb(55 65 81); }
        .my-date { text-align:center; }
        .my-date strong { display:block; color:#d97706; font-size:1.2rem; }
        .my-date span { color:rgb(100 116 139); font-size:.68rem; text-transform:uppercase; }
        .my-details strong { display:block; color:rgb(17 24 39); }
        .dark .my-details strong { color:white; }
        .my-details span { color:rgb(100 116 139); font-size:.76rem; }
        .my-attendance { color:rgb(100 116 139); font-size:.72rem; font-weight:800; text-transform:capitalize; }
        .my-total { display:flex; justify-content:space-between; gap:1rem; padding:.9rem 1.2rem; border-top:1px solid rgb(229 231 235); color:rgb(100 116 139); font-size:.8rem; }
        .dark .my-total { border-color:rgb(55 65 81); }
        .my-total strong { color:rgb(17 24 39); font-size:1rem; }
        .dark .my-total strong { color:white; }
        .my-empty { padding:4rem 1rem; border:1px dashed rgb(203 213 225); border-radius:.9rem; color:rgb(100 116 139); text-align:center; }
        @media(max-width:760px) { .my-schedule { grid-template-columns:1fr; } .my-payment-head { align-items:flex-start; } .my-slot { grid-template-columns:3.5rem 1fr; } .my-attendance { grid-column:2; } }
    </style>

    <div class="my-bookings">
        @forelse ($payments as $paymentRecord)
            <article @class(['my-payment', 'is-target' => $payment === $paymentRecord->id])>
                <header class="my-payment-head">
                    <div>
                        <h2>Payment #{{ $paymentRecord->id }}</h2>
                        <p>{{ $paymentRecord->created_at->format('M j, Y · g:i A') }} · {{ $paymentRecord->paymentMethod->name }}</p>
                    </div>
                    <span @class(['my-status', 'pending' => $paymentRecord->status === 'submitted', 'paid' => $paymentRecord->status === 'paid'])>
                        {{ $paymentRecord->status === 'paid' ? 'Payment confirmed' : 'Payment under review' }}
                    </span>
                </header>

                <div class="my-schedule">
                    @foreach ($paymentRecord->reservations as $reservation)
                        @php($start = Carbon\CarbonImmutable::parse($reservation->time_slot))
                        <div class="my-slot">
                            <div class="my-date"><strong>{{ $reservation->date->format('j') }}</strong><span>{{ $reservation->date->format('M Y') }}</span></div>
                            <div class="my-details">
                                <strong>{{ $reservation->court->name }}</strong>
                                <span>{{ $reservation->date->format('l') }} · {{ $start->format('g:i A') }} - {{ $start->addHour()->format('g:i A') }}</span>
                            </div>
                            <span class="my-attendance">{{ str_replace('_', ' ', $reservation->status) }}</span>
                        </div>
                    @endforeach
                </div>

                <footer class="my-total"><span>{{ $paymentRecord->reservations->count() }} reserved {{ $paymentRecord->reservations->count() === 1 ? 'hour' : 'hours' }}</span><strong>₱{{ number_format((float) $paymentRecord->total_amount, 2) }}</strong></footer>
            </article>
        @empty
            <div class="my-empty"><strong>No reservations yet.</strong><br>Your submitted and confirmed bookings will appear here.</div>
        @endforelse
    </div>
</x-filament-panels::page>
