<?php

namespace Src\Domain\Pricing\Services;

use Carbon\Carbon;
use Src\Application\Pricing\DTOs\AdditionalPriceCalculationDTO;

interface IPriceService
{
    public function calculateBookingPrice(float $dailyPrice, float $popularityFee, Carbon $startDate, Carbon $endDate): float;
    public function calculateAdditionalPrice(AdditionalPriceCalculationDTO $additionalPriceCalculationDTO): float;
    public function calculateFinalPrice(float $bookingPrice, float $additionalPrice): float;
    public function calculatePopularityFee(float $dailyPrice, int $bookedCount): float;
}
