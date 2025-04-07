<?php

namespace Src\Domain\Car\Projections;

use Src\Domain\Shared\Projections\IBaseProjection;
use Illuminate\Database\Eloquent\Collection;

interface ICarProjection extends IBaseProjection
{
    public function updateAvailability(string $id, bool $isAvailable);
    public function getAvailableCars(): Collection;
    public function findByModel(string $model): Collection;
    public function getAvailableCarsByDateRange($startDate, $endDate): Collection;
} 