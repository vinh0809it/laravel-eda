<?php

declare(strict_types=1);

namespace Tests\Booking\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Shared\Domain\Enums\HttpStatusCode;
use Src\Identity\Infrastructure\Persistence\Eloquent\User;

uses(RefreshDatabase::class);

test('returns 401 envelope without a token', function () {
    $response = $this->getJson('/api/v1/bookings');

    assertErrorResponse(
        response: $response,
        errorCode: 'UNAUTHORIZED',
        msg: 'Unauthenticated.',
        httpStatusCode: HttpStatusCode::UNAUTHORIZED->value
    );
});

test('returns 422 envelope for a malformed booking id', function () {
    $token = User::factory()->create()->createToken('t')->plainTextToken;

    $response = $this->withToken($token)->getJson('/api/v1/bookings/not-a-uuid');

    assertErrorResponse(
        response: $response,
        errorCode: 'INVALID_ARGUMENT',
        msg: 'Invalid booking ID',
        httpStatusCode: HttpStatusCode::UNPROCESSABLE_ENTITY->value
    );
});

test('returns 404 envelope for an unknown api route', function () {
    $response = $this->getJson('/api/v1/does-not-exist');

    $response->assertStatus(HttpStatusCode::NOT_FOUND->value)
        ->assertJson(['error_code' => 'HTTP_ERROR']);
});
