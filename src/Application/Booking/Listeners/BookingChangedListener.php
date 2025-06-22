<?php

namespace Src\Application\Booking\Listeners;

use Illuminate\Support\Facades\Log;
use Src\Domain\Booking\Events\BookingChanged;

class BookingChangedListener
{
    public function handle(BookingChanged $event): void
    {
        Log::info('Booking changed', [
            'booking_id' => $event->bookingId,
            'start_date' => $event->newStartDate,
            'end_date' => $event->newEndDate,
            'original_price' => $event->newOriginalPrice
        ]);

        // Here you can add more side effects:
        // - Send email confirmation
        // - Send notification
        // - Update analytics
        // - etc.
    }
} 