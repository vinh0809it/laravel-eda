<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Services;

use Src\Domain\Shared\Interfaces\IPaginationResult;
use Src\Domain\Booking\Projections\IBookingProjection;

class BookingService implements IBookingService
{
    public function __construct(
        private readonly IBookingProjection $bookingProjection
    ) {}

    public function isConflictWithOtherBookings(int $carId, string $startDate, string $endDate): bool
    {
        $existingBookings = $this->bookingProjection->findByDateRange(
            $startDate,
            $endDate
        );
       
        $conflictingBookings = $existingBookings->filter(function ($booking) use ($carId) {
            return $booking->car_id === $carId;
        });
        
        return $conflictingBookings->isNotEmpty();
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
}