<?php

namespace Src\Domain\Pricing\Services;

use Src\Application\Car\DTOs\CarProjectionDTO;

interface IPriceService
{
    public function calculateBookingPrice(CarProjectionDTO $car, string $startDate, string $endDate): float;
} 