<?php

namespace Src\Presentation\Booking\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Bus\Dispatcher;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Src\Application\Booking\UseCases\Commands\CreateBookingCommand;
use Src\Presentation\Booking\Http\Requests\CreateBookingRequest;

class BookingController extends Controller
{
    public function __construct(
        private readonly Dispatcher $bus
    ) {}

    public function store(CreateBookingRequest $request)
    {
        $command = new CreateBookingCommand(
            carId: $request->car_id,
            userId: Auth::id(),
            startDate: Carbon::parse($request->start_date),
            endDate: Carbon::parse($request->end_date),
        );

        $booking = $this->bus->dispatch($command);

        return Inertia::render('Bookings/Show', [
            'booking' => [
                'id' => $booking->getId(),
                'car_id' => $booking->getCarId(),
                'start_date' => $booking->getStartDate(),
                'end_date' => $booking->getEndDate(),
                'original_price' => $booking->getOriginalPrice(),
                'status' => $booking->getStatus(),
            ],
        ]);
    }
} 