<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\GetBookingById;

use Src\Booking\Application\DTOs\BookingDTO;
use Src\Booking\Application\Ports\BookingReadRepository;
use Src\Booking\Domain\Exceptions\BookingNotFoundException;
use Src\Shared\Application\Bus\Query;
use Src\Shared\Application\Bus\QueryHandler;

final readonly class GetBookingByIdHandler implements QueryHandler
{
    public function __construct(
        private BookingReadRepository $bookings
    ) {}

    public function handle(Query $query): BookingDTO
    {
        if (!$query instanceof GetBookingByIdQuery) {
            throw new \InvalidArgumentException('Query must be an instance of GetBookingByIdQuery');
        }

        $booking = $this->bookings->findBookingById($query->bookingId);

        // A booking that belongs to someone else is reported as not found
        if (!$booking || $booking->userId !== $query->userId) {
            throw new BookingNotFoundException(trace: ['bookingId' => $query->bookingId]);
        }

        return $booking;
    }
}
