<?php

namespace Src\Domain\Booking\Events;

class BookingCancelled
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $reason,
        public readonly string $cancelledBy,
    ) {}
} 