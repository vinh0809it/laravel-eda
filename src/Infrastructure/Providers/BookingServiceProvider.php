<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Application\Booking\UseCases\Commands\CreateBookingCommand;
use Src\Application\Booking\UseCases\Commands\CreateBookingCommandHandler;
use Src\Application\Shared\Bus\CommandBus;
use Src\Application\Shared\Bus\QueryBus;
use Src\Application\Booking\UseCases\Queries\GetBookingsQuery;
use Src\Application\Booking\UseCases\Queries\GetBookingsQueryHandler;
use Src\Domain\Booking\Projections\IBookingProjection;
use Src\Infrastructure\Booking\Projections\BookingProjection;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Booking\Services\BookingService;
use Src\Application\Booking\UseCases\Commands\CompleteBookingCommand;
use Src\Application\Booking\UseCases\Commands\CompleteBookingCommandHandler;
use Src\Domain\Booking\ReadRepositories\IBookingReadRepository;
use Src\Infrastructure\Booking\ReadRepositories\BookingReadRepository;

class BookingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Command bus Binding
        $this->app->singleton(CommandBus::class, function ($app) {
            $commandBus = new CommandBus();
            
            $commandBus->register(
                CreateBookingCommand::class, 
                $app->make(CreateBookingCommandHandler::class)
            );

            $commandBus->register(
                CompleteBookingCommand::class, 
                $app->make(CompleteBookingCommandHandler::class)
            );

            return $commandBus;
        });

        // Query bus Binding
        $this->app->singleton(QueryBus::class, function ($app) {
            $queryBus = new QueryBus();
            
            $queryBus->register(
                GetBookingsQuery::class, 
                $app->make(GetBookingsQueryHandler::class)
            );

            return $queryBus;
        });

        // Bindings
        $this->app->singleton(IBookingService::class, BookingService::class);
        $this->app->singleton(IBookingProjection::class, BookingProjection::class);
        $this->app->singleton(IBookingReadRepository::class, BookingReadRepository::class);

    }

    public function boot(): void
    {
        //
    }
} 