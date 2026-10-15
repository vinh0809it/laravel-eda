<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\ChangeBooking;

use Carbon\Carbon;
use Src\Shared\Application\Bus\Command;

final readonly class ChangeBookingCommand implements Command
{
    public function __construct(
        public string $bookingId,
        public string $userId,
        public Carbon $newStartDate,
        public Carbon $newEndDate,
    ) {}
}
