<?php

declare(strict_types=1);

namespace Src\Domain\Car\ReadRepositories;

use Src\Domain\Car\Snapshots\CarSnapshot;
use Src\Domain\Shared\Repositories\IBaseRepository;

interface ICarReadRepository extends IBaseRepository
{
    public function findCarById(string $carId): ?CarSnapshot;
}
