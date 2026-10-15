<?php

namespace Src\Shared\Application\Bus;

use Src\Shared\Application\Bus\Command;

interface CommandHandler
{
    /**
     * @param Command $command
     * 
     * @return mixed
     */
    public function handle(Command $command): mixed;
}