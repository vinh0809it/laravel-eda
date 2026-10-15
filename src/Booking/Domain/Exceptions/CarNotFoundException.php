<?php

declare(strict_types=1);

namespace Src\Booking\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\BusinessException;

/** A booking references a car the catalogue does not know. */
class CarNotFoundException extends BusinessException
{
    public function __construct(
        string $message = "Car not found",
        int $code = 404,
        array $trace = []
    ) {
        parent::__construct(
            errorCode: 'CAR_NOT_FOUND',
            message: $message,
            code: $code,
            trace: $trace
        );
    }
}
