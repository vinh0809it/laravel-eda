<?php

declare(strict_types=1);

namespace Src\Car\Infrastructure\Adapters;

use Src\Car\Application\Ports\PopularityFeeCalculator;
use Src\Pricing\Contracts\PriceService;

/** Anti-corruption layer between Car and the Pricing module. */
final class PricingAdapter implements PopularityFeeCalculator
{
    public function __construct(
        private readonly PriceService $pricing
    ) {}

    public function calculatePopularityFee(float $dailyPrice, int $bookedCount): float
    {
        return $this->pricing->calculatePopularityFee($dailyPrice, $bookedCount);
    }
}
