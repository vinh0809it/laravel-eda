<?php
namespace Src\Application\Shared\Exceptions;

use Src\Domain\Shared\Exceptions\DomainException;

final class HandlerNotFoundException extends DomainException
{
    public function __construct(string $command)
    {
        $this->httpCode = 404;
        parent::__construct('No handler registered for '.$command);
    }
}