<?php

namespace Src\Car\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\BusinessException;

class CarNotFoundException extends BusinessException
{
    private const ERROR_CODE = 'CAR_NOT_FOUND';
    private const MESSAGE = "Car not found";
    private const STATUS_CODE = 404;

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