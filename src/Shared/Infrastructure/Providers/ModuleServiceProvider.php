<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Src\Shared\Application\Bus\CommandBus;
use Src\Shared\Application\Bus\QueryBus;
use Src\Shared\Application\Ports\EventMapper;
use Src\Shared\Infrastructure\Bus\SyncCommandBus;
use Src\Shared\Infrastructure\Bus\SyncQueryBus;
use Src\Shared\Infrastructure\EventSourcing\RegistryEventMapper;

/**
 * Composition root of one module.
 *
 * A module declares what it owns (handlers, listeners, subscribers, persisted
 * events, routes, migrations) and this base class wires it into the framework.
 * Laravel's own `$singletons` / `$bindings` properties cover the port -> adapter
 * bindings, so a concrete provider is usually just configuration.
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> command => handler */
    protected array $commands = [];

    /** @var array<class-string, class-string> query => handler */
    protected array $queries = [];

    /** @var array<class-string, list<class-string>> event => listeners (side effects such as mail, notifications) */
    protected array $listeners = [];

    /** @var list<class-string> event subscribers, typically projectors, that declare their own event map */
    protected array $subscribers = [];

    /** @var list<class-string> domain events this module persists in the event store */
    protected array $events = [];

    protected ?string $apiRoutes = null;

    protected ?string $webRoutes = null;

    protected ?string $migrations = null;

    public function register(): void
    {
        $this->callAfterResolving(CommandBus::class, fn (SyncCommandBus $bus) => $bus->map($this->commands));
        $this->callAfterResolving(QueryBus::class, fn (SyncQueryBus $bus) => $bus->map($this->queries));
        $this->callAfterResolving(EventMapper::class, fn (RegistryEventMapper $mapper) => $mapper->register($this->events));
    }

    public function boot(): void
    {
        foreach ($this->listeners as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach ($this->subscribers as $subscriber) {
            Event::subscribe($subscriber);
        }

        if ($this->migrations) {
            $this->loadMigrationsFrom($this->migrations);
        }

        if ($this->apiRoutes) {
            $this->loadRoutesFrom($this->apiRoutes);
        }

        if ($this->webRoutes) {
            $this->loadRoutesFrom($this->webRoutes);
        }
    }
}
