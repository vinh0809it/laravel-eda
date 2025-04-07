<?php

namespace Src\Presentation\Booking\Http\Controllers\Admin;

use Illuminate\Bus\Dispatcher;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Src\Application\Booking\Commands\CreateBookingCommand;
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
            startDate: $request->start_date,
            endDate: $request->end_date,
        );

        $booking = $this->bus->dispatch($command);

        return Inertia::render('Bookings/Show', [
            'booking' => [
                'id' => $booking->getId(),
                'car_id' => $booking->getCarId(),
                'start_date' => $booking->getStartDate(),
                'end_date' => $booking->getEndDate(),
                'total_price' => $booking->getTotalPrice(),
                'status' => $booking->getStatus(),
            ],
        ]);
    }
} 