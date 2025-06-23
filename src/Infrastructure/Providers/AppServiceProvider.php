<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use Src\Infrastructure\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Src\Domain\Shared\EventStore\IEventMapper;
use Src\Domain\Shared\EventStore\IEventStoreRepository;
use Src\Infrastructure\EventStore\Repositories\EventStoreRepository;
use Src\Domain\Shared\Loggers\IEventProcessLogger;
use Src\Domain\Shared\Services\EventSourcingService;
use Src\Domain\Shared\Services\IEventSourcingService;
use Src\Infrastructure\EventStore\Mappers\EventMapper;
use Src\Infrastructure\Shared\Loggers\EloquentEventProcessLogger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register custom exception handler
        $this->app->singleton(ExceptionHandler::class, Handler::class);
        $this->app->singleton(IEventStoreRepository::class, EventStoreRepository::class);
        $this->app->singleton(IEventProcessLogger::class, EloquentEventProcessLogger::class);
        $this->app->singleton(IEventSourcingService::class, EventSourcingService::class);
        $this->app->singleton(IEventMapper::class, EventMapper::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
