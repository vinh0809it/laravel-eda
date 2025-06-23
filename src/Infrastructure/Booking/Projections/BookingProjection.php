<?php

namespace Src\Infrastructure\Booking\Projections;

use Src\Infrastructure\Booking\Models\Booking;
use Src\Infrastructure\Shared\Projections\BaseProjection;
use Src\Domain\Booking\Projections\IBookingProjection;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Booking\Events\BookingCanceled;
use Src\Domain\Booking\Events\BookingChanged;
use Src\Domain\Shared\Loggers\IEventProcessLogger;

class BookingProjection extends BaseProjection implements IBookingProjection
{
    public function __construct(
        Booking $model,
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

        $this->model->create([
            'id' => $event->bookingId,
            'user_id' => $event->userId,
            'car_id' => $event->carId,  
            'start_date' => $event->startDate,
            'end_date' => $event->endDate,
            'original_price' => $event->originalPrice,
            'status' => BookingStatus::CREATED->value
        ]);

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

        $booking = $this->model->find($event->bookingId);

        if(!$booking) {
            $this->logger->markFailure($event->bookingId, $loggerContext, 'Booking not found for completion');
            return;
        }
        
        $booking->actual_end_date = $event->actualEndDate;
        $booking->final_price = $event->finalPrice;
        $booking->completion_note = $event->completionNote;
        $booking->status = BookingStatus::COMPLETED->value;
        $booking->save();

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

        $booking = $this->model->find($event->bookingId);

        if(!$booking) {
            $this->logger->markFailure($event->bookingId, $loggerContext, 'Booking not found for changed');
            return;
        }
        
        $booking->start_date = $event->newStartDate;
        $booking->end_date = $event->newEndDate;
        $booking->original_price = $event->newOriginalPrice;
        $booking->status = BookingStatus::CHANGED->value;
        $booking->save();

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

        $booking = $this->model->find($event->bookingId);

        if(!$booking) {
            $this->logger->markFailure($event->bookingId, $loggerContext, 'Booking not found for cancelation');
            return;
        }
        
        $booking->canceled_at = $event->canceledAt;
        $booking->cancel_reason = $event->cancelReason;
        $booking->status = BookingStatus::CANCELED->value;
        $booking->save();

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }
}
