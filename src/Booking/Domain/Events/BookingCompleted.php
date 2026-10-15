<?php

declare(strict_types=1);

namespace Src\Booking\Domain\Events;

use Carbon\Carbon;
use Src\Shared\Domain\Events\DomainEvent;

final class BookingCompleted implements DomainEvent
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $carId,
        public readonly Carbon $actualEndDate,
        public readonly float $additionalPrice,
        public readonly float $finalPrice,
        public readonly string $completionNote
    ) {}

    public function getEventType(): string
    {
        return 'BookingCompleted';
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
            'actual_end_date' => $this->actualEndDate->toDateString(),
            'additional_price' => $this->additionalPrice,
            'final_price' => $this->finalPrice,
            'completion_note' => $this->completionNote
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            bookingId: $data['booking_id'],
            carId: $data['car_id'],
            actualEndDate: Carbon::parse($data['actual_end_date']),
            additionalPrice: $data['additional_price'],
            finalPrice: $data['final_price'],
            completionNote: $data['completion_note']
        );
    }
} 