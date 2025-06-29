<?php

namespace Src\Application\Car\Listeners;

use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Car\Events\PopularityFeeRecalculated;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;

class RecalcFeeOnBookingCompletedListener
{
    public function __construct(
        private ICarService $carService,
        private IPriceService $priceService
    ) {}

    public function handle(BookingCompleted $event): void
    {
        $car = $this->carService->findCarById($event->carId);

        $pricePerDay = $car->pricePerDay;
        $bookedCount = $car->bookedCount;

        $newPopularityFee = $this->priceService->calculatePopularityFee($pricePerDay, $bookedCount);

        $popularityFeeRecalculated = new PopularityFeeRecalculated(
            bookingId: $event->bookingId,
            carId: $event->carId,
            newPopularityFee: $newPopularityFee
        );

        event($popularityFeeRecalculated);
    }
}
