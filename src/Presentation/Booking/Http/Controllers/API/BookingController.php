<?php

namespace Src\Presentation\Booking\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Guid\Guid;
use Src\Application\Booking\UseCases\Commands\CreateBookingCommand;
use Src\Application\Shared\Bus\CommandBus;
use Src\Application\Shared\Bus\QueryBus;
use Src\Presentation\Booking\Http\Requests\CreateBookingRequest;
use Src\Application\Booking\DTOs\BookingResponseDTO;
use Src\Application\Booking\UseCases\Queries\GetBookingsQuery;
use InvalidArgumentException;

class BookingController extends Controller
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {}

    public function index(?string $bookingId = null): JsonResponse
    {
        if ($bookingId && !Guid::isValid($bookingId)) {
            throw new InvalidArgumentException('Invalid booking ID');
        }

        $query = new GetBookingsQuery(
            bookingId: $bookingId,
            page: request()->input('page', 1),
            perPage: request()->input('per_page', 10),
            sortBy: request()->input('sort_by', 'created_at'),
            sortDirection: request()->input('sort_direction', 'desc'),
            startDate: request()->input('start_date'),
            endDate: request()->input('end_date'),
            status: request()->input('status'),
            userId: Auth::id()
        );

        $bookings = $this->queryBus->dispatch($query);

        return response()->json([
            'data' => $bookings->items(),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
                'last_page' => $bookings->lastPage(),
            ],
        ]);
    }

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
        ], 201);
    }
} 