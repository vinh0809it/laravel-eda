<?php

declare(strict_types=1);

namespace Src\Domain\Car\Events;

use Src\Domain\Shared\Events\IDomainEvent;

final class PopularityFeeRecalculated implements IDomainEvent
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $carId,
        public readonly float $newPopularityFee
    ) {}

    public function getEventType(): string
    {
        return 'PopularityFeeRecalculated';
    }

    public function getAggregateType(): string
    {
        return 'Car';
    }

    public function getAggregateId(): string
    {
        return $this->carId;
    }

    public function toArray(): array
    {
        return [
            'booking_id' => $this->bookingId,
            'car_id' => $this->carId,
            'popularity_fee' => $this->newPopularityFee
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            bookingId: $data['booking_id'],
            carId: $data['car_id'],
            newPopularityFee: $data['popularity_fee']
        );
    }
}
