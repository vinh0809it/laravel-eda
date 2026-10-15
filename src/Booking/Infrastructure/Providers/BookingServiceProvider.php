<?php

declare(strict_types=1);

namespace Src\Booking\Infrastructure\Providers;

use Src\Booking\Application\Listeners\BookingCanceledListener;
use Src\Booking\Application\Listeners\BookingChangedListener;
use Src\Booking\Application\Listeners\BookingCompletedListener;
use Src\Booking\Application\Listeners\BookingCreatedListener;
use Src\Booking\Application\UseCases\CancelBooking\CancelBookingCommand;
use Src\Booking\Application\UseCases\CancelBooking\CancelBookingHandler;
use Src\Booking\Application\UseCases\ChangeBooking\ChangeBookingCommand;
use Src\Booking\Application\UseCases\ChangeBooking\ChangeBookingHandler;
use Src\Booking\Application\UseCases\CompleteBooking\CompleteBookingCommand;
use Src\Booking\Application\UseCases\CompleteBooking\CompleteBookingHandler;
use Src\Booking\Application\UseCases\CreateBooking\CreateBookingCommand;
use Src\Booking\Application\UseCases\CreateBooking\CreateBookingHandler;
use Src\Booking\Application\UseCases\GetBookingById\GetBookingByIdQuery;
use Src\Booking\Application\UseCases\GetBookingById\GetBookingByIdHandler;
use Src\Booking\Application\UseCases\GetBookings\GetBookingsQuery;
use Src\Booking\Application\UseCases\GetBookings\GetBookingsHandler;
use Src\Booking\Domain\Events\BookingCanceled;
use Src\Booking\Domain\Events\BookingChanged;
use Src\Booking\Domain\Events\BookingCompleted;
use Src\Booking\Domain\Events\BookingCreated;
use Src\Booking\Application\Ports\BookingPricing;
use Src\Booking\Application\Ports\BookingReadRepository;
use Src\Booking\Application\Ports\CarCatalog;
use Src\Booking\Infrastructure\Adapters\CarCatalogAdapter;
use Src\Booking\Infrastructure\Adapters\PricingAdapter;
use Src\Booking\Infrastructure\Persistence\Eloquent\BookingReadModelProjector;
use Src\Booking\Infrastructure\Persistence\Eloquent\EloquentBookingReadRepository;
use Src\Shared\Infrastructure\Providers\ModuleServiceProvider;

final class BookingServiceProvider extends ModuleServiceProvider
{
    public array $singletons = [
        BookingReadRepository::class => EloquentBookingReadRepository::class,
        // Ports to other modules, each behind an anti-corruption adapter
        CarCatalog::class => CarCatalogAdapter::class,
        BookingPricing::class => PricingAdapter::class,
    ];

    protected array $commands = [
        CreateBookingCommand::class => CreateBookingHandler::class,
        ChangeBookingCommand::class => ChangeBookingHandler::class,
        CompleteBookingCommand::class => CompleteBookingHandler::class,
        CancelBookingCommand::class => CancelBookingHandler::class,
    ];

    protected array $queries = [
        GetBookingsQuery::class => GetBookingsHandler::class,
        GetBookingByIdQuery::class => GetBookingByIdHandler::class,
    ];

    protected array $events = [
        BookingCreated::class,
        BookingChanged::class,
        BookingCompleted::class,
        BookingCanceled::class,
    ];

    protected array $listeners = [
        BookingCreated::class => [BookingCreatedListener::class],
        BookingChanged::class => [BookingChangedListener::class],
        BookingCompleted::class => [BookingCompletedListener::class],
        BookingCanceled::class => [BookingCanceledListener::class],
    ];

    protected array $subscribers = [
        BookingReadModelProjector::class,
    ];

    protected ?string $apiRoutes = __DIR__ . '/../../Presentation/routes/api.php';

    protected ?string $migrations = __DIR__ . '/../Migrations';
}
