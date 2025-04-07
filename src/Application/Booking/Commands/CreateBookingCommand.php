<?php

namespace Src\Application\Booking\Commands;

use Src\Application\Shared\Interfaces\ICommand;

class CreateBookingCommand implements ICommand
{
    public function __construct(
        public readonly string $carId,
        public readonly string $userId,
        public readonly string $startDate,
        public readonly string $endDate,
    ) {}
} 