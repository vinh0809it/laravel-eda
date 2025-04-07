<?php

namespace Src\Application\Shared\Interfaces;

use Src\Application\Shared\Interfaces\ICommand;
use Src\Application\Shared\Interfaces\ICommandHandler;

interface ICommandBus
{
    public function register(string $commandClass, ICommandHandler $handler): void;
    public function dispatch(ICommand $command);
}