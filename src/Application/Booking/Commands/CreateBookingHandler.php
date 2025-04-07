<?php

namespace Src\Application\Booking\Commands;

use Src\Domain\Booking\Aggregate\BookingAggregate;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Application\Shared\Interfaces\ICommand;
use Src\Application\Shared\Interfaces\ICommandHandler;
use Src\Domain\Car\Exceptions\CarNotAvailableException;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Booking\Exceptions\BookingConflictException;
use Illuminate\Support\Str;
use Src\Application\Car\DTOs\CarDTO;
use Src\Application\Shared\Traits\ShouldAppendEvent;
use Src\Domain\Shared\Repositories\IEventStoreRepository;

class CreateBookingHandler implements ICommandHandler
{
    use ShouldAppendEvent;

    public function __construct(
        IEventStoreRepository $eventStore,
        private readonly IBookingService $bookingService,
        private readonly ICarService $carService,
        private readonly IPriceService $priceService
    ) {
        $this->setEventStore($eventStore);
    }    

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

        if (!$car->isAvailable()) {
            throw new CarNotAvailableException(
                trace: ['carId' => $command->carId]
            );
        }

        // Validate no booking conflicts
        if ($this->bookingService->isConflictWithOtherBookings(
            $command->carId,
            $command->startDate,
            $command->endDate
        )) {
            throw new BookingConflictException(
                trace: [
                    'carId' => $command->carId,
                    'startDate' => $command->startDate,
                    'endDate' => $command->endDate
                ]
            );
        }

        $carDTO = new CarDTO(
            id: $car->getId(),
            brand: $car->getBrand(),
            model: $car->getModel(),
            year: $car->getYear(),
            pricePerDay: $car->getPricePerDay()
        );
        
        // Calculate total price using PriceService
        $totalPrice = $this->priceService->calculateBookingPrice(
            $carDTO, 
            $command->startDate, 
            $command->endDate
        );

        // Create booking aggregate
        $bookingId = Str::uuid();

        $booking = BookingAggregate::create(
            id: $bookingId,
            carId: $command->carId,
            userId: $command->userId,
            startDate: $command->startDate,
            endDate: $command->endDate,
            totalPrice: $totalPrice
        );

        // Store event in event store
        $this->persistAggregate($booking);
        
        // Return booking aggregate
        return $booking;
    }
}
