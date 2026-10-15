<?php

declare(strict_types=1);

namespace Src\Booking\Infrastructure\Adapters;

use Carbon\Carbon;
use Src\Booking\Application\Ports\BookingPricing;
use Src\Pricing\Contracts\PriceService;

/** Anti-corruption layer between Booking and the Pricing module. */
final class PricingAdapter implements BookingPricing
{
    public function __construct(
        private readonly PriceService $pricing
    ) {}

    public function calculateBookingPrice(float $dailyPrice, float $popularityFee, Carbon $startDate, Carbon $endDate): float
    {
        return $this->pricing->calculateBookingPrice($dailyPrice, $popularityFee, $startDate, $endDate);
    }

    public function calculateAdditionalPrice(float $pricePerDay, Carbon $endDate, Carbon $actualEndDate): float
    {
        return $this->pricing->calculateAdditionalPrice($pricePerDay, $endDate, $actualEndDate);
    }

    public function calculateFinalPrice(float $bookingPrice, float $additionalPrice): float
    {
        return $this->pricing->calculateFinalPrice($bookingPrice, $additionalPrice);
    }
}
