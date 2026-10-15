<?php

namespace Src\Booking\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\BusinessException;

class BookingConflictException extends BusinessException
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