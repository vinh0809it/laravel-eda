<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Events;

use Src\Domain\Shared\Events\IDomainEvent;

class BookingCreated implements IDomainEvent
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $carId,
        public readonly string $userId,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly float $originalPrice
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
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'original_price' => $this->originalPrice
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            bookingId: $data['booking_id'],
            carId: $data['car_id'],
            userId: $data['user_id'],
            startDate: $data['start_date'],
            endDate: $data['end_date'],
            originalPrice: $data['original_price']
        );
    }
} 