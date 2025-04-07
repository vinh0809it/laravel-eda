<?php

namespace Src\Domain\Car\Repositories;

use Src\Domain\Shared\Repositories\IBaseRepository;
use Illuminate\Database\Eloquent\Collection;

interface ICarRepository extends IBaseRepository
{
    public function updateAvailability(string $id, bool $isAvailable);
    public function getAvailableCars(): Collection;
    public function findByModel(string $model): Collection;
    public function getAvailableCarsByDateRange($startDate, $endDate): Collection;
} 