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
        return [
            'id' => $this->booking->getId(),
            'car_id' => $this->booking->getCarId(),
            'user_id' => $this->booking->getUserId(),
            'start_date' => $this->booking->getStartDate(),
            'end_date' => $this->booking->getEndDate(),
            'original_price' => $this->booking->getOriginalPrice(),
            'status' => $this->booking->getStatus()
        ];
    }

    public function forCompletion(): array
    {
        return [
            'id' => $this->booking->getId(),
            'car_id' => $this->booking->getCarId(),
            'user_id' => $this->booking->getUserId(),
            'start_date' => $this->booking->getStartDate(),
            'end_date' => $this->booking->getEndDate(),
            'actual_end_date' => $this->booking->getActualEndDate(),
            'original_price' => $this->booking->getOriginalPrice(),
            'final_price' => $this->booking->getFinalPrice(),
            'status' => $this->booking->getStatus()
        ];
    }
}