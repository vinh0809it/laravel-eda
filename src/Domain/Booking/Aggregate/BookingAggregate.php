<?php

namespace Src\Domain\Booking\Aggregate;

use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Booking\Events\BookingCancelled;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Infrastructure\EventStore\Repositories\EventStoreRepository;
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
            totalPrice: $totalPrice,
            status: 'created'
        );

        $booking->recordEvent($event);

        $booking->incrementVersion(); 
        return $booking;
    }

    public function cancel(string $reason, string $cancelledBy, EventStoreRepository $eventStore): void
    {
        if ($this->status === 'cancelled') {
            throw new \RuntimeException('Booking is already cancelled');
        }

        if ($this->status === 'completed') {
            throw new \RuntimeException('Cannot cancel a completed booking');
        }

        $event = new BookingCancelled(
            bookingId: $this->id,
            reason: $reason,
            cancelledBy: $cancelledBy,
        );

        $this->status = 'cancelled';
        $this->version++;

        $eventStore->append(
            eventType: 'BookingCancelled',
            aggregateType: 'Booking',
            aggregateId: $this->id,
            eventData: [
                'reason' => $reason,
                'cancelled_by' => $cancelledBy,
            ],
            metadata: [
                'user_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
            version: $this->version
        );
    }

    public function complete(string $completedBy, string $completionNotes, EventStoreRepository $eventStore): void
    {
        if ($this->status === 'completed') {
            throw new \RuntimeException('Booking is already completed');
        }

        if ($this->status === 'cancelled') {
            throw new \RuntimeException('Cannot complete a cancelled booking');
        }

        $event = new BookingCompleted(
            bookingId: $this->id,
            completedBy: $completedBy,
            completionNotes: $completionNotes,
        );

        $this->status = 'completed';
        $this->version++;

        $eventStore->append(
            eventType: 'BookingCompleted',
            aggregateType: 'Booking',
            aggregateId: $this->id,
            eventData: [
                'completed_by' => $completedBy,
                'completion_notes' => $completionNotes,
            ],
            metadata: [
                'user_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
            version: $this->version
        );
    }

    public static function load(string $id, EventStoreRepository $eventStore): self
    {
        $events = $eventStore->getEvents('Booking', $id);
        $latestEvent = end($events);

        if (!$latestEvent) {
            throw new \RuntimeException("Booking not found: {$id}");
        }

        $booking = new self(
            id: $latestEvent['aggregate_id'],
            carId: $latestEvent['event_data']['car_id'],
            userId: $latestEvent['event_data']['user_id'],
            startDate: $latestEvent['event_data']['start_date'],
            endDate: $latestEvent['event_data']['end_date'],
            totalPrice: $latestEvent['event_data']['total_price'],
            status: $latestEvent['event_data']['status'],
        );

        // Replay all events to rebuild state
        foreach ($events as $event) {
            switch ($event['event_type']) {
                case 'BookingCancelled':
                    $booking->status = 'cancelled';
                    break;
                case 'BookingCompleted':
                    $booking->status = 'completed';
                    break;
            }
            $booking->version = $event['version'];
        }

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