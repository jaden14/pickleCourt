<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirmPayment')
                ->label('Confirm paid')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription('This will mark the payment and every linked court reservation as paid.')
                ->visible(fn (): bool => $this->getRecord()->status === 'submitted')
                ->action(fn (Payment $record) => $record->markAsPaid())
                ->successNotificationTitle('Payment and all reservations marked as paid.'),
            Action::make('cancelBooking')
                ->label('Cancel booking')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancel this booking?')
                ->modalDescription('This will cancel the payment and every linked court reservation. The court hours will become available again.')
                ->modalSubmitActionLabel('Yes, cancel booking')
                ->visible(fn (): bool => $this->getRecord()->status === 'paid' && $this->getRecord()->reservations()->where('status', 'paid')->exists())
                ->disabled(fn (): bool => ! $this->getRecord()->canBeCancelled())
                ->tooltip(fn (): ?string => $this->getRecord()->canBeCancelled()
                    ? null
                    : 'Cancellation is not allowed within 24 hours of the earliest booked time.')
                ->action(fn (Payment $record) => $record->cancelBooking())
                ->successNotificationTitle('Booking cancelled and court hours released.'),
            Action::make('checkInAll')
                ->label('Check in all')
                ->icon(Heroicon::OutlinedUserPlus)
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Check in all booked courts?')
                ->modalDescription('Every paid court and hour linked to this payment will be marked as occupied.')
                ->modalSubmitActionLabel('Yes, check in all')
                ->visible(fn (): bool => $this->getRecord()->status === 'paid' && $this->getRecord()->reservations()->where('status', 'paid')->exists())
                ->action(fn (Payment $record) => $record->checkInAllReservations())
                ->successNotificationTitle('All linked reservations marked as occupied.'),
            Action::make('markAllNoShow')
                ->label('No show all')
                ->icon(Heroicon::OutlinedUserMinus)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Mark all as no show?')
                ->modalDescription('Every still-booked reservation linked to this payment will be marked as no show.')
                ->modalSubmitActionLabel('Yes, mark no show')
                ->visible(fn (): bool => $this->getRecord()->status === 'paid' && $this->getRecord()->reservations()->where('status', 'paid')->exists())
                ->disabled(fn (): bool => ! $this->getRecord()->canMarkAllAsNoShow())
                ->tooltip(fn (): ?string => $this->getRecord()->canMarkAllAsNoShow()
                    ? null
                    : 'No show can be marked once the first booked hour begins.')
                ->action(fn (Payment $record) => $record->markAllReservationsAsNoShow())
                ->successNotificationTitle('All linked reservations marked as no show.'),
            Action::make('completeAll')
                ->label('Complete all')
                ->icon(Heroicon::OutlinedCheckBadge)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Complete all occupied courts?')
                ->modalDescription('Every occupied reservation linked to this payment will be marked as completed.')
                ->modalSubmitActionLabel('Yes, complete all')
                ->visible(fn (): bool => $this->getRecord()->status === 'paid' && $this->getRecord()->reservations()->where('status', 'occupied')->exists())
                ->action(fn (Payment $record) => $record->completeAllReservations())
                ->successNotificationTitle('All occupied reservations marked as completed.'),
            PaymentResource::rescheduleAction(),
        ];
    }
}
