<?php

declare(strict_types=1);

namespace Src\Application\Booking\UseCases\Commands;

use Src\Application\Shared\Interfaces\ICommand;

final class CompleteBookingCommand implements ICommand
{
    public function __construct(
        public readonly string $bookingId
    ) {
    }
} 