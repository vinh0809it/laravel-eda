<?php

namespace Src\Application\Car\DTOs;

class CarDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $brand,
        public readonly string $model,
        public readonly string $year,
        public readonly float $pricePerDay
    ) {}
}