<?php

namespace Src\Domain\Car\Services;

use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Car\ReadRepositories\ICarReadRepository;
use Src\Domain\Car\Snapshots\CarSnapshot;

class CarService implements ICarService
{
    public function __construct(
        private readonly ICarReadRepository $carReadRepository
    ) {}

    /**
     * @param string $carId
     * 
     * @return CarSnapshot|null
     */
    public function findCarById(string $carId): ?CarSnapshot
    {
        return $this->carReadRepository->findCarById($carId);
    }

    /**
     * @param string $carId
     * 
     * @return float
     */
    public function getDailyPrice(string $carId): float
    {
        $car = $this->findCarById($carId);

        if(!$car) {
            throw new CarNotFoundException(trace: [
                    'carId' => $carId,
                    'when' => 'getDailyPrice'
            ]);
        }

        return $this->findCarById($carId)?->pricePerDay;
    }
}
