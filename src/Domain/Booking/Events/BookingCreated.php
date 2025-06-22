<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Events;

use Carbon\Carbon;
use Src\Domain\Shared\Events\IDomainEvent;

class BookingCreated implements IDomainEvent
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $carId,
        public readonly string $userId,
        public readonly Carbon $startDate,
        public readonly Carbon $endDate,
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
            'start_date' => $this->startDate->toDateString(),
            'end_date' => $this->endDate->toDateString(),
            'original_price' => $this->originalPrice
        ];
    }

    public static function fromArray(array $data): self
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        return new self(
            bookingId: $data['booking_id'],
            carId: $data['car_id'],
            userId: $data['user_id'],
            startDate: $startDate,
            endDate: $endDate,
            originalPrice: $data['original_price']
        );
    }
} 