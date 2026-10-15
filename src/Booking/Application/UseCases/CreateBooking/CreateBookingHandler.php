<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\CreateBooking;

use Ramsey\Uuid\Uuid;
use Src\Booking\Application\DTOs\BookingDTO;
use Src\Booking\Application\Ports\BookingPricing;
use Src\Booking\Application\Ports\BookingReadRepository;
use Src\Booking\Application\Ports\CarCatalog;
use Src\Booking\Domain\Aggregates\BookingAggregate;
use Src\Booking\Domain\Exceptions\BookingConflictException;
use Src\Booking\Domain\Exceptions\CarNotFoundException;
use Src\Shared\Application\Bus\Command;
use Src\Shared\Application\Bus\CommandHandler;
use Src\Shared\Application\Ports\AggregateRepository;

final readonly class CreateBookingHandler implements CommandHandler
{
    public function __construct(
        private AggregateRepository $eventSourcingService,
        private BookingReadRepository $bookings,
        private CarCatalog $cars,
        private BookingPricing $pricing
    ) {}

    public function handle(Command $command): BookingDTO
    {
        if (!$command instanceof CreateBookingCommand) {
            throw new \InvalidArgumentException('Command must be an instance of CreateBookingCommand');
        }

        $car = $this->cars->findCarById($command->carId)
            ?? throw new CarNotFoundException(trace: ['carId' => $command->carId]);

        if ($this->bookings->hasBookingConflict($command->userId, $command->carId, $command->startDate, $command->endDate)) {
            throw new BookingConflictException(trace: [
                'userId' => $command->userId,
                'carId' => $command->carId,
                'startDate' => $command->startDate->toDateString(),
                'endDate' => $command->endDate->toDateString(),
            ]);
        }

        $originalPrice = $this->pricing->calculateBookingPrice(
            dailyPrice: $car->pricePerDay,
            popularityFee: $car->popularityFee,
            startDate: $command->startDate,
            endDate: $command->endDate
        );

        $booking = BookingAggregate::create(
            id: Uuid::uuid4()->toString(),
            carId: $command->carId,
            userId: $command->userId,
            startDate: $command->startDate,
            endDate: $command->endDate,
            originalPrice: $originalPrice
        );

        $this->eventSourcingService->save($booking);

        return BookingDTO::fromAggregate($booking);
    }
}
