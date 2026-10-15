<?php

declare(strict_types=1);

namespace Src\Booking\Application\Listeners;

use Psr\Log\LoggerInterface;
use Src\Booking\Domain\Events\BookingChanged;

final class BookingChangedListener
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function handle(BookingChanged $event): void
    {
        $this->logger->info('Booking changed', [
            'booking_id' => $event->bookingId,
            'start_date' => $event->newStartDate,
            'end_date' => $event->newEndDate,
            'original_price' => $event->newOriginalPrice
        ]);
    }
} 