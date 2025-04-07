<?php

namespace Src\Domain\Booking\Repositories;

use Src\Domain\Shared\Repositories\IBaseRepository;
use Illuminate\Database\Eloquent\Collection;

interface IBookingRepository extends IBaseRepository
{
    public function findByUserId(string $userId): Collection;
    public function findByCarId(string $carId): Collection;
    public function findByDateRange(string $startDate, string $endDate): Collection;
} 