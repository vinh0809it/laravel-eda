<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use Src\Infrastructure\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Src\Domain\Shared\Repositories\IEventStoreRepository;
use Src\Infrastructure\EventStore\Repositories\EventStoreRepository;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
