<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\CancelBooking;

use Src\Shared\Application\Bus\Command;

final readonly class CancelBookingCommand implements Command
{
    public function __construct(
        public string $bookingId,
        public string $userId,
        public string $cancelReason,
    ) {}
}
