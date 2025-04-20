<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Services;

use Src\Domain\Shared\Interfaces\IPaginationResult;
use Src\Domain\Booking\Projections\IBookingProjection;
use Src\Application\Booking\DTOs\BookingProjectionDTO;

class BookingService implements IBookingService
{
    public function __construct(
        private readonly IBookingProjection $bookingProjection
    ) {}

    public function isConflictWithOtherBookings(string $userId, string $startDate, string $endDate): bool
    {
        $existingBookings = $this->bookingProjection->findByDateRange(
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

        $paginatedResult = $this->bookingProjection->paginate(
            page: $page,
            perPage: $perPage,
            sortBy: $sortBy,
            sortDirection: $sortDirection,
            filters: $filters
        );

        return $paginatedResult;
    }

    public function getBookingById(string $bookingId): BookingProjectionDTO
    {
        return $this->bookingProjection->findById($bookingId);
    }   
}