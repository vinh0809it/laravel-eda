<?php

namespace Src\Domain\Car\Aggregates;

class CarAggregate
{
    private int $version = 0;

    public function __construct(
        private readonly string $id,
        private readonly string $brand,
        private readonly string $model,
        private readonly string $year,
        private readonly float $pricePerDay,
        private readonly int $bookedCount,
    ) {}

    public static function create(
        string $id,
        string $brand,
        string $model,
        string $year,
        float $pricePerDay,
        int $bookedCount
    ): self {
        $car = new self(
            id: $id,
            brand: $brand,
            model: $model,
            year: $year,
            pricePerDay: $pricePerDay,
            bookedCount: $bookedCount,
        );

        $car->version = 1;

        return $car;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'price_per_day' => $this->pricePerDay,
            'booked_count' => $this->bookedCount,
            'version' => $this->version,
        ];
    }

    public function getBookedCount(): int
    {
        return $this->bookedCount;
    }

    public function getPricePerDay(): float
    {
        return $this->pricePerDay;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }
    
    public function getModel(): string
    {
        return $this->model;
    }   

    public function getYear(): string
    {
        return $this->year;
    }   
    
    public function getVersion(): int
    {
        return $this->version;
    }   
} 