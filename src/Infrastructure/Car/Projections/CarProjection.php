<?php

namespace Src\Infrastructure\Car\Projections;

use Src\Infrastructure\Car\Models\Car;
use Src\Infrastructure\Shared\Projections\BaseProjection;
use Illuminate\Database\Eloquent\Collection;
use Src\Domain\Car\Projections\ICarProjection;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Shared\Loggers\IEventProcessLogger;

class CarProjection extends BaseProjection implements ICarProjection
{
    public function __construct(
        Car $model,
        private readonly IEventProcessLogger $logger
    ) {
        parent::__construct($model);
    }

    public function onBookingCreated(BookingCreated $event): void
    {
        if ($this->logger->hasProcessed($event->bookingId, self::class)) {
            return;
        }

        $this->updateAvailability($event->carId, false);

        $this->logger->markSuccess($event->bookingId, self::class);
    }

    public function updateAvailability(string $id, bool $isAvailable): void
    {
        $this->model->where('id', $id)->update(['is_available' => $isAvailable]);
    }

    public function getAvailableCars(): Collection
    {
        return $this->model->where('is_available', true)->get();
    }
}