<?php

namespace Src\Infrastructure\Exceptions;

use Exception;

class UnknownEventException extends Exception
{
    public function __construct(
        string $message = "Unknown event",
        int $code = 400,
        array $trace = []
    ) {
        parent::__construct(
            errorCode: 'UNKNOWN_EVENT',
            message: $message,
            code: $code,
            trace: $trace
        );
    }
} 