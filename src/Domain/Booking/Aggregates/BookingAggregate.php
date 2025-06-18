<?php

namespace Src\Domain\Booking\Aggregates;

use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Shared\Aggregate\AggregateRoot;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Shared\Events\IDomainEvent;
use Src\Domain\Shared\Exceptions\BussinessException;

class BookingAggregate extends AggregateRoot
{
    public const AGGREGATE_TYPE = 'Booking';

    private string $id;
    private string $carId;
    private string $userId;
    private string $startDate;
    private string $endDate;
    private string $actualEndDate;
    private float $originalPrice;
    private float $finalPrice;
    private string $status;

    public static function create(
        string $id,
        string $carId,
        string $userId,
        string $startDate,
        string $endDate,
        float $originalPrice
    ): self {
        $booking = new self();

        // Create event
        $event = new BookingCreated(
            bookingId: $id,
            carId: $carId,
            userId: $userId,
            startDate: $startDate,
            endDate: $endDate,
            originalPrice: $originalPrice
        );

        $booking->recordEvent($event);
        $booking->apply($event);

        return $booking;
    }

    public function complete(string $actualEndDate, float $additionalPrice, float $finalPrice): void
    {
        if ($this->status === BookingStatus::COMPLETED->value) {
            throw new BussinessException(
                message: 'Booking is already completed!',
                code: 409
            );
        }

        $event = new BookingCompleted(
            bookingId: $this->id,
            carId: $this->carId,
            actualEndDate: $actualEndDate,
            additionalPrice: $additionalPrice,
            finalPrice: $finalPrice
        );

        $this->recordEvent($event);
        $this->apply($event);
    }

    public static function replayEvents(array $events): self
    {
        $aggregate = new self();

        foreach ($events as $event) {
            $aggregate->apply($event);
        }

        return $aggregate;
    }

    public function apply(IDomainEvent $event): void
    {   
        if ($event instanceof BookingCreated) {
            $this->id = $event->bookingId;
            $this->carId = $event->carId;
            $this->userId = $event->userId;
            $this->startDate = $event->startDate;
            $this->endDate = $event->endDate;
            $this->originalPrice = $event->originalPrice;
            $this->status = BookingStatus::CREATED->value;

        }else if ($event instanceof BookingCompleted) {
            $this->actualEndDate = $event->actualEndDate;
            $this->finalPrice = $event->finalPrice;
            $this->status = BookingStatus::COMPLETED->value;

        }

        $this->incrementVersion();
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

    public function getActualEndDate(): string
    {
        return $this->actualEndDate;
    }

    public function getOriginalPrice(): float
    {
        return $this->originalPrice;
    }

    public function getFinalPrice(): float
    {
        return $this->finalPrice;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getAggregateType(): string
    {
        return self::AGGREGATE_TYPE;
    }
} 