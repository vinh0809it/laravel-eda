<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Services;

use Src\Domain\Shared\Interfaces\IPaginationResult;
use Src\Application\Booking\DTOs\BookingProjectionDTO;

interface IBookingService
{
    public function isConflictWithOtherBookings(string $userId, string $startDate, string $endDate): bool;

    public function getBookings(
        int $page = 1,
        int $perPage = 10,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        array $filters = []
    ): IPaginationResult;

    public function getBookingById(string $bookingId): BookingProjectionDTO;
}