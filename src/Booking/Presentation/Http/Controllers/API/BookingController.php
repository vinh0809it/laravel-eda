<?php

declare(strict_types=1);

namespace Src\Booking\Presentation\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Src\Booking\Application\UseCases\CancelBooking\CancelBookingCommand;
use Src\Booking\Application\UseCases\ChangeBooking\ChangeBookingCommand;
use Src\Booking\Application\UseCases\CompleteBooking\CompleteBookingCommand;
use Src\Booking\Application\UseCases\CreateBooking\CreateBookingCommand;
use Src\Booking\Application\UseCases\GetBookingById\GetBookingByIdQuery;
use Src\Booking\Application\UseCases\GetBookings\GetBookingsQuery;
use Src\Booking\Presentation\Http\Requests\CancelBookingRequest;
use Src\Booking\Presentation\Http\Requests\ChangeBookingRequest;
use Src\Booking\Presentation\Http\Requests\CompleteBookingRequest;
use Src\Booking\Presentation\Http\Requests\CreateBookingRequest;
use Src\Booking\Presentation\Http\Resources\BookingResource;
use Src\Shared\Application\Bus\CommandBus;
use Src\Shared\Application\Bus\QueryBus;
use Src\Shared\Domain\Enums\HttpStatusCode;

final class BookingController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $bookings = $this->queryBus->dispatch(new GetBookingsQuery(
            userId: (string) $request->user()->id,
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 10),
            sortBy: (string) $request->input('sort_by', 'created_at'),
            sortDirection: (string) $request->input('sort_direction', 'desc'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            status: $request->input('status'),
        ));

        return response()->json([
            'data' => BookingResource::collection($bookings->items()),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
                'last_page' => $bookings->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $bookingId): JsonResponse
    {
        $this->assertUuid($bookingId);

        $booking = $this->queryBus->dispatch(new GetBookingByIdQuery(
            bookingId: $bookingId,
            userId: (string) $request->user()->id,
        ));

        return $this->respond($booking);
    }

    public function store(CreateBookingRequest $request): JsonResponse
    {
        $booking = $this->commandBus->dispatch(new CreateBookingCommand(
            carId: $request->car_id,
            userId: (string) $request->user()->id,
            startDate: Carbon::parse($request->start_date),
            endDate: Carbon::parse($request->end_date),
        ));

        return $this->respond($booking, HttpStatusCode::CREATED);
    }

    public function update(ChangeBookingRequest $request, string $bookingId): JsonResponse
    {
        $this->assertUuid($bookingId);

        $booking = $this->commandBus->dispatch(new ChangeBookingCommand(
            bookingId: $bookingId,
            userId: (string) $request->user()->id,
            newStartDate: Carbon::parse($request->start_date),
            newEndDate: Carbon::parse($request->end_date),
        ));

        return $this->respond($booking);
    }

    public function complete(CompleteBookingRequest $request, string $bookingId): JsonResponse
    {
        $this->assertUuid($bookingId);

        $booking = $this->commandBus->dispatch(new CompleteBookingCommand(
            bookingId: $bookingId,
            userId: (string) $request->user()->id,
            completionNote: $request->completion_note,
        ));

        return $this->respond($booking);
    }

    public function cancel(CancelBookingRequest $request, string $bookingId): JsonResponse
    {
        $this->assertUuid($bookingId);

        $booking = $this->commandBus->dispatch(new CancelBookingCommand(
            bookingId: $bookingId,
            userId: (string) $request->user()->id,
            cancelReason: $request->cancel_reason,
        ));

        return $this->respond($booking);
    }

    private function respond(mixed $booking, HttpStatusCode $status = HttpStatusCode::OK): JsonResponse
    {
        return (new BookingResource($booking))->response()->setStatusCode($status->value);
    }

    private function assertUuid(string $bookingId): void
    {
        if (!Uuid::isValid($bookingId)) {
            throw new InvalidArgumentException('Invalid booking ID');
        }
    }
}
