<?php 
namespace Src\Application\Shared\Bus;

use Src\Application\Shared\Exceptions\HandlerNotFoundException;
use Src\Application\Shared\Interfaces\ICommand;
use Src\Application\Shared\Interfaces\ICommandBus;
use Src\Application\Shared\Interfaces\ICommandHandler;

class CommandBus implements ICommandBus
{
    protected $handlers = [];

    /**
     * @param string $commandClass
     * @param ICommandHandler $handler
     * 
     * @return void
     */
    public function register(string $commandClass, ICommandHandler $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    /**
     * @param ICommand $command
     * 
     */
    public function dispatch(ICommand $command)
    {
        $commandClass = get_class($command);

        if (!isset($this->handlers[$commandClass])) {
            throw new HandlerNotFoundException($commandClass);
        }

        return $this->handlers[$commandClass]->handle($command);
    }
}