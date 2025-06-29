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

    /**
     * @param string $userId
     * @param string $carId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * 
     * @return bool
     */
    public function hasBookingConflict(string $userId, string $carId, Carbon $startDate, Carbon $endDate): bool
    {
        return $this->bookingReadRepository->hasBookingConflict(
            userId: $userId,
            carId: $carId,
            startDate: $startDate,
            endDate: $endDate
        );
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param string $sortBy
     * @param string $sortDirection
     * @param array $filters
     * 
     * @return IPaginationResult
     */
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

    /**
     * @param string $bookingId
     * 
     * @return BookingSnapshot|null
     */
    public function getBookingById(string $bookingId): ?BookingSnapshot
    {
        return $this->bookingReadRepository->findBookingById($bookingId);
    }   
}
