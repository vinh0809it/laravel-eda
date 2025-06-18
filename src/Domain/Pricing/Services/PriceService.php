<?php

namespace Src\Domain\Pricing\Services;

use Src\Domain\Pricing\Contracts\IPriceCalculator;
use Src\Domain\Pricing\ValueObjects\Price;
use Src\Application\Car\DTOs\CarProjectionDTO;
use Src\Application\Pricing\DTOs\AdditionalPriceCalculationDTO;

class PriceService implements IPriceService
{
    public function __construct(
        private readonly IPriceCalculator $priceCalculator,
    ) {}

    public function calculateBookingPrice(CarProjectionDTO $car, string $startDate, string $endDate): float
    {
        $dailyPrice = $car->pricePerDay;
        $price = new Price($dailyPrice);
        return $this->priceCalculator->calculateUsagePrice(
            $price, 
            $startDate, 
            $endDate
        );
    }

    public function calculateAdditionalPrice(AdditionalPriceCalculationDTO $additionalPriceCalculationDTO): float
    {
        $dailyPrice = $additionalPriceCalculationDTO->pricePerDay;
        $price = new Price($dailyPrice);
        return $this->priceCalculator->calculateUsagePrice(
            $price, 
            $additionalPriceCalculationDTO->endDate, 
            $additionalPriceCalculationDTO->actualEndDate
        );
    }

    public function calculateFinalPrice(float $bookingPrice, float $additionalPrice): float
    {
        return $bookingPrice + $additionalPrice;
    }
}