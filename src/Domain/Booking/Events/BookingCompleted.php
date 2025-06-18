<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Events;

use Src\Domain\Shared\Events\IDomainEvent;

final class BookingCompleted implements IDomainEvent
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $carId,
        public readonly string $actualEndDate,
        public readonly float $additionalPrice,
        public readonly float $finalPrice,
    ) {}

    public function getEventType(): string
    {
        return 'BookingCreated';
    }

    public function getAggregateType(): string
    {
        return 'Booking';
    }

    public function getAggregateId(): string
    {
        return $this->bookingId;
    }

    public function toArray(): array
    {
        return [
            'booking_id' => $this->bookingId,
            'car_id' => $this->carId,
            'actual_end_date' => $this->actualEndDate,
            'additional_price' => $this->additionalPrice,
            'final_price' => $this->finalPrice
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            bookingId: $data['booking_id'],
            carId: $data['car_id'],
            actualEndDate: $data['actual_end_date'],
            additionalPrice: $data['additional_price'],
            finalPrice: $data['final_price']
        );
    }
} 