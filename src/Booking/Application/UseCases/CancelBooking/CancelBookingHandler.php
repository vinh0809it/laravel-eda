<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\CancelBooking;

use Carbon\Carbon;
use Src\Booking\Application\DTOs\BookingDTO;
use Src\Booking\Domain\Aggregates\BookingAggregate;
use Src\Booking\Domain\Exceptions\BookingNotFoundException;
use Src\Shared\Application\Bus\Command;
use Src\Shared\Application\Bus\CommandHandler;
use Src\Shared\Application\Ports\AggregateRepository;

final readonly class CancelBookingHandler implements CommandHandler
{
    public function __construct(
        private AggregateRepository $eventSourcingService
    ) {}

    public function handle(Command $command): BookingDTO
    {
        if (!$command instanceof CancelBookingCommand) {
            throw new \InvalidArgumentException('Command must be an instance of CancelBookingCommand');
        }

        $events = $this->eventSourcingService->getEvents(BookingAggregate::AGGREGATE_TYPE, $command->bookingId);

        if (empty($events)) {
            throw new BookingNotFoundException(trace: ['bookingId' => $command->bookingId]);
        }

        $booking = BookingAggregate::replayEvents($events);
        $booking->assertOwnedBy($command->userId);

        $booking->cancel(canceledAt: Carbon::now(), cancelReason: $command->cancelReason);

        $this->eventSourcingService->save($booking);

        return BookingDTO::fromAggregate($booking);
    }
}
