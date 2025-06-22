<?php

namespace Src\Application\Booking\UseCases\Commands;

use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Application\Shared\Interfaces\ICommand;
use Src\Application\Shared\Interfaces\ICommandHandler;
use Src\Domain\Car\Exceptions\CarNotAvailableException;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Booking\Exceptions\BookingConflictException;
use Src\Application\Booking\DTOs\BookingResponseDTO;
use Src\Domain\Shared\Services\IEventSourcingService;
use Ramsey\Uuid\Uuid;
use Src\Domain\Car\Snapshots\CarSnapshot;

class CreateBookingCommandHandler implements ICommandHandler
{
    public function __construct(
        private readonly IEventSourcingService $eventSourcingService,
        private readonly IBookingService $bookingService,
        private readonly ICarService $carService,
        private readonly IPriceService $priceService
    ) {}    

    public function handle(ICommand $command): mixed
    {
        if (!$command instanceof CreateBookingCommand) {
            throw new \InvalidArgumentException('Command must be an instance of CreateBookingCommand');
        }

        // Validate car is available
        $car = $this->carService->findCarById($command->carId);

        if (!$car) {
            throw new CarNotFoundException(
                trace: ['carId' => $command->carId]
            );
        }

        if (!$car->isAvailable) {
            throw new CarNotAvailableException(
                trace: ['carId' => $command->carId]
            );
        }

        // Validate no booking conflicts
        if ($this->bookingService->isConflictWithOtherBookings(
            $command->userId,
            $command->startDate,
            $command->endDate
        )) {
            throw new BookingConflictException(
                trace: [
                    'userId' => $command->userId,
                    'startDate' => $command->startDate->toDateString(),
                    'endDate' => $command->endDate->toDateString()
                ]
            );
        }

        $carSnapshot = new CarSnapshot(
            id: $car->id,
            brand: $car->brand,
            model: $car->model,
            year: $car->year,
            pricePerDay: $car->pricePerDay,
            isAvailable: $car->isAvailable
        );
        
        // Calculate original price using PriceService
        $originalPrice = $this->priceService->calculateBookingPrice(
            dailyPrice: $carSnapshot->pricePerDay, 
            startDate: $command->startDate, 
            endDate: $command->endDate
        );

        // Create booking aggregate
        $bookingId = Uuid::uuid4();

        $booking = BookingAggregate::create(
            id: $bookingId,
            carId: $command->carId,
            userId: $command->userId,
            startDate: $command->startDate,
            endDate: $command->endDate,
            originalPrice: $originalPrice
        );

        // Store event in event store
        $this->eventSourcingService->save($booking);
        
        // Return booking response DTO
        $responseDTO = new BookingResponseDTO($booking);
        return $responseDTO->forCreation();
    }
}
