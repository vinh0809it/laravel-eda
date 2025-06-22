<?php

declare(strict_types=1);

namespace Src\Application\Booking\UseCases\Commands;

use Carbon\Carbon;
use Src\Application\Shared\Interfaces\ICommand;

final class ChangeBookingCommand implements ICommand
{
    public function __construct(
        public readonly string $bookingId,
        public readonly Carbon $newStartDate,
        public readonly Carbon $newEndDate
    ) {}
} 