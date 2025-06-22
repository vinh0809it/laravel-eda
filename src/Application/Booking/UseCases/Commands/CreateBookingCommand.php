<?php

namespace Src\Application\Booking\UseCases\Commands;

use Carbon\Carbon;
use Src\Application\Shared\Interfaces\ICommand;

class CreateBookingCommand implements ICommand
{
    public function __construct(
        public readonly string $carId,
        public readonly string $userId,
        public readonly Carbon $startDate,
        public readonly Carbon $endDate,
    ) {}
} 