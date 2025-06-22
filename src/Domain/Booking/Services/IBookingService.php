<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Services;

use Carbon\Carbon;
use Src\Domain\Shared\Interfaces\IPaginationResult;
use Src\Domain\Booking\Snapshots\BookingSnapshot;

interface IBookingService
{
    public function hasBookingConflict(string $userId, string $carId, Carbon $startDate, Carbon $endDate): bool;

    public function getBookings(
        int $page = 1,
        int $perPage = 10,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        array $filters = []
    ): IPaginationResult;

    public function getBookingById(string $bookingId): BookingSnapshot;
}