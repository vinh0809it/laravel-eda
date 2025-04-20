<?php

namespace Src\Domain\Car\Services;

use Src\Application\Car\DTOs\CarProjectionDTO;

interface ICarService
{
    public function isAvailable(string $carId): bool;
    public function isCarExists(string $carId): bool;
    public function getDailyPrice(string $carId): float;
    public function findCarById(string $carId): ?CarProjectionDTO;
} 