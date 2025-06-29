<?php

namespace Src\Domain\Car\Projections;

use Carbon\Carbon;
use Src\Domain\Booking\Events\BookingChanged;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Shared\Projections\IBaseProjection;

interface ICarProjection extends IBaseProjection
{
    public function onBookingCreated(BookingCreated $event): void;
    public function onBookingChanged(BookingChanged $event): void;
    public function onBookingCompleted(BookingCompleted $event): void;
    public function increaseBookedCount(string $id): void;
    public function decreaseBookedCount(string $id): void;
    public function updateLastBookingCompletedAt(string $id, Carbon $completedAt): void;
    public function updatePopularityFee(string $id, float $newPopularityFee): void;
}
