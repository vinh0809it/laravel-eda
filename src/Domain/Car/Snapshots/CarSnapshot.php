<?php

namespace Src\Domain\Car\Snapshots;

class CarSnapshot
{
    public function __construct(
        public readonly string $id,
        public readonly string $brand,
        public readonly string $model,
        public readonly int $year,
        public readonly float $pricePerDay,
        public readonly bool $isAvailable,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'pricePerDay' => $this->pricePerDay,
            'isAvailable' => $this->isAvailable,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            brand: $data['brand'],
            model: $data['model'],
            year: $data['year'],
            pricePerDay: $data['price_per_day'],
            isAvailable: $data['is_available'],
        );
    }
}
