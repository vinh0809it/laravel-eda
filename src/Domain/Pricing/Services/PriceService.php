<?php

namespace Src\Domain\Pricing\Services;

use Carbon\Carbon;
use Src\Domain\Pricing\Contracts\IPriceCalculator;
use Src\Domain\Pricing\ValueObjects\Price;
use Src\Application\Pricing\DTOs\AdditionalPriceCalculationDTO;

class PriceService implements IPriceService
{
    public function __construct(
        private readonly IPriceCalculator $priceCalculator,
    ) {}

    /**
     * @param float $dailyPrice
     * @param float $popularityFee
     * @param Carbon $startDate
     * @param Carbon $endDate
     * 
     * @return float
     */
    public function calculateBookingPrice(float $dailyPrice, float $popularityFee, Carbon $startDate, Carbon $endDate): float
    {
        $price = new Price($dailyPrice);

        $bookingUsagePrice = $this->priceCalculator->calculateUsagePrice(
            $price, 
            $startDate, 
            $endDate
        );

        return $bookingUsagePrice + $popularityFee;
    }

    /**
     * @param AdditionalPriceCalculationDTO $additionalPriceCalculationDTO
     * 
     * @return float
     */
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

    /**
     * @param float $bookingPrice
     * @param float $additionalPrice
     * 
     * @return float
     */
    public function calculateFinalPrice(float $bookingPrice, float $additionalPrice): float
    {
        return $bookingPrice + $additionalPrice;
    }

    /**
     * @param float $dailyPrice
     * @param int $bookedCount
     * 
     * @return float
     */
    public function calculatePopularityFee(float $dailyPrice, int $bookedCount): float
    {
        $price = new Price($dailyPrice);

        return $this->priceCalculator->calculatePopularityFee(
            $price, 
            $bookedCount
        );
    }
}
