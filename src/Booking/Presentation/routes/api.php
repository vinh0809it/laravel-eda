<?php

use Illuminate\Support\Facades\Route;
use Src\Booking\Presentation\Http\Controllers\API\BookingController;

Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1')->group(function () {
    Route::get('bookings', [BookingController::class, 'index']);
    Route::get('bookings/{bookingId}', [BookingController::class, 'show']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::patch('bookings/{bookingId}', [BookingController::class, 'update']);
    Route::post('bookings/{bookingId}/complete', [BookingController::class, 'complete']);
    Route::post('bookings/{bookingId}/cancel', [BookingController::class, 'cancel']);
});
