<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\GetBookings;

use Src\Booking\Application\Ports\BookingReadRepository;
use Src\Shared\Application\Pagination\PaginationResult;
use Src\Shared\Application\Bus\Query;
use Src\Shared\Application\Bus\QueryHandler;

final readonly class GetBookingsHandler implements QueryHandler
{
    public function __construct(
        private BookingReadRepository $bookings
    ) {}

    /** @return PaginationResult<BookingDTO> */
    public function handle(Query $query): PaginationResult
    {
        if (!$query instanceof GetBookingsQuery) {
            throw new \InvalidArgumentException('Query must be an instance of GetBookingsQuery');
        }

        return $this->bookings->paginate(
            page: $query->page,
            perPage: $query->perPage,
            sortBy: $query->sortBy,
            sortDirection: $query->sortDirection,
            filters: array_filter([
                'start_date' => $query->startDate,
                'end_date' => $query->endDate,
                'status' => $query->status,
                'user_id' => $query->userId,
            ])
        );
    }
}
