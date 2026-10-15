<?php

declare(strict_types=1);

namespace Src\Car\Application\Ports;

/**
 * What Car needs from pricing, expressed in Car's own terms.
 * The adapter in Car\Infrastructure translates to the Pricing module's contract.
 */
interface PopularityFeeCalculator
{
    public function calculatePopularityFee(float $dailyPrice, int $bookedCount): float;
}
