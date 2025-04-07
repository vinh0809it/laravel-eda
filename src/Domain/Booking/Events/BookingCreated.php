<?php

namespace Src\Domain\Booking\Events;

class BookingCreated
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $carId,
        public readonly string $userId,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly float $totalPrice,
    ) {}

    public function toArray(): array
    {
        return [
            'booking_id' => $this->bookingId,
            'car_id' => $this->carId,
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'total_price' => $this->totalPrice,
        ];
    }
} 