<?php

declare(strict_types=1);

namespace Src\Booking\Application\Ports;

use Carbon\Carbon;

/** What Booking needs from pricing. Adapter: Booking\Infrastructure\Adapters\PricingAdapter. */
interface BookingPricing
{
    public function calculateBookingPrice(float $dailyPrice, float $popularityFee, Carbon $startDate, Carbon $endDate): float;

    public function calculateAdditionalPrice(float $pricePerDay, Carbon $endDate, Carbon $actualEndDate): float;

    public function calculateFinalPrice(float $bookingPrice, float $additionalPrice): float;
}
