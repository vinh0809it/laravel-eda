<?php

declare(strict_types=1);

namespace Src\Booking\Domain\Events;

use Carbon\Carbon;
use Src\Shared\Domain\Events\DomainEvent;

final class BookingChanged implements DomainEvent
{
    public function __construct(
        public readonly string $bookingId,
        public readonly Carbon $newStartDate,
        public readonly Carbon $newEndDate,
        public readonly float $newOriginalPrice
    ) {}

    public function getEventType(): string
    {
        return 'BookingChanged';
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
            'start_date' => $this->newStartDate->toDateString(),
            'end_date' => $this->newEndDate->toDateString(),
            'original_price' => $this->newOriginalPrice
        ];
    }

    public static function fromArray(array $data): self
    {
        $newStartDate = Carbon::parse($data['start_date']);
        $newEndDate = Carbon::parse($data['end_date']);

        return new self(
            bookingId: $data['booking_id'],
            newStartDate: $newStartDate,
            newEndDate: $newEndDate,
            newOriginalPrice: $data['original_price'],
        );
    }
} 