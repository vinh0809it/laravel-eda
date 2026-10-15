<?php

namespace Src\Pricing\Domain\Services;

use Carbon\Carbon;
use Src\Pricing\Domain\ValueObjects\Price;

interface PriceCalculator
{
    public function calculateUsagePrice(Price $dailyPrice, Carbon $startDate, Carbon $endDate): float;
    public function calculatePopularityFee(Price $dailyPrice, int $bookedCount): float;
}
