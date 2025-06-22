<?php

namespace Src\Domain\Car\Services;

use Src\Domain\Car\Projections\ICarProjection;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Car\Snapshots\CarSnapshot;

class CarService implements ICarService
{
    public function __construct(
        private readonly ICarProjection $carProjection
    ) {}

    public function findCarById(string $carId): ?CarSnapshot
    {
        $car = $this->carProjection->findById($carId);
        if (!$car) {
            return null;
        }

        return CarSnapshot::fromArray($car->toArray());
    }

    public function getDailyPrice(string $carId): float
    {
        $car = $this->carProjection->findById($carId);
        if (!$car) {
            throw new CarNotFoundException(
                trace: ['carId' => $carId]
            );
        }

        return $car->price_per_day;
    }
} 