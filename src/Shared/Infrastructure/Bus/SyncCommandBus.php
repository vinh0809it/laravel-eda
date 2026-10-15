<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus;

use Illuminate\Contracts\Container\Container;
use Src\Shared\Application\Bus\Command;
use Src\Shared\Application\Bus\CommandBus;
use Src\Shared\Application\Bus\CommandHandler;
use Src\Shared\Application\Bus\HandlerNotFoundException;

/**
 * Synchronous, in-process command bus.
 *
 * Handlers are registered as class names and resolved from the container on
 * dispatch, so booting the application does not instantiate every handler.
 * An asynchronous bus would be another adapter of the same port.
 */
final class SyncCommandBus implements CommandBus
{
    /** @var array<class-string<Command>, class-string<CommandHandler>> */
    private array $handlers = [];

    public function __construct(
        private readonly Container $container
    ) {}

    /** @param array<class-string<Command>, class-string<CommandHandler>> $map */
    public function map(array $map): void
    {
        $this->handlers = [...$this->handlers, ...$map];
    }

    public function dispatch(Command $command): mixed
    {
        $handlerClass = $this->handlers[$command::class]
            ?? throw new HandlerNotFoundException($command::class);

        return $this->container->make($handlerClass)->handle($command);
    }
}
