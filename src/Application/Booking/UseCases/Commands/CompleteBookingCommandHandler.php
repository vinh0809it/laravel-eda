<?php

declare(strict_types=1);

namespace Src\Application\Booking\UseCases\Commands;

use Carbon\Carbon;
use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Application\Shared\Interfaces\ICommand;
use Src\Application\Shared\Interfaces\ICommandHandler;
use Src\Application\Booking\DTOs\BookingResponseDTO;
use Src\Application\Pricing\DTOs\AdditionalPriceCalculationDTO;
use Src\Domain\Booking\Exceptions\BookingNotFoundException;
use Src\Domain\Shared\Services\IEventSourcingService;

final class CompleteBookingCommandHandler implements ICommandHandler
{
    public function __construct(
        private readonly IEventSourcingService $eventSourcingService,
        private readonly IBookingService $bookingService,
        private readonly ICarService $carService,
        private readonly IPriceService $priceService
    ) {}

    public function handle(ICommand $command): mixed
    {
        if (!$command instanceof CompleteBookingCommand) {
            throw new \InvalidArgumentException('Command must be an instance of CompleteBookingCommand');
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

        // Price per day from Car
        $pricePerDay = $this->carService->getDailyPrice($booking->getCarId());

        $endDate = Carbon::parse($booking->getEndDate());
        $actualEndDate = Carbon::now();

        $additionalPriceCalculationDTO = new AdditionalPriceCalculationDTO(
            pricePerDay: $pricePerDay,
            endDate: $endDate,
            actualEndDate: $actualEndDate
        );

        // Recalculate final price based on actual end date
        $additionalPrice = $this->priceService->calculateAdditionalPrice(
            $additionalPriceCalculationDTO
        );

        $finalPrice = $this->priceService->calculateFinalPrice(
            bookingPrice: $booking->getOriginalPrice(),
            additionalPrice: $additionalPrice
        );

        // Complete the booking
        $booking->complete(
            actualEndDate: $actualEndDate,
            additionalPrice: $additionalPrice,
            finalPrice: $finalPrice,
            completionNote: $command->completionNote
        );

        // Store event in event store
        $this->eventSourcingService->save($booking);

        $responseDTO = new BookingResponseDTO($booking);
        return $responseDTO->forCompletion();
    }
} 