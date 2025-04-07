<?php

namespace Src\Domain\Pricing\Services;

use Src\Application\Car\DTOs\CarDTO;

interface IPriceService
{
    public function calculateBookingPrice(CarDTO $car, string $startDate, string $endDate): float;
} 