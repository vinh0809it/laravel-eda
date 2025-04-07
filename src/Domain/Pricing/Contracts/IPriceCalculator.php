<?php

namespace Src\Domain\Pricing\Contracts;

use Src\Domain\Pricing\ValueObjects\Price;

interface IPriceCalculator
{
    public function calculateUsagePrice(Price $dailyPrice, string $startDate, string $endDate): float;
} 