<?php

declare(strict_types=1);

namespace Src\Car\Contracts;

use Src\Car\Domain\Exceptions\CarNotFoundException;

/**
 * Public API of the Car module. Other modules may depend on this interface
 * and on CarSnapshot only; everything else in Src\Car is private.
 */
interface CarCatalog
{
    public function findCarById(string $carId): ?CarSnapshot;

    /** @throws CarNotFoundException */
    public function getDailyPrice(string $carId): float;
}
