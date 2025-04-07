<?php
namespace Src\Application\Shared\Bus;

use Src\Application\Shared\Exceptions\HandlerNotFoundException;
use Src\Application\Shared\Interfaces\IQuery;
use Src\Application\Shared\Interfaces\IQueryBus;
use Src\Application\Shared\Interfaces\IQueryHandler;

class QueryBus implements IQueryBus
{
    private array $handlers = [];

    /**
     * @param string $queryClass
     * @param IQueryHandler $handler
     * 
     * @return void
     */
    public function register(string $queryClass, IQueryHandler $handler): void
    {
        $this->handlers[$queryClass] = $handler;
    }

    /**
     * @param IQuery $query
     * 
     * @return mixed
     */
    public function dispatch(IQuery $query): mixed
    {
        $queryClass = get_class($query);

        if (!isset($this->handlers[$queryClass])) {
            throw new HandlerNotFoundException($queryClass);
        }

        return $this->handlers[$queryClass]->handle($query);
    }
}