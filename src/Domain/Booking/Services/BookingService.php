<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Services;

use Src\Domain\Shared\Interfaces\IPaginationResult;
use Src\Application\Booking\DTOs\BookingDTO;
use Src\Domain\Booking\ReadRepositories\IBookingReadRepository;

class BookingService implements IBookingService
{
    public function __construct(
        private readonly IBookingReadRepository $bookingReadRepository
    ) {}

    public function isConflictWithOtherBookings(string $userId, string $startDate, string $endDate): bool
    {
        $existingBookings = $this->bookingReadRepository->findByDateRange(
            $startDate,
            $endDate
        );

        $conflictingBookings = array_filter($existingBookings, function ($booking) use ($userId) {
            return $booking->userId === $userId;
        });
        
        return count($conflictingBookings) > 0;
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

    public function getBookingById(string $bookingId): BookingDTO
    {
        return $this->bookingReadRepository->findById($bookingId);
    }   
}