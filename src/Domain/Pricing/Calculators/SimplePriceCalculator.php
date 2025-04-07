<?php

namespace Src\Domain\Pricing\Calculators;

use Src\Domain\Pricing\Contracts\IPriceCalculator;
use Src\Domain\Pricing\ValueObjects\Price;

class SimplePriceCalculator implements IPriceCalculator
{
    public function calculateUsagePrice(Price $dailyPrice, string $startDate, string $endDate): float
    {
        $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
        return $dailyPrice->multiply($days);
    }
} 