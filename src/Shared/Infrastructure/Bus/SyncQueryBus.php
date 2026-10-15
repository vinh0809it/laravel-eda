<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus;

use Illuminate\Contracts\Container\Container;
use Src\Shared\Application\Bus\HandlerNotFoundException;
use Src\Shared\Application\Bus\Query;
use Src\Shared\Application\Bus\QueryBus;
use Src\Shared\Application\Bus\QueryHandler;

/** Synchronous, in-process query bus. See SyncCommandBus for the resolution strategy. */
final class SyncQueryBus implements QueryBus
{
    /** @var array<class-string<Query>, class-string<QueryHandler>> */
    private array $handlers = [];

    public function __construct(
        private readonly Container $container
    ) {}

    /** @param array<class-string<Query>, class-string<QueryHandler>> $map */
    public function map(array $map): void
    {
        $this->handlers = [...$this->handlers, ...$map];
    }

    public function dispatch(Query $query): mixed
    {
        $handlerClass = $this->handlers[$query::class]
            ?? throw new HandlerNotFoundException($query::class);

        return $this->container->make($handlerClass)->handle($query);
    }
}
