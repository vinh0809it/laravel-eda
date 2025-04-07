<?php

namespace Src\Domain\Booking\Events;

class BookingCompleted
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $completedBy,
        public readonly string $completionNotes,
    ) {}
} 