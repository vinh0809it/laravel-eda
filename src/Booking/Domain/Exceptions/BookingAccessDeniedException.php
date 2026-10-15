<?php

declare(strict_types=1);

namespace Src\Booking\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\BusinessException;

class BookingAccessDeniedException extends BusinessException
{
    public function __construct(
        string $message = "You do not have access to this booking",
        int $code = 403,
        array $trace = []
    ) {
        parent::__construct(
            errorCode: 'BOOKING_ACCESS_DENIED',
            message: $message,
            code: $code,
            trace: $trace
        );
    }
}
