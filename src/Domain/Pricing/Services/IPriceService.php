<?php

namespace Src\Domain\Pricing\Services;

use Src\Application\Car\DTOs\CarProjectionDTO;
use Src\Application\Pricing\DTOs\AdditionalPriceCalculationDTO;

interface IPriceService
{
    public function calculateBookingPrice(CarProjectionDTO $car, string $startDate, string $endDate): float;
    public function calculateAdditionalPrice(AdditionalPriceCalculationDTO $additionalPriceCalculationDTO): float;
    public function calculateFinalPrice(float $bookingPrice, float $additionalPrice): float;
} 