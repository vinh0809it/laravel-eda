<?php

namespace Src\Domain\Car\Services;

use Src\Domain\Car\Snapshots\CarSnapshot;

interface ICarService
{
    public function getDailyPrice(string $carId): float;
    public function findCarById(string $carId): ?CarSnapshot;
}
