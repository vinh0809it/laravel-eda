<?php

namespace Src\Domain\Pricing\Contracts;

use Carbon\Carbon;
use Src\Domain\Pricing\ValueObjects\Price;

interface IPriceCalculator
{
    public function calculateUsagePrice(Price $dailyPrice, Carbon $startDate, Carbon $endDate): float;
} 