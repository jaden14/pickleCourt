<x-filament-panels::page>
    <style>
        .booking-shell { display: grid; gap: 1rem; }
        .booking-toolbar { display: flex; align-items: center; justify-content: space-between; gap: .75rem; flex-wrap: wrap; }
        .booking-nav { display: flex; align-items: center; gap: .5rem; }
        .booking-range { color: rgb(75 85 99); font-size: .875rem; font-weight: 600; }
        .dark .booking-range { color: rgb(209 213 219); }
        .booking-week-scroll { overflow-x: auto; padding-bottom: .15rem; }
        .booking-days { display: grid; grid-template-columns: 4.5rem repeat(7, minmax(7rem, 1fr)) 4.5rem; min-width: 62rem; overflow: hidden; border: 1px solid rgb(229 231 235); border-radius: .85rem; background: white; box-shadow: 0 1px 2px rgb(0 0 0 / .05); }
        .dark .booking-days { border-color: rgb(55 65 81); background: rgb(17 24 39); }
        .booking-day { padding: .8rem .5rem; border: 0; border-right: 1px solid rgb(229 231 235); background: transparent; color: rgb(55 65 81); cursor: pointer; text-align: center; transition: background .15s, color .15s; }
        .dark .booking-day { border-color: rgb(55 65 81); color: rgb(209 213 219); }
        .booking-day:hover { background: rgb(249 250 251); }
        .dark .booking-day:hover { background: rgb(31 41 55); }
        .booking-day.is-selected { background: #f59e0b; color: #111827; box-shadow: inset 0 -4px 0 #b45309; }
        .dark .booking-day.is-selected { background: #f59e0b; color: #111827; }
        .booking-week-arrow { display: grid; place-items: center; border: 0; border-right: 1px solid rgb(229 231 235); background: rgb(249 250 251); color: rgb(75 85 99); cursor: pointer; transition: background .15s, color .15s; }
        .booking-week-arrow:last-child { border-right: 0; border-left: 1px solid rgb(229 231 235); }
        .booking-week-arrow:hover { background: #fef3c7; color: #92400e; }
        .booking-week-arrow svg { width: 1.35rem; height: 1.35rem; }
        .dark .booking-week-arrow { border-color: rgb(55 65 81); background: rgb(31 41 55); color: rgb(209 213 219); }
        .booking-day-name { display: block; font-size: .72rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .booking-day-number { display: block; margin-top: .12rem; font-size: 1.05rem; font-weight: 800; }
        .booking-day-month { display: block; font-size: .7rem; opacity: .8; }
        .booking-day-maintenance { display: inline-block; margin-top: .25rem; padding: .12rem .35rem; border-radius: 999px; background: #fee2e2; color: #b91c1c; font-size: .58rem; font-weight: 800; letter-spacing: .03em; text-transform: uppercase; }
        .booking-day.is-selected .booking-day-maintenance { background: #7f1d1d; color: #fee2e2; }
        .booking-selected-heading { display: flex; align-items: baseline; justify-content: space-between; gap: 1rem; }
        .booking-selected-heading h2 { color: rgb(17 24 39); font-size: 1.05rem; font-weight: 700; }
        .dark .booking-selected-heading h2 { color: white; }
        .booking-selected-heading span { color: rgb(107 114 128); font-size: .8rem; }
        .booking-courts-scroll { overflow-x: auto; padding: .1rem .1rem 1rem; }
        .booking-courts { display: grid; grid-template-columns: repeat(var(--court-columns, 4), minmax(17rem, 1fr)); gap: 1rem; align-items: start; }
        .booking-court { overflow: hidden; border: 1px solid rgb(229 231 235); border-radius: .85rem; background: white; box-shadow: 0 2px 5px rgb(0 0 0 / .07); }
        .dark .booking-court { border-color: rgb(55 65 81); background: rgb(17 24 39); }
        .booking-court-image { display: block; width: 100%; height: 11rem; object-fit: cover; background: rgb(243 244 246); }
        .booking-court-placeholder { display: grid; place-items: center; width: 100%; height: 11rem; background: rgb(243 244 246); color: rgb(156 163 175); }
        .dark .booking-court-placeholder { background: rgb(31 41 55); }
        .booking-court-title { margin: 0; padding: .8rem 1rem; border-bottom: 1px solid rgb(229 231 235); color: rgb(17 24 39); font-size: .95rem; font-weight: 800; text-align: center; text-transform: uppercase; }
        .dark .booking-court-title { border-color: rgb(55 65 81); color: white; }
        .booking-slots { display: grid; gap: .45rem; max-height: 34rem; overflow-y: auto; padding: .75rem; }
        .booking-slot { display: grid; grid-template-columns: 8.75rem 1fr; align-items: center; gap: .55rem; padding: .4rem; border: 1px solid rgb(229 231 235); border-radius: .6rem; }
        .dark .booking-slot { border-color: rgb(55 65 81); }
        .booking-time { color: rgb(75 85 99); font-size: .75rem; font-weight: 700; text-align: center; }
        .dark .booking-time { color: rgb(209 213 219); }
        .booking-button { min-height: 2.15rem; border: 0; border-radius: .5rem; background: #d97706; color: white; cursor: pointer; font-size: .75rem; font-weight: 700; transition: background .15s; }
        .booking-button:hover { background: #b45309; }
        .booking-button:disabled { cursor: not-allowed; }
        .booking-button.is-booked { background: rgb(254 226 226); color: rgb(220 38 38); }
        .dark .booking-button.is-booked { background: rgb(69 10 10 / .55); color: rgb(252 165 165); }
        .booking-button.is-pending { background: #fef3c7; color: #92400e; }
        .dark .booking-button.is-pending { background: rgb(120 53 15 / .45); color: #fcd34d; }
        .booking-button.is-occupied { background: #dbeafe; color: #1d4ed8; }
        .dark .booking-button.is-occupied { background: rgb(30 58 138 / .4); color: #93c5fd; }
        .booking-button.is-completed { background: #e2e8f0; color: #475569; }
        .dark .booking-button.is-completed { background: #1e293b; color: #94a3b8; }
        .booking-button.is-no-show { background: #ffedd5; color: #c2410c; }
        .dark .booking-button.is-no-show { background: rgb(124 45 18 / .45); color: #fdba74; }
        .booking-button.is-disabled { background: #fef3c7; color: #92400e; }
        .dark .booking-button.is-disabled { background: rgb(120 53 15 / .45); color: #fcd34d; }
        .booking-button.is-selected { background: #059669; color: white; box-shadow: 0 0 0 2px rgb(16 185 129 / .25); }
        .booking-button.is-selected:hover { background: #047857; }
        .booking-button.is-past { background: rgb(243 244 246); color: rgb(156 163 175); }
        .dark .booking-button.is-past { background: rgb(31 41 55); color: rgb(107 114 128); }
        .booking-empty { padding: 3rem 1rem; border: 1px dashed rgb(209 213 219); border-radius: .85rem; color: rgb(107 114 128); text-align: center; }
        .booking-checkout { position: sticky; z-index: 20; bottom: 1rem; display: grid; gap: .8rem; padding: 1rem; border: 1px solid #f59e0b; border-radius: .9rem; background: rgb(255 255 255 / .97); box-shadow: 0 12px 30px rgb(0 0 0 / .18); backdrop-filter: blur(8px); }
        .dark .booking-checkout { background: rgb(17 24 39 / .97); }
        .booking-checkout-items { display: flex; gap: .5rem; overflow-x: auto; padding-bottom: .2rem; }
        .booking-checkout-item { display: flex; align-items: center; gap: .55rem; min-width: max-content; padding: .5rem .65rem; border: 1px solid rgb(229 231 235); border-radius: .55rem; color: rgb(55 65 81); font-size: .75rem; }
        .dark .booking-checkout-item { border-color: rgb(55 65 81); color: rgb(229 231 235); }
        .booking-checkout-remove { border: 0; background: transparent; color: #dc2626; cursor: pointer; font-size: 1rem; line-height: 1; }
        .booking-checkout-footer { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .booking-total-label { color: rgb(107 114 128); font-size: .75rem; }
        .booking-total-price { color: rgb(17 24 39); font-size: 1.4rem; font-weight: 800; }
        .dark .booking-total-price { color: white; }
        .booking-pay { min-width: 11rem; padding: .7rem 1rem; border: 0; border-radius: .6rem; background: #d97706; color: white; cursor: pointer; font-weight: 800; }
        .booking-pay:hover { background: #b45309; }
        .booking-pay:disabled { cursor: wait; opacity: .65; }
        [wire\:loading].booking-loading { opacity: .65; pointer-events: none; }
        @media (max-width: 800px) {
            .booking-days { grid-template-columns: 3.75rem repeat(7, minmax(6.25rem, 1fr)) 3.75rem; min-width: 55rem; }
            .booking-selected-heading { align-items: flex-start; flex-direction: column; gap: .2rem; }
            .booking-courts { grid-template-columns: repeat(var(--court-columns, 4), minmax(15rem, 1fr)); }
        }
    </style>

    <div class="booking-shell">
        <div class="booking-toolbar">
            <div class="booking-nav">
                <x-filament::button color="gray" wire:click="currentWeek">Today</x-filament::button>
            </div>
            <p class="booking-range">{{ $dates->first()->format('M j') }} – {{ $dates->last()->format('M j, Y') }}</p>
        </div>

        <div class="booking-week-scroll">
            <nav class="booking-days" aria-label="Select reservation date">
                <button type="button" class="booking-week-arrow" wire:click="previousWeek" aria-label="Previous week" title="Previous week">
                    <x-heroicon-m-chevron-left />
                </button>

                @foreach ($dates as $date)
                    @php
                        $fullDayReason = $fullDayClosures->get($date->toDateString());
                        $isFullDayClosure = filled($fullDayReason);
                    @endphp
                    <button
                        type="button"
                        wire:key="day-{{ $date->toDateString() }}"
                        wire:click="selectDate('{{ $date->toDateString() }}')"
                        @if ($date->toDateString() === $selectedDate) aria-current="date" @endif
                        @class([
                            'booking-day',
                            'is-selected' => $date->toDateString() === $selectedDate,
                            'is-maintenance' => $isFullDayClosure,
                        ])
                    >
                        <span class="booking-day-name">{{ $date->format('D') }}</span>
                        <span class="booking-day-number">{{ $date->format('j') }}</span>
                        <span class="booking-day-month">{{ $date->format('M') }}</span>
                        @if ($isFullDayClosure)
                            <span class="booking-day-maintenance">{{ $fullDayReason }}</span>
                        @endif
                    </button>
                @endforeach

                <button type="button" class="booking-week-arrow" wire:click="nextWeek" aria-label="Next week" title="Next week">
                    <x-heroicon-m-chevron-right />
                </button>
            </nav>
        </div>

        <div class="booking-selected-heading">
            <h2>{{ Carbon\CarbonImmutable::parse($selectedDate)->format('l, F j, Y') }}</h2>
            <span>Choose an available time to book instantly</span>
        </div>

        @if ($courts->isEmpty())
            <div class="booking-empty">No active, reservable courts are available.</div>
        @else
            <div class="booking-courts-scroll">
                <div
                    class="booking-courts booking-loading"
                    style="--court-columns: {{ min($courts->count(), 4) }}"
                    wire:loading.class="booking-loading"
                >
                    @foreach ($courts as $court)
                        <article wire:key="court-{{ $selectedDate }}-{{ $court->id }}" class="booking-court">
                            @if ($court->image)
                                <img src="{{ Storage::disk('public')->url($court->image) }}" alt="{{ $court->name }}" class="booking-court-image">
                            @else
                                <div class="booking-court-placeholder"><x-heroicon-o-photo style="width: 2.5rem; height: 2.5rem;" /></div>
                            @endif

                            <h3 class="booking-court-title">{{ $court->name }}</h3>

                            <div class="booking-slots">
                                @forelse ($this->timeSlotsFor($court, $selectedDate) as $timeSlot)
                                    @php
                                        $slotKey = $this->slotKey($court->id, $selectedDate, $timeSlot);
                                        $reservationStatus = $bookedSlots->get($slotKey);
                                        $isBooked = $reservationStatus === 'paid';
                                        $isPaymentPending = $reservationStatus === 'pending_payment';
                                        $isOccupied = $reservationStatus === 'occupied';
                                        $isCompleted = $reservationStatus === 'completed';
                                        $isNoShow = $reservationStatus === 'no_show';
                                        $hasReservationStatus = filled($reservationStatus);
                                        $isReserved = $isBooked || $isPaymentPending || $isOccupied;
                                        $closureReason = $disabledSlots->get($slotKey);
                                        $isDisabled = filled($closureReason);
                                        $isPast = $this->isPastSlot($selectedDate, $timeSlot);
                                        $hourlyRate = $court->hourlyRateFor($timeSlot);
                                        $isSelected = isset($selectedBookings[$slotKey]);
                                        $slotLabel = match (true) {
                                            $isBooked => 'Booked',
                                            $isPaymentPending => 'Processing Payment',
                                            $isOccupied => 'Occupied',
                                            $isCompleted => 'Completed',
                                            $isNoShow => 'No Show',
                                            $isDisabled => $closureReason,
                                            $isPast => 'Unavailable',
                                            $isSelected => 'Selected',
                                            default => 'Book · ₱'.number_format((float) $hourlyRate, 0),
                                        };
                                    @endphp

                                    <div wire:key="slot-{{ $slotKey }}" class="booking-slot">
                                        @php($slotStart = Carbon\CarbonImmutable::createFromFormat('H:i:s', $timeSlot))
                                        <time class="booking-time">{{ $slotStart->format('g:i A') }} - {{ $slotStart->addHour()->format('g:i A') }}</time>
                                        <button
                                            type="button"
                                            @disabled($hasReservationStatus || $isDisabled || $isPast)
                                            wire:click="book({{ $court->id }}, '{{ $selectedDate }}', '{{ $timeSlot }}')"
                                            @class([
                                                'booking-button',
                                                'is-booked' => $isBooked,
                                                'is-pending' => $isPaymentPending,
                                                'is-occupied' => $isOccupied,
                                                'is-completed' => $isCompleted,
                                                'is-no-show' => $isNoShow,
                                                'is-disabled' => $isDisabled && ! $hasReservationStatus,
                                                'is-past' => $isPast && ! $hasReservationStatus && ! $isDisabled,
                                                'is-selected' => $isSelected,
                                            ])
                                        >
                                            {{ $slotLabel }}
                                        </button>
                                    </div>
                                @empty
                                    <div class="booking-empty">Booking hours are not configured.</div>
                                @endforelse
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($selectedBookings !== [])
            <aside class="booking-checkout" aria-label="Booking payment summary">
                <div class="booking-checkout-items">
                    @foreach ($selectedBookings as $selectedSlotKey => $booking)
                        @php($selectedStart = Carbon\CarbonImmutable::createFromFormat('H:i:s', $booking['time_slot']))
                        <div wire:key="checkout-{{ $selectedSlotKey }}" class="booking-checkout-item">
                            <span>
                                <strong>{{ $booking['court_name'] }}</strong> ·
                                {{ Carbon\CarbonImmutable::parse($booking['date'])->format('M j') }} ·
                                {{ $selectedStart->format('g:i A') }}-{{ $selectedStart->addHour()->format('g:i A') }} ·
                                ₱{{ number_format((float) $booking['hourly_rate'], 0) }}
                            </span>
                            <button type="button" class="booking-checkout-remove" wire:click="removeSelectedBooking('{{ $selectedSlotKey }}')" aria-label="Remove selected booking">×</button>
                        </div>
                    @endforeach
                </div>

                <div class="booking-checkout-footer">
                    <div>
                        <div class="booking-total-label">Total payment · {{ count($selectedBookings) }} {{ count($selectedBookings) === 1 ? 'hour' : 'hours' }}</div>
                        <div class="booking-total-price">₱{{ number_format($this->totalPayment(), 2) }}</div>
                    </div>
                    <button type="button" class="booking-pay" wire:click="proceedToCheckout" wire:loading.attr="disabled" wire:target="proceedToCheckout">
                        <span wire:loading.remove wire:target="proceedToCheckout">Proceed to Payment</span>
                        <span wire:loading wire:target="proceedToCheckout">Opening checkout…</span>
                    </button>
                </div>
            </aside>
        @endif
    </div>
</x-filament-panels::page>
