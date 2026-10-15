<?php

declare(strict_types=1);

namespace Src\Car\Application\Services;

use Src\Car\Application\Ports\CarReadRepository;
use Src\Car\Contracts\CarSnapshot;
use Src\Car\Contracts\CarCatalog;
use Src\Car\Domain\Exceptions\CarNotFoundException;

final class ReadModelCarCatalog implements CarCatalog
{
    public function __construct(
        private readonly CarReadRepository $cars
    ) {}

    public function findCarById(string $carId): ?CarSnapshot
    {
        return $this->cars->findCarById($carId);
    }

    public function getDailyPrice(string $carId): float
    {
        $car = $this->cars->findCarById($carId)
            ?? throw new CarNotFoundException(trace: ['carId' => $carId, 'when' => 'getDailyPrice']);

        return $car->pricePerDay;
    }
}
