<?php

namespace Src\Domain\Booking\Aggregates;

use Carbon\Carbon;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Shared\Aggregate\AggregateRoot;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Booking\Events\BookingCanceled;
use Src\Domain\Booking\Events\BookingChanged;
use Src\Domain\Shared\Enums\HttpStatusCode;
use Src\Domain\Shared\Events\IDomainEvent;
use Src\Domain\Shared\Exceptions\BussinessException;

class BookingAggregate extends AggregateRoot
{
    public const AGGREGATE_TYPE = 'Booking';

    private string $id;
    private string $carId;
    private string $userId;
    private Carbon $startDate;
    private Carbon $endDate;
    private Carbon $actualEndDate;
    private float $originalPrice;
    private float $finalPrice;
    private string $completionNote;
    private Carbon $canceledAt;
    private string $cancelReason;
    private string $status;

    public static function create(
        string $id,
        string $carId,
        string $userId,
        Carbon $startDate,
        Carbon $endDate,
        float $originalPrice
    ): self {
        $booking = new self();

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

     public function change(Carbon $newStartDate, Carbon $newEndDate, float $newOriginalPrice): void
    {
        $this->assertNotTerminateState();

        $event = new BookingChanged(
            bookingId: $this->id,
            newStartDate: $newStartDate,
            newEndDate: $newEndDate,
            newOriginalPrice: $newOriginalPrice
        );

        $this->recordEvent($event);
        $this->apply($event);
    }

    public function complete(Carbon $actualEndDate, float $additionalPrice, float $finalPrice, string $completionNote): void
    {
        $this->assertNotTerminateState();

        $event = new BookingCompleted(
            bookingId: $this->id,
            carId: $this->carId,
            actualEndDate: $actualEndDate,
            additionalPrice: $additionalPrice,
            finalPrice: $finalPrice,
            completionNote: $completionNote
        );

        $this->recordEvent($event);
        $this->apply($event);
    }

    public function cancel(Carbon $canceledAt, string $cancelReason): void
    {
        $this->assertNotTerminateState();

        $event = new BookingCanceled(
            bookingId: $this->id,
            carId: $this->carId,
            canceledAt: $canceledAt,
            cancelReason: $cancelReason
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

        }else if ($event instanceof BookingChanged) {

            $this->startDate = $event->newStartDate;
            $this->endDate = $event->newEndDate;
            $this->originalPrice = $event->newOriginalPrice;
            $this->status = BookingStatus::CHANGED->value;

        }else if ($event instanceof BookingCompleted) {

            $this->actualEndDate = $event->actualEndDate;
            $this->finalPrice = $event->finalPrice;
            $this->completionNote = $event->completionNote;
            $this->status = BookingStatus::COMPLETED->value;

        }else if ($event instanceof BookingCanceled) {

            $this->canceledAt = $event->canceledAt;
            $this->cancelReason = $event->cancelReason;
            $this->status = BookingStatus::CANCELED->value;
        }

        $this->incrementVersion();
    }
    
    private function assertNotTerminateState(): void
    {
        if ($this->status === BookingStatus::CANCELED->value) {
            throw new BussinessException(
                message: 'Booking is already canceled!',
                code: HttpStatusCode::CONFLICT->value
            );
        }

        if ($this->status === BookingStatus::COMPLETED->value) {
            throw new BussinessException(
                message: 'Booking is already completed!',
                code: HttpStatusCode::CONFLICT->value
            );
        }
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

    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    public function getEndDate(): Carbon
    {
        return $this->endDate;
    }

    public function getActualEndDate(): Carbon
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

    public function getCanceledAt(): Carbon
    {
        return $this->canceledAt;
    }

    public function getCancelReason(): string
    {
        return $this->cancelReason;
    }

    public function getCompletionNote(): string
    {
        return $this->completionNote;
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