<?php

declare(strict_types=1);

namespace Src\Booking\Application\Ports;

use Carbon\Carbon;
use Src\Booking\Application\DTOs\BookingDTO;
use Src\Shared\Application\Pagination\PaginationResult;

interface BookingReadRepository
{
    public function hasBookingConflict(string $userId, string $carId, Carbon $startDate, Carbon $endDate, ?string $excludeBookingId = null): bool;

    public function findBookingById(string $bookingId): ?BookingDTO;

    public function paginate(int $page, int $perPage, string $sortBy, string $sortDirection, array $filters = []): PaginationResult;
}
