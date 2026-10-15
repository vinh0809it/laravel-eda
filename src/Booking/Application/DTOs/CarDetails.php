<?php

declare(strict_types=1);

namespace Src\Booking\Application\DTOs;

/** The slice of a car that Booking needs. Owned by Booking, filled by the Car adapter. */
final readonly class CarDetails
{
    public function __construct(
        public string $id,
        public float $pricePerDay,
        public float $popularityFee,
        public int $bookedCount,
    ) {}
}
