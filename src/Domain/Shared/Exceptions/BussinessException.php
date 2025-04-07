<?php 
namespace Src\Domain\Shared\Exceptions;
use Exception;

class BussinessException extends Exception
{

    public function __construct(
        protected string $errorCode = 'BUSINESS_ERROR',
        string $message = 'Bussiness Logic Related Issue.',
        int $code = 500,
        protected array $trace = []
    ) {
        parent::__construct($message, $code);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getTraceData(): array
    {
        return $this->trace;
    }
}