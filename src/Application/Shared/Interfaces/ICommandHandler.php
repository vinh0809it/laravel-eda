<?php

namespace Src\Application\Shared\Interfaces;

use Src\Application\Shared\Interfaces\ICommand;

interface ICommandHandler
{
    /**
     * @param ICommand $command
     * 
     * @return mixed
     */
    public function handle(ICommand $command): mixed;
}