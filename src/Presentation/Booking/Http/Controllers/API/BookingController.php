<?php

namespace Src\Presentation\Booking\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Src\Application\Booking\UseCases\Commands\CreateBookingCommand;
use Src\Application\Shared\Bus\CommandBus;
use Src\Application\Shared\Bus\QueryBus;
use Src\Presentation\Booking\Http\Requests\CreateBookingRequest;
use Src\Application\Booking\UseCases\Queries\GetBookingsQuery;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Src\Application\Booking\UseCases\Commands\CancelBookingCommand;
use Src\Application\Booking\UseCases\Commands\ChangeBookingCommand;
use Src\Application\Booking\UseCases\Commands\CompleteBookingCommand;
use Src\Domain\Shared\Enums\HttpStatusCode;
use Src\Presentation\Booking\Http\Requests\ChangeBookingRequest;

final class BookingController extends Controller
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {}

    /**
     * @param string|null $bookingId
     * 
     * @return JsonResponse
     */
    public function index(?string $bookingId = null): JsonResponse
    {
        if ($bookingId && !Uuid::isValid($bookingId)) {
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
    
    /**
     * @param string $bookingId
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function complete(string $bookingId, Request $request): JsonResponse
    {
        if (!Uuid::isValid($bookingId)) {
            throw new InvalidArgumentException('Invalid booking ID');
        }

        $command = new CompleteBookingCommand(
            bookingId: $bookingId,
            completionNote: $request->completion_note
        );

        $response = $this->commandBus->dispatch($command);

        return response()->json([
            'data' => $response
        ]);
    }

    /**
     * @param string $bookingId
     * @param ChangeBookingRequest $request
     * 
     * @return JsonResponse
     */
    public function update(string $bookingId, ChangeBookingRequest $request): JsonResponse
    {
        if (!Uuid::isValid($bookingId)) {
            throw new InvalidArgumentException('Invalid booking ID');
        }

        $command = new ChangeBookingCommand(
            bookingId: $bookingId,
            newStartDate: Carbon::parse($request->start_date),
            newEndDate: Carbon::parse($request->end_date),
        );

        $response = $this->commandBus->dispatch($command);
        
        return response()->json([
            'data' => $response
        ]);
    }

    /**
     * @param CreateBookingRequest $request
     * 
     * @return JsonResponse
     */
    public function store(CreateBookingRequest $request): JsonResponse
    {
        $command = new CreateBookingCommand(
            carId: $request->car_id,
            userId: Auth::id(),
            startDate: Carbon::parse($request->start_date),
            endDate: Carbon::parse($request->end_date),
        );

        $response = $this->commandBus->dispatch($command);
        
        return response()->json([
            'data' => $response
        ], HttpStatusCode::CREATED->value);
    }

    /**
     * @param string $bookingId
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function cancel(string $bookingId, Request $request): JsonResponse
    {
        if (!Uuid::isValid($bookingId)) {
            throw new InvalidArgumentException('Invalid booking ID');
        }

        $command = new CancelBookingCommand(
            bookingId: $bookingId,
            cancelReason: $request->cancel_reason
        );

        $response = $this->commandBus->dispatch($command);

        return response()->json([
            'data' => $response
        ]);
    }
} 