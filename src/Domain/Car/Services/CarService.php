<?php

namespace Src\Domain\Car\Services;

use Src\Domain\Car\Projections\ICarProjection;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Car\Aggregate\CarAggregate;

class CarService implements ICarService
{
    public function __construct(
        private readonly ICarProjection $carProjection
    ) {}

    public function findCarById(string $carId): CarAggregate
    {
        $car = $this->carProjection->findById($carId);
        if (!$car) {
            throw new CarNotFoundException(
                trace: ['carId' => $carId]
            );
        }

        return CarAggregate::create(
            id: $car->id,
            brand: $car->brand,
            model: $car->model,
            year: $car->year,
            pricePerDay: $car->price_per_day,
            isAvailable: $car->is_available
        );
    }

    public function isAvailable(string $carId): bool
    {
        $car = $this->carProjection->findById($carId);
        if (!$car) {
            throw new CarNotFoundException(
                trace: ['carId' => $carId]
            );
        }

        return $car->is_available;
    }   

    public function isCarExists(string $carId): bool
    {
        return $this->carProjection->findById($carId) !== null;
    }

    public function getDailyPrice(string $carId): float
    {
        $car = $this->carProjection->findById($carId);
        if (!$car) {
            throw new CarNotFoundException("Car not found with ID: {$carId}");
        }

        return $car->price_per_day;
    }
} 