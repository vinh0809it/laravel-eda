<?php

declare(strict_types=1);

namespace Src\Application\Pricing\DTOs;

use Carbon\Carbon;

final class AdditionalPriceCalculationDTO
{
    public function __construct(
        public readonly float $pricePerDay,
        public readonly Carbon $endDate,
        public readonly Carbon $actualEndDate
    ) {
    }
}