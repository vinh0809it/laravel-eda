<?php

declare(strict_types=1);

namespace Src\Application\Booking\UseCases\Commands;

use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Application\Shared\Interfaces\ICommand;
use Src\Application\Shared\Interfaces\ICommandHandler;
use Src\Application\Booking\DTOs\BookingResponseDTO;
use Src\Domain\Booking\Exceptions\BookingNotFoundException;
use Src\Domain\Shared\Services\IEventSourcingService;

final class ChangeBookingCommandHandler implements ICommandHandler
{
    public function __construct(
        private readonly IEventSourcingService $eventSourcingService,
        private readonly IBookingService $bookingService,
        private readonly ICarService $carService,
        private readonly IPriceService $priceService
    ) {}

    public function handle(ICommand $command): mixed
    {
        if (!$command instanceof ChangeBookingCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ChangeBookingCommand');
        }

        $events = $this->eventSourcingService->getEvents(
            aggregateType: BookingAggregate::AGGREGATE_TYPE,
            aggregateId: $command->bookingId
        );

        if (empty($events)) {
            throw new BookingNotFoundException(
                trace: ['bookingId' => $command->bookingId]
            );
        }

        $booking = BookingAggregate::replayEvents($events);

        // Get daily price from Car
        $dailyPrice = $this->carService->getDailyPrice($booking->getCarId());

        // Calc the new Booking Price
        $newOriginalPrice = $this->priceService->calculateBookingPrice(
            dailyPrice: $dailyPrice,
            startDate: $command->newStartDate,
            endDate: $command->newEndDate
        );

        // Change the booking
        $booking->change(
            newStartDate: $command->newStartDate,
            newEndDate: $command->newEndDate,
            newOriginalPrice: $newOriginalPrice
        );

        // Store event in event store
        $this->eventSourcingService->save($booking);

        $responseDTO = new BookingResponseDTO($booking);
        return $responseDTO->forChanging();
    }
} 