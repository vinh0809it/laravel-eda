<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Projections;

use Illuminate\Database\Eloquent\Collection;
use Src\Domain\Shared\Projections\IBaseProjection;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Shared\Interfaces\IPaginationResult;

interface IBookingProjection extends IBaseProjection
{
    public function onBookingCreated(BookingCreated $event): void;  
    public function findByUserId(string $userId): Collection;
    public function findByCarId(string $carId): Collection;
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function paginate(
        int $page,
        int $perPage,
        string $sortBy,
        string $sortDirection,
        array $filters = []
    ): IPaginationResult;
} 