<?php

namespace Src\Domain\Pricing\Calculators;

use Carbon\Carbon;
use Src\Domain\Pricing\Contracts\IPriceCalculator;
use Src\Domain\Pricing\ValueObjects\Price;

class SimplePriceCalculator implements IPriceCalculator
{
    public function calculateUsagePrice(Price $dailyPrice, Carbon $startDate, Carbon $endDate): float
    {
        $days = $startDate->diffInDays($endDate);
        return $dailyPrice->multiply($days);
    }
} 