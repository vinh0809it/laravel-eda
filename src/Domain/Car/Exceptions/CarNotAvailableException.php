<?php

namespace Src\Domain\Car\Exceptions;
use Src\Domain\Shared\Exceptions\BussinessException;

class CarNotAvailableException extends BussinessException
{
    private const ERROR_CODE = 'CAR_NOT_AVAILABLE';
    private const MESSAGE = "Car is not available";
    private const STATUS_CODE = 409;

    public function __construct(
        string $message = self::MESSAGE,
        array $trace = []
    ) {
        parent::__construct(
            errorCode: self::ERROR_CODE,
            message: $message,
            code: self::STATUS_CODE,
            trace: $trace
        );
    }
} 