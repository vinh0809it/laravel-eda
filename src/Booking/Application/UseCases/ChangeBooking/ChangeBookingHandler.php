<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\ChangeBooking;

use Src\Booking\Application\DTOs\BookingDTO;
use Src\Booking\Application\Ports\BookingPricing;
use Src\Booking\Application\Ports\BookingReadRepository;
use Src\Booking\Application\Ports\CarCatalog;
use Src\Booking\Domain\Aggregates\BookingAggregate;
use Src\Booking\Domain\Exceptions\BookingConflictException;
use Src\Booking\Domain\Exceptions\BookingNotFoundException;
use Src\Booking\Domain\Exceptions\CarNotFoundException;
use Src\Shared\Application\Bus\Command;
use Src\Shared\Application\Bus\CommandHandler;
use Src\Shared\Application\Ports\AggregateRepository;

final readonly class ChangeBookingHandler implements CommandHandler
{
    public function __construct(
        private AggregateRepository $eventSourcingService,
        private BookingReadRepository $bookings,
        private CarCatalog $cars,
        private BookingPricing $pricing
    ) {}

    public function handle(Command $command): BookingDTO
    {
        if (!$command instanceof ChangeBookingCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ChangeBookingCommand');
        }

        $events = $this->eventSourcingService->getEvents(BookingAggregate::AGGREGATE_TYPE, $command->bookingId);

        if (empty($events)) {
            throw new BookingNotFoundException(trace: ['bookingId' => $command->bookingId]);
        }

        $booking = BookingAggregate::replayEvents($events);
        $booking->assertOwnedBy($command->userId);

        // The booking's own row is excluded from the overlap check
        if ($this->bookings->hasBookingConflict(
            userId: $booking->getUserId(),
            carId: $booking->getCarId(),
            startDate: $command->newStartDate,
            endDate: $command->newEndDate,
            excludeBookingId: $booking->getId()
        )) {
            throw new BookingConflictException(trace: [
                'bookingId' => $booking->getId(),
                'startDate' => $command->newStartDate->toDateString(),
                'endDate' => $command->newEndDate->toDateString(),
            ]);
        }

        $car = $this->cars->findCarById($booking->getCarId())
            ?? throw new CarNotFoundException(trace: ['carId' => $booking->getCarId()]);

        $booking->change(
            newStartDate: $command->newStartDate,
            newEndDate: $command->newEndDate,
            newOriginalPrice: $this->pricing->calculateBookingPrice(
                dailyPrice: $car->pricePerDay,
                popularityFee: $car->popularityFee,
                startDate: $command->newStartDate,
                endDate: $command->newEndDate
            )
        );

        $this->eventSourcingService->save($booking);

        return BookingDTO::fromAggregate($booking);
    }
}
