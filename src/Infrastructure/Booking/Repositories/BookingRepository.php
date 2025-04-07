<?php

namespace Src\Infrastructure\Booking\Repositories;

use Src\Infrastructure\Booking\Models\Booking;
use Src\Infrastructure\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Src\Domain\Booking\Repositories\IBookingRepository;

class BookingRepository extends BaseRepository implements IBookingRepository
{
    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }

    public function findByUserId(string $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function findByCarId(string $carId): Collection
    {
        return $this->model->where('car_id', $carId)->get();
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function findByDateRange($startDate, $endDate): Collection
    {
        return $this->model->whereBetween('start_date', [$startDate, $endDate])->get();
    }

    public function findByUserIdAndDateRange(string $userId, $startDate, $endDate): Collection
    {
        return $this->model->where('user_id', $userId)->whereBetween('start_date', [$startDate, $endDate])->get();
    }
} 