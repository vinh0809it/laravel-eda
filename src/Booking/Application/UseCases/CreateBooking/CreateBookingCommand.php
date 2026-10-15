<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\CreateBooking;

use Carbon\Carbon;
use Src\Shared\Application\Bus\Command;

final readonly class CreateBookingCommand implements Command
{
    public function __construct(
        public string $carId,
        public string $userId,
        public Carbon $startDate,
        public Carbon $endDate,
    ) {}
}
