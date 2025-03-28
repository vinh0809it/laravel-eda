<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Src\Presentation\Auth\Http\Controllers\ApiAuthController;

Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
});

Route::post('/login', [ApiAuthController::class, 'login']);