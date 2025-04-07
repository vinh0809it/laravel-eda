<?php

declare(strict_types=1);

namespace Src\Domain\Booking\Exceptions;

use Src\Domain\Shared\Exceptions\BussinessException;

class BookingNotFoundException extends BussinessException
{
    public function __construct(
        string $message = "Booking not found",
        int $code = 404,
        array $trace = []
    ) {
        parent::__construct(
            errorCode: 'BOOKING_NOT_FOUND',
            message: $message,
            code: $code,
            trace: $trace
        );
    }
} 