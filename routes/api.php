<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Src\Presentation\Auth\Http\Controllers\ApiAuthController;
use Src\Presentation\Booking\Http\Controllers\API\BookingController;

Route::middleware('api')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return response()->json($request->user());
        });

        Route::prefix('v1')->group(function () {
            Route::resource('bookings', BookingController::class);
        });
        
    });
});

Route::post('/login', [ApiAuthController::class, 'login']);

