<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Src\Identity\Presentation\Http\Controllers\ApiAuthController;

Route::middleware('api')->prefix('api')->group(function () {
    Route::post('register', [ApiAuthController::class, 'register']);
    Route::post('login', [ApiAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::get('user', fn (Request $request) => response()->json($request->user()));
    });
});
