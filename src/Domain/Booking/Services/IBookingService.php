<?php

namespace Src\Domain\Booking\Services;

interface IBookingService
{
    public function isConflictWithOtherBookings(int $carId, string $startDate, string $endDate): bool;
}