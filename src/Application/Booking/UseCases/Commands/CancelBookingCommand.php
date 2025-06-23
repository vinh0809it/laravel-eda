<?php

declare(strict_types=1);

namespace Src\Application\Booking\UseCases\Commands;

use Src\Application\Shared\Interfaces\ICommand;

final class CancelBookingCommand implements ICommand
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $cancelReason
    ) {}
} 