<?php

declare(strict_types=1);

namespace Src\Booking\Application\Listeners;

use Psr\Log\LoggerInterface;
use Src\Booking\Domain\Events\BookingCanceled;

final class BookingCanceledListener
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function handle(BookingCanceled $event): void
    {
        $this->logger->info('Booking completed', [
            'booking_id' => $event->bookingId,
            'car_id' => $event->carId,
            'canceled_at' => $event->canceledAt,
            'cancel_reason' => $event->cancelReason
        ]);
    }
} 