<?php

declare(strict_types=1);

namespace Src\Shared\Application\Bus;

interface CommandBus
{
    public function dispatch(Command $command): mixed;
}
