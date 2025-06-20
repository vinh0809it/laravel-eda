<?php

namespace Src\Application\Booking\Listeners;

use Illuminate\Support\Facades\Log;
use Src\Domain\Booking\Events\BookingCompleted;

class BookingCompletedListener
{
    public function handle(BookingCompleted $event): void
    {
        Log::info('Booking completed', [
            'booking_id' => $event->bookingId,
            'car_id' => $event->carId,
            'actual_end_date' => $event->actualEndDate,
            'additional_price' => $event->additionalPrice,
            'final_price' => $event->finalPrice
        ]);

        // Here you can add more side effects:
        // - Send email confirmation
        // - Send notification
        // - Update analytics
        // - etc.
    }
} 