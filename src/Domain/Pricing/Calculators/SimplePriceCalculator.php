<?php

namespace Src\Domain\Pricing\Calculators;

use Carbon\Carbon;
use Src\Domain\Pricing\Contracts\IPriceCalculator;
use Src\Domain\Pricing\ValueObjects\Price;

class SimplePriceCalculator implements IPriceCalculator
{
    /**
     * @param Price $dailyPrice
     * @param Carbon $startDate
     * @param Carbon $endDate
     * 
     * @return float
     */
    public function calculateUsagePrice(Price $dailyPrice, Carbon $startDate, Carbon $endDate): float
    {
        $days = $startDate->diffInDays($endDate);
        return $dailyPrice->multiply($days);
    }

    /**
     * @param Price $dailyPrice
     * @param int $bookedCount
     * 
     * @return float
     */
    public function calculatePopularityFee(Price $dailyPrice, int $bookedCount): float
    {
        return $dailyPrice->getAmount() * $this->getPopularityRate($bookedCount);
    }

    /**
     * @param int $bookedCount
     * 
     * @return float
     */
    private function getPopularityRate(int $bookedCount): float
    {
        $rawRate = $bookedCount / 100;

        // Floor to nearest 0.1 (e.g., 0.14 => 0.1, 0.29 => 0.2)
        return floor($rawRate * 10) / 10;
    }
}
