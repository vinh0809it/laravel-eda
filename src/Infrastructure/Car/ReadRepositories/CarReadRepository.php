<?php

namespace Src\Infrastructure\Car\ReadRepositories;

use Src\Infrastructure\Shared\Repositories\BaseRepository;
use Src\Domain\Car\ReadRepositories\ICarReadRepository;
use Src\Domain\Car\Snapshots\CarSnapshot;
use Src\Infrastructure\Car\Models\Car;

class CarReadRepository extends BaseRepository implements ICarReadRepository
{
    public function __construct(
        Car $model
    ) {
        parent::__construct($model);
    }

    /**
     * @param string $carId
     * 
     * @return CarSnapshot|null
     */
    public function findCarById(string $carId): ?CarSnapshot
    {
        $car = $this->findById($carId);

        if (!$car) {
            return null;
        }

        return CarSnapshot::fromArray($car->toArray());
    }
}
