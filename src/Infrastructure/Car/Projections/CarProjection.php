<?php

namespace Src\Infrastructure\Car\Projections;

use Carbon\Carbon;
use Src\Domain\Booking\Events\BookingCanceled;
use Src\Domain\Booking\Events\BookingChanged;
use Src\Infrastructure\Car\Models\Car;
use Src\Infrastructure\Shared\Projections\BaseProjection;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Car\Projections\ICarProjection;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Car\Events\PopularityFeeRecalculated;
use Src\Domain\Shared\Loggers\IEventProcessLogger;

class CarProjection extends BaseProjection implements ICarProjection
{
    public function __construct(
        Car $model,
        private readonly IEventProcessLogger $logger
    ) {
        parent::__construct($model);
    }

    /**
     * @param BookingCreated $event
     * 
     * @return void
     */
    public function onBookingCreated(BookingCreated $event): void
    {
        $loggerContext = $this->context(__FUNCTION__);

        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        $this->increaseBookedCount($event->carId);

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    /**
     * @param BookingCompleted $event
     * 
     * @return void
     */
    public function onBookingCompleted(BookingCompleted $event): void
    {
        $loggerContext = $this->context(__FUNCTION__);

        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        $this->updateLastBookingCompletedAt($event->carId, $event->actualEndDate);

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    /**
     * @param BookingChanged $event
     * 
     * @return void
     */
    public function onBookingChanged(BookingChanged $event): void
    {
        $loggerContext = $this->context(__FUNCTION__);

        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        // TODO:
        // update something
        
        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    /**
     * @param BookingCanceled $event
     * 
     * @return void
     */
    public function onBookingCanceled(BookingCanceled $event): void
    {
        $loggerContext = $this->context(__FUNCTION__);

        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        $this->decreaseBookedCount($event->carId);

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    /**
     * @param PopularityFeeRecalculated $event
     * 
     * @return void
     */
    public function onPopularityFeeRecalculated(PopularityFeeRecalculated $event): void
    {
        $loggerContext = $this->context(__FUNCTION__);

        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        $this->updatePopularityFee($event->carId, $event->newPopularityFee);

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    /**
     * @param string $id
     * 
     * @return void
     */
    public function increaseBookedCount(string $id): void
    {
        $this->model->where('id', $id)->increment('booked_count');
    }

    /**
     * @param string $id
     * 
     * @return void
     */
    public function decreaseBookedCount(string $id): void
    {
        $this->model->where('id', $id)->decrement('booked_count');
    }

    /**
     * @param string $id
     * @param Carbon $completedAt
     * 
     * @return void
     */
    public function updateLastBookingCompletedAt(string $id, Carbon $completedAt): void
    {
        $this->model->where('id', $id)->update([
            'last_booking_completed_at' => $completedAt
        ]);
    }

    /**
     * @param string $id
     * @param float $newPopularityFee
     * 
     * @return void
     */
    public function updatePopularityFee(string $id, float $newPopularityFee): void
    {
        $this->model->where('id', $id)->update(['popularity_fee' => $newPopularityFee]);
    }
}