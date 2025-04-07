<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Application\Booking\Commands\CreateBookingCommand;
use Src\Application\Booking\Commands\CreateBookingHandler;
use Src\Application\Shared\Bus\CommandBus;
use Src\Domain\Booking\Repositories\IBookingRepository;
use Src\Infrastructure\Booking\Repositories\BookingRepository;
use Src\Infrastructure\Booking\Models\Booking;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Booking\Services\BookingService;

class BookingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Command bus Binding
        $this->app->singleton(CommandBus::class, function ($app) {
            $commandBus = new CommandBus();
            
            $commandBus->register(
                CreateBookingCommand::class, 
                $app->make(CreateBookingHandler::class)
            );

            return $commandBus;
        });

        // Bindings
        $this->app->singleton(IBookingService::class, BookingService::class);
        $this->app->singleton(IBookingRepository::class, BookingRepository::class);

    }

    public function boot(): void
    {
        //
    }
} 