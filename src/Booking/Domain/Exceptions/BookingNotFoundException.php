<?php

declare(strict_types=1);

namespace Src\Booking\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\BusinessException;

class BookingNotFoundException extends BusinessException
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