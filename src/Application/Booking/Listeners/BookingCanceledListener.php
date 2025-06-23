<?php

namespace Src\Application\Booking\Listeners;

use Illuminate\Support\Facades\Log;
use Src\Domain\Booking\Events\BookingCanceled;

class BookingCanceledListener
{
    public function handle(BookingCanceled $event): void
    {
        Log::info('Booking completed', [
            'booking_id' => $event->bookingId,
            'car_id' => $event->carId,
            'canceled_at' => $event->canceledAt,
            'cancel_reason' => $event->cancelReason
        ]);

        // Here you can add more side effects:
        // - Send email confirmation
        // - Send notification
        // - Update analytics
        // - etc.
    }
} 