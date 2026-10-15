<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\CompleteBooking;

use Carbon\Carbon;
use Src\Booking\Application\DTOs\BookingDTO;
use Src\Booking\Application\Ports\BookingPricing;
use Src\Booking\Application\Ports\CarCatalog;
use Src\Booking\Domain\Aggregates\BookingAggregate;
use Src\Booking\Domain\Exceptions\BookingNotFoundException;
use Src\Booking\Domain\Exceptions\CarNotFoundException;
use Src\Shared\Application\Bus\Command;
use Src\Shared\Application\Bus\CommandHandler;
use Src\Shared\Application\Ports\AggregateRepository;

final readonly class CompleteBookingHandler implements CommandHandler
{
    public function __construct(
        private AggregateRepository $eventSourcingService,
        private CarCatalog $cars,
        private BookingPricing $pricing
    ) {}

    public function handle(Command $command): BookingDTO
    {
        if (!$command instanceof CompleteBookingCommand) {
            throw new \InvalidArgumentException('Command must be an instance of CompleteBookingCommand');
        }

        $events = $this->eventSourcingService->getEvents(BookingAggregate::AGGREGATE_TYPE, $command->bookingId);

        if (empty($events)) {
            throw new BookingNotFoundException(trace: ['bookingId' => $command->bookingId]);
        }

        $booking = BookingAggregate::replayEvents($events);
        $booking->assertOwnedBy($command->userId);

        $car = $this->cars->findCarById($booking->getCarId())
            ?? throw new CarNotFoundException(trace: ['carId' => $booking->getCarId(), 'when' => 'complete']);

        $actualEndDate = Carbon::now();

        $additionalPrice = $this->pricing->calculateAdditionalPrice(
            pricePerDay: $car->pricePerDay,
            endDate: Carbon::parse($booking->getEndDate()),
            actualEndDate: $actualEndDate
        );

        $booking->complete(
            actualEndDate: $actualEndDate,
            additionalPrice: $additionalPrice,
            finalPrice: $this->pricing->calculateFinalPrice($booking->getOriginalPrice(), $additionalPrice),
            completionNote: $command->completionNote
        );

        $this->eventSourcingService->save($booking);

        return BookingDTO::fromAggregate($booking);
    }
}
