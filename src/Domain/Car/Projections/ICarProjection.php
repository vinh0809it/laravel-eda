<?php

namespace Src\Domain\Car\Projections;

use Src\Domain\Shared\Projections\IBaseProjection;

interface ICarProjection extends IBaseProjection
{
    public function updateAvailability(string $id, bool $isAvailable): void;
} 