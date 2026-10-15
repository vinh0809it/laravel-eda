<?php

declare(strict_types=1);

namespace Src\Booking\Application\Ports;

use Src\Booking\Application\DTOs\CarDetails;

/**
 * What Booking needs from the car catalogue, in Booking's own terms.
 * The adapter in Booking\Infrastructure talks to the Car module's contract.
 */
interface CarCatalog
{
    public function findCarById(string $carId): ?CarDetails;
}
