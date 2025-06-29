<?php

namespace Src\Application\Booking\UseCases\Commands;

use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Application\Shared\Interfaces\ICommand;
use Src\Application\Shared\Interfaces\ICommandHandler;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Booking\Exceptions\BookingConflictException;
use Src\Application\Booking\DTOs\BookingResponseDTO;
use Src\Domain\Shared\Services\IEventSourcingService;
use Ramsey\Uuid\Uuid;

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
        $carSnapshot = $this->carService->findCarById($command->carId);

        if (!$carSnapshot) {
            throw new CarNotFoundException(
                trace: ['carId' => $command->carId]
            );
        }

        // Validate no booking conflicts
        if ($this->bookingService->hasBookingConflict(
            $command->userId,
            $command->carId,
            $command->startDate,
            $command->endDate
        )) {
            throw new BookingConflictException(
                trace: [
                    'userId' => $command->userId,
                    'carId' => $command->carId,
                    'startDate' => $command->startDate->toDateString(),
                    'endDate' => $command->endDate->toDateString()
                ]
            );
        }

        // Calculate original price using PriceService
        $originalPrice = $this->priceService->calculateBookingPrice(
            dailyPrice: $carSnapshot->pricePerDay,
            popularityFee: $carSnapshot->popularityFee,
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
