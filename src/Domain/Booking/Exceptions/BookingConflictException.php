<?php

namespace Src\Domain\Booking\Exceptions;

use Src\Domain\Shared\Exceptions\BussinessException;

class BookingConflictException extends BussinessException
{
    public function __construct(
        string $message = "Booking conflict",
        int $code = 409,
        array $trace = []
    ) {
        parent::__construct(
            errorCode: 'BOOKING_CONFLICT',
            message: $message,
            code: $code,
            trace: $trace
        );
    }
} 