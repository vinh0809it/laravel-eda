<?php

namespace Src\Application\Booking\Listeners;

use Src\Domain\Booking\Events\BookingCreated;
use Illuminate\Support\Facades\Log;

class BookingCreatedListener
{
    public function handle(BookingCreated $event): void
    {
        Log::info('Booking created', [
            'booking_id' => $event->bookingId,
            'user_id' => $event->userId,
            'car_id' => $event->carId,
            'original_price' => $event->originalPrice,
        ]);

        // Here you can add more side effects:
        // - Send email confirmation
        // - Send notification
        // - Update analytics
        // - etc.
    }
} 