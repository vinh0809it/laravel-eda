<?php

declare(strict_types=1);

namespace Src\Application\Booking\UseCases\Queries;

use Src\Application\Shared\Interfaces\IQuery;
use Src\Domain\Booking\Exceptions\BookingNotFoundException;
use Src\Domain\Shared\Interfaces\IPaginationResult;
use Src\Domain\Booking\Services\IBookingService;
use Src\Application\Shared\Interfaces\IQueryHandler;

class GetBookingsQueryHandler implements IQueryHandler
{
    public function __construct(
        private readonly IBookingService $bookingService
    ) 
    {}

    public function handle(IQuery $query): IPaginationResult
    {
        if (!$query instanceof GetBookingsQuery) {
            throw new \InvalidArgumentException('Query must be an instance of GetBookingsQuery');
        }

        $filters = [
            'booking_id' => $query->getBookingId()?->value(),
            'start_date' => $query->getStartDate(),
            'end_date' => $query->getEndDate(),
            'status' => $query->getStatus(),
            'user_id' => $query->getUserId(),
        ];

        $bookings = $this->bookingService->getBookings(
            page: $query->getPage(),
            perPage: $query->getPerPage(),
            sortBy: $query->getSortBy(),
            sortDirection: $query->getSortDirection(),
            filters: array_filter($filters)
        );

        if ($bookings->items() === [] && $query->getBookingId()) {
            throw new BookingNotFoundException($query->getBookingId()->value());
        }

        return $bookings;
    }
} 