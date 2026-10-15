<?php

declare(strict_types=1);

namespace Src\Booking\Infrastructure\Adapters;

use Src\Booking\Application\DTOs\CarDetails;
use Src\Booking\Application\Ports\CarCatalog;
use Src\Car\Contracts\CarCatalog as CarModuleCatalog;

/** Anti-corruption layer between Booking and the Car module. */
final class CarCatalogAdapter implements CarCatalog
{
    public function __construct(
        private readonly CarModuleCatalog $cars
    ) {}

    public function findCarById(string $carId): ?CarDetails
    {
        $car = $this->cars->findCarById($carId);

        return $car ? new CarDetails(
            id: $car->id,
            pricePerDay: $car->pricePerDay,
            popularityFee: $car->popularityFee,
            bookedCount: $car->bookedCount,
        ) : null;
    }
}
