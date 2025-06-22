<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Projections;

use Src\Domain\Booking\Events\BookingChanged;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Shared\Projections\IBaseProjection;
use Src\Domain\Booking\Events\BookingCreated;

interface IBookingProjection extends IBaseProjection
{
    public function onBookingCreated(BookingCreated $event): void;  
    public function onBookingChanged(BookingChanged $event): void;  
    public function onBookingCompleted(BookingCompleted $event): void;  
}
