<?php

declare(strict_types=1);

namespace Src\Car\Application\Ports;

use Src\Car\Contracts\CarSnapshot;

interface CarReadRepository
{
    public function findCarById(string $carId): ?CarSnapshot;
}
