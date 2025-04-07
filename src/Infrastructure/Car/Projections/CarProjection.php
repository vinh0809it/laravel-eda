<?php

namespace Src\Infrastructure\Car\Projections;

use Src\Infrastructure\Car\Models\Car;
use Src\Infrastructure\Shared\Projections\BaseProjection;
use Illuminate\Database\Eloquent\Collection;
use Src\Domain\Car\Projections\ICarProjection;

class CarProjection extends BaseProjection implements ICarProjection
{
    public function __construct(Car $model)
    {
        parent::__construct($model);
    }

    public function updateAvailability(string $id, bool $isAvailable)
    {
        return $this->model->where('id', $id)->update(['is_available' => $isAvailable]);
    }

    public function getAvailableCars(): Collection
    {
        return $this->model->where('is_available', true)->get();
    }

    public function findByModel(string $model): Collection
    {
        return $this->model->where('model', $model)->get();
    }

    public function findById(string $id)
    {
        return Car::find($id);
    }

    public function getAll()
    {
        return Car::all();
    }

    public function getAvailableCarsByDateRange($startDate, $endDate): Collection
    {
        return $this->model->whereDoesntHave('bookings', function ($query) use ($startDate, $endDate) {
            $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate]);
            });
        })->get();
    }
}