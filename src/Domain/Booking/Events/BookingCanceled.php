<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Events;

use Carbon\Carbon;
use Src\Domain\Shared\Events\IDomainEvent;

final class BookingCanceled implements IDomainEvent
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $carId,
        public readonly Carbon $canceledAt,
        public readonly string $cancelReason
    ) {}

    public function getEventType(): string
    {
        return 'BookingCanceled';
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
            'canceled_at' => $this->canceledAt,
            'cancel_reason' => $this->cancelReason
        ];
    }

    public static function fromArray(array $data): self
    {
        $canceledAt = Carbon::parse($data['canceled_at']);
        return new self(
            bookingId: $data['booking_id'],
            carId: $data['car_id'],
            canceledAt: $canceledAt,
            cancelReason: $data['cancel_reason']
        );
    }
} 