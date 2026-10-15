<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Src\Shared\Application\Bus\CommandBus;
use Src\Shared\Application\Bus\QueryBus;
use Src\Shared\Application\Ports\AggregateRepository;
use Src\Shared\Application\Ports\EventMapper;
use Src\Shared\Application\Ports\EventProcessLogger;
use Src\Shared\Application\Ports\EventStore;
use Src\Shared\Infrastructure\Bus\SyncCommandBus;
use Src\Shared\Infrastructure\Bus\SyncQueryBus;
use Src\Shared\Infrastructure\EventSourcing\EventSourcedAggregateRepository;
use Src\Shared\Infrastructure\EventSourcing\RegistryEventMapper;
use Src\Shared\Infrastructure\Persistence\Eloquent\EloquentEventProcessLogger;
use Src\Shared\Infrastructure\Persistence\Eloquent\EloquentEventStore;

/**
 * Shared kernel: buses, event store and cross-cutting framework setup.
 * Module-specific wiring lives in each module's ModuleServiceProvider.
 */
final class SharedServiceProvider extends ServiceProvider
{
    public array $singletons = [
        CommandBus::class => SyncCommandBus::class,
        QueryBus::class => SyncQueryBus::class,
        EventMapper::class => RegistryEventMapper::class,
        EventStore::class => EloquentEventStore::class,
        EventProcessLogger::class => EloquentEventProcessLogger::class,
        AggregateRepository::class => EventSourcedAggregateRepository::class,
    ];

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');

        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

        Vite::prefetch(concurrency: 3);
    }
}
