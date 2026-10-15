<?php

declare(strict_types=1);

namespace Src\Pricing\Contracts;

use Carbon\Carbon;

/** Public API of the Pricing module. */
interface PriceService
{
    public function calculateBookingPrice(float $dailyPrice, float $popularityFee, Carbon $startDate, Carbon $endDate): float;

    public function calculateAdditionalPrice(float $pricePerDay, Carbon $endDate, Carbon $actualEndDate): float;

    public function calculateFinalPrice(float $bookingPrice, float $additionalPrice): float;

    public function calculatePopularityFee(float $dailyPrice, int $bookedCount): float;
}
