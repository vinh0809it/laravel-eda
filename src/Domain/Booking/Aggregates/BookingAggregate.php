<?php

namespace Src\Domain\Booking\Aggregates;

use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Shared\Aggregate\AggregateRoot;

class BookingAggregate extends AggregateRoot
{
    public string $aggregateType = 'Booking';

    public function __construct(
        private readonly string $id,
        private readonly string $carId,
        private readonly string $userId,
        private readonly string $startDate,
        private readonly string $endDate,
        private readonly float $totalPrice,
        private readonly string $status,
    ) {}

    public static function create(
        string $id,
        string $carId,
        string $userId,
        string $startDate,
        string $endDate,
        float $totalPrice
    ): self {
        $booking = new self(
            id: $id,
            carId: $carId,
            userId: $userId,
            startDate: $startDate,
            endDate: $endDate,
            totalPrice: $totalPrice,
            status: 'created',
        );

        // Create event
        $event = new BookingCreated(
            bookingId: $id,
            carId: $carId,
            userId: $userId,
            startDate: $startDate,
            endDate: $endDate,
            totalPrice: $totalPrice
        );

        $booking->recordEvent($event);

        $booking->incrementVersion(); 
        return $booking;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCarId(): string
    {
        return $this->carId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'car_id' => $this->carId,
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'total_price' => $this->totalPrice,
            'status' => $this->status,
            'version' => $this->version,
        ];
    }

    public function toEventData(): array
    {
        return [
            'id' => $this->id,
            'car_id' => $this->carId,
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'total_price' => $this->totalPrice,
        ];
    }
} 