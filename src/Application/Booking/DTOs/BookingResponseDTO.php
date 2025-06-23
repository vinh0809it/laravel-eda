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
            'start_date' => $this->booking->getStartDate()->toDateString(),
            'end_date' => $this->booking->getEndDate()->toDateString(),
            'original_price' => $this->booking->getOriginalPrice(),
            'status' => $this->booking->getStatus()
        ];
    }

    public function forChanging(): array
    {
        return [
            'id' => $this->booking->getId(),
            'car_id' => $this->booking->getCarId(),
            'user_id' => $this->booking->getUserId(),
            'start_date' => $this->booking->getStartDate()->toDateString(),
            'end_date' => $this->booking->getEndDate()->toDateString(),
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
            'start_date' => $this->booking->getStartDate()->toDateString(),
            'end_date' => $this->booking->getEndDate()->toDateString(),
            'actual_end_date' => $this->booking->getActualEndDate()->toDateString(),
            'original_price' => $this->booking->getOriginalPrice(),
            'final_price' => $this->booking->getFinalPrice(),
            'completion_note' => $this->booking->getCompletionNote(),
            'status' => $this->booking->getStatus()
        ];
    }

    public function forCancelation(): array
    {
        return [
            'id' => $this->booking->getId(),
            'car_id' => $this->booking->getCarId(),
            'user_id' => $this->booking->getUserId(),
            'start_date' => $this->booking->getStartDate()->toDateString(),
            'end_date' => $this->booking->getEndDate()->toDateString(),
            'original_price' => $this->booking->getOriginalPrice(),
            'canceled_at' => $this->booking->getCanceledAt()->toDateTimeString(),
            'cancel_reason' => $this->booking->getCancelReason(),
            'status' => $this->booking->getStatus()
        ];
    }
}