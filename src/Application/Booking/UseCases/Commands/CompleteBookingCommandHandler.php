<?php

declare(strict_types=1);

namespace Src\Application\Booking\UseCases\Commands;

use Carbon\Carbon;
use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Shared\Repositories\IEventStoreRepository;
use Src\Application\Shared\Traits\ShouldAppendEvent;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Application\Shared\Interfaces\ICommand;
use Src\Application\Shared\Interfaces\ICommandHandler;
use Src\Application\Booking\DTOs\BookingResponseDTO;
use Src\Application\Pricing\DTOs\AdditionalPriceCalculationDTO;
use Src\Domain\Booking\Exceptions\BookingNotFoundException;
use Src\Domain\Car\Exceptions\CarNotFoundException;

final class CompleteBookingCommandHandler implements ICommandHandler
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
        if (!$command instanceof CompleteBookingCommand) {
            throw new \InvalidArgumentException('Command must be an instance of CompleteBookingCommand');
        }

        $events = $this->loadEventStore(
            aggregateType: BookingAggregate::AGGREGATE_TYPE,
            aggregateId: $command->bookingId
        );

        if (empty($events)) {
            throw new BookingNotFoundException(
                trace: ['bookingId' => $command->bookingId]
            );
        }

        $booking = BookingAggregate::replayEvents($events);

        // Get car details
        $car = $this->carService->findCarById($booking->getCarId());

        if (!$car) {
            throw new CarNotFoundException(
                trace: ['carId' => $booking->getCarId()]
            );
        }

        $endDate = Carbon::parse($booking->getEndDate());
        $actualEndDate = Carbon::now();

        $additionalPriceCalculationDTO = new AdditionalPriceCalculationDTO(
            pricePerDay: $car->pricePerDay,
            endDate: $endDate,
            actualEndDate: $actualEndDate
        );

        // Recalculate final price based on actual end date
        $additionalPrice = $this->priceService->calculateAdditionalPrice(
            $additionalPriceCalculationDTO
        );

        $finalPrice = $this->priceService->calculateAdditionalPrice(
            $additionalPriceCalculationDTO
        );

        // Complete the booking
        $booking->complete(
            actualEndDate: $actualEndDate->format('Y-m-d'),
            additionalPrice: $additionalPrice,
            finalPrice: $finalPrice
        );

        // Store event in event store
        $this->persistAggregate($booking);

        $responseDTO = new BookingResponseDTO($booking);
        return $responseDTO->forCompletion();
    }
} 