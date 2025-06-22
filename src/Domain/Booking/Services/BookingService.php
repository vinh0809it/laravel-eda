<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Services;

use Carbon\Carbon;
use Src\Domain\Shared\Interfaces\IPaginationResult;
use Src\Domain\Booking\ReadRepositories\IBookingReadRepository;
use Src\Domain\Booking\Snapshots\BookingSnapshot;

class BookingService implements IBookingService
{
    public function __construct(
        private readonly IBookingReadRepository $bookingReadRepository
    ) {}

    public function hasBookingConflict(string $userId, string $carId, Carbon $startDate, Carbon $endDate): bool
    {
        return $this->bookingReadRepository->hasBookingConflict(
            userId: $userId,
            carId: $carId,
            startDate: $startDate,
            endDate: $endDate
        );
    }

    public function getBookings(
        int $page = 1,
        int $perPage = 10,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        array $filters = []
    ): IPaginationResult {

        $paginatedResult = $this->bookingReadRepository->paginate(
            page: $page,
            perPage: $perPage,
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            filters: $filters
        );

        return $paginatedResult;
    }

    public function getBookingById(string $bookingId): BookingSnapshot
    {
        return $this->bookingReadRepository->findById($bookingId);
    }   
}