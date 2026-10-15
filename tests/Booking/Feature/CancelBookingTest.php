<?php

declare(strict_types=1);

namespace Tests\Booking\Feature;

use Carbon\Carbon;
use Src\Car\Infrastructure\Persistence\Eloquent\CarModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Booking\Domain\Aggregates\BookingAggregate;
use Src\Booking\Domain\Enums\BookingStatus;
use Src\Shared\Domain\Enums\HttpStatusCode;
use Src\Shared\Application\Ports\AggregateRepository;
use Src\Identity\Infrastructure\Persistence\Eloquent\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->car = CarModel::factory()->create();

    $this->token = $this->user->createToken('test-token')->plainTextToken;

    $this->bookingId = fakeUuid();

    $aggregate = BookingAggregate::create(
        id: $this->bookingId,
        carId: $this->car->id,
        userId: (string) $this->user->id,
        startDate: Carbon::now()->subDays(3),
        endDate: Carbon::now(),
        originalPrice: faker()->randomFloat(2, 100, 1000)
    );

    app(AggregateRepository::class)->save($aggregate);
});

test('test cancels a booking successfully', function () {
    // Arrange
    $cancelReason = faker()->sentence();

    $payload = [
        'cancel_reason' => $cancelReason
    ];

    $endpoint = '/api/v1/bookings/' . $this->bookingId . '/cancel';

    // Act
    $response = $this->withToken($this->token)->postJson($endpoint, $payload);

    // Assert
    $response->assertStatus(HttpStatusCode::OK->value)
        ->assertJsonStructure([
            'data' => [
                'id',
                'car_id',
                'user_id',
                'start_date',
                'end_date',
                'original_price',
                'canceled_at',
                'cancel_reason',
                'status'
            ]
        ]);

    $this->assertDatabaseHas('bookings', [
        'id' => $this->bookingId,
        'car_id' => $this->car->id,
        'user_id' => $this->user->id,
        'canceled_at' => now()->toDateTimeString(),
        'cancel_reason' => $cancelReason,
        'status' => BookingStatus::CANCELED->value
    ]);

    $this->assertDatabaseHas('cars', [
        'id' => $this->car->id,
        'booked_count' => 0
    ]);
})
->group('cancel_booking_integration');

test('test fails to cancel a booking with wrong booking id', function () {
    // Arrange
    $payload = [
        'cancel_reason' => faker()->sentence()
    ];
    
    $endpoint = '/api/v1/bookings/' . fakeUuid() . '/cancel';

    // Act
    $response = $this->withToken($this->token)->postJson($endpoint, $payload);

    // Assert
    assertErrorResponse(
        response: $response, 
        errorCode: 'BOOKING_NOT_FOUND', 
        msg: 'Booking not found', 
        httpStatusCode: HttpStatusCode::NOT_FOUND->value
    );
})
->group('cancel_booking_integration');

test('forbids cancel of a booking owned by another user', function () {
    $otherToken = User::factory()->create()->createToken('other-token')->plainTextToken;

    $response = $this->withToken($otherToken)->postJson('/api/v1/bookings/' . $this->bookingId . '/cancel', ['cancel_reason' => faker()->sentence()]);

    assertErrorResponse(
        response: $response,
        errorCode: 'BOOKING_ACCESS_DENIED',
        msg: 'You do not have access to this booking',
        httpStatusCode: HttpStatusCode::FORBIDDEN->value
    );
})
->group('cancel_booking_integration');

test('fails to cancel a booking when required fields are missing', function () {
    $response = $this->withToken($this->token)->postJson('/api/v1/bookings/' . $this->bookingId . '/cancel', []);

    $response->assertStatus(HttpStatusCode::UNPROCESSABLE_ENTITY->value)
        ->assertJson(['error_code' => 'VALIDATION_ERROR'])
        ->assertJsonValidationErrors(['cancel_reason'], 'errors');
})
->group('cancel_booking_integration');

test('gets own booking by id', function () {
    $response = $this->withToken($this->token)->getJson('/api/v1/bookings/' . $this->bookingId);

    $response->assertStatus(HttpStatusCode::OK->value)
        ->assertJsonPath('data.id', $this->bookingId)
        ->assertJsonPath('data.user_id', (string) $this->user->id);
})
->group('get_booking_integration');

test('hides a booking owned by another user from GET by id', function () {
    $otherToken = User::factory()->create()->createToken('other-token')->plainTextToken;

    $response = $this->withToken($otherToken)->getJson('/api/v1/bookings/' . $this->bookingId);

    assertErrorResponse(
        response: $response,
        errorCode: 'BOOKING_NOT_FOUND',
        msg: 'Booking not found',
        httpStatusCode: HttpStatusCode::NOT_FOUND->value
    );
})
->group('get_booking_integration');
