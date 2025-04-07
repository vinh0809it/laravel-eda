<?php

namespace Src\Domain\Booking\Services;

use Src\Domain\Car\Repositories\ICarRepository;
use Src\Domain\Booking\Repositories\IBookingRepository;

class BookingService implements IBookingService
{
    public function __construct(
        private readonly ICarRepository $carRepository,
        private readonly IBookingRepository $bookingRepository
    ) {}

    public function isConflictWithOtherBookings(int $carId, string $startDate, string $endDate): bool
    {
        $existingBookings = $this->bookingRepository->findByDateRange(
            $startDate,
            $endDate
        );
       
        $conflictingBookings = $existingBookings->filter(function ($booking) use ($carId) {
            return $booking->car_id === $carId;
        });
        
        return $conflictingBookings->isNotEmpty();
    }
}