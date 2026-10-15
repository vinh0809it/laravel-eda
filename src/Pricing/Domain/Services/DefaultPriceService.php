<?php

namespace Src\Pricing\Domain\Services;

use Carbon\Carbon;
use Src\Pricing\Domain\Services\PriceCalculator;
use Src\Pricing\Domain\ValueObjects\Price;
use Src\Pricing\Contracts\PriceService;

class DefaultPriceService implements PriceService
{
    public function __construct(
        private readonly PriceCalculator $priceCalculator,
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

    public function calculateAdditionalPrice(float $pricePerDay, Carbon $endDate, Carbon $actualEndDate): float
    {
        return $this->priceCalculator->calculateUsagePrice(new Price($pricePerDay), $endDate, $actualEndDate);
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
