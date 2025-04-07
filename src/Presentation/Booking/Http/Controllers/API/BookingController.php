<?php

namespace Src\Presentation\Booking\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Src\Application\Booking\Commands\CreateBookingCommand;
use Src\Application\Shared\Bus\CommandBus;
use Src\Presentation\Booking\Http\Requests\CreateBookingRequest;
use Src\Application\Booking\DTOs\BookingResponseDTO;

class BookingController extends Controller
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function store(CreateBookingRequest $request): JsonResponse
    {
        $command = new CreateBookingCommand(
            carId: $request->car_id,
            userId: Auth::id(),
            startDate: $request->start_date,
            endDate: $request->end_date,
        );

        $booking = $this->commandBus->dispatch($command);
        
        $responseDTO = new BookingResponseDTO($booking);

        return response()->json([
            'data' => $responseDTO->forCreation(),
        ]);
    }
} 