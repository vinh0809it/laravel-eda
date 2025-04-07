<?php

namespace Src\Domain\Pricing\Services;

use Src\Domain\Pricing\Contracts\IPriceCalculator;
use Src\Domain\Pricing\ValueObjects\Price;
use Src\Application\Car\DTOs\CarDTO;

class PriceService implements IPriceService
{
    public function __construct(
        private readonly IPriceCalculator $priceCalculator,
    ) {}

    public function calculateBookingPrice(CarDTO $car, string $startDate, string $endDate): float
    {
        $dailyPrice = $car->pricePerDay;
        $price = new Price($dailyPrice);
        return $this->priceCalculator->calculateUsagePrice($price, $startDate, $endDate);
    }
}   