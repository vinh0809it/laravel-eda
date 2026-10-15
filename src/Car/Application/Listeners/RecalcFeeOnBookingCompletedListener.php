<?php

declare(strict_types=1);

namespace Src\Car\Application\Listeners;

use Illuminate\Contracts\Events\Dispatcher;
use Src\Booking\Domain\Events\BookingCompleted;
use Src\Car\Application\Ports\CarReadRepository;
use Src\Car\Application\Ports\PopularityFeeCalculator;
use Src\Car\Domain\Events\PopularityFeeRecalculated;

final class RecalcFeeOnBookingCompletedListener
{
    public function __construct(
        private readonly CarReadRepository $cars,
        private readonly PopularityFeeCalculator $feeCalculator,
        private readonly Dispatcher $events
    ) {}

    public function handle(BookingCompleted $event): void
    {
        $car = $this->cars->findCarById($event->carId);

        if (!$car) {
            return;
        }

        $this->events->dispatch(new PopularityFeeRecalculated(
            bookingId: $event->bookingId,
            carId: $event->carId,
            newPopularityFee: $this->feeCalculator->calculatePopularityFee($car->pricePerDay, $car->bookedCount)
        ));
    }
}
