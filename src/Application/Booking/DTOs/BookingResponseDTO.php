<?php

namespace Src\Application\Booking\DTOs;

use Src\Domain\Booking\Aggregates\BookingAggregate;

class BookingResponseDTO
{
    public function __construct(
        protected readonly BookingAggregate $booking
    ) {}

    public function forCreation(): array
    {
        return $this->booking->toArray();
    }

    public function forUpdate(): array
    {
        return $this->booking->toArray();
    }
    
    public function forCancellation(): array
    {
        return $this->booking->toArray();
    }

    public function forCompletion(): array
    {
        return $this->booking->toArray();
    }
}