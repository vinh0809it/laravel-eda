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

    $this->bookingId = faker()->uuid();

    $bookingDays = 2;
    $this->newStartDate = fakeDateFromNow();
    $this->newEndDate = fakeDateFromNow($bookingDays);
    $this->newOriginalPrice = $this->car->price_per_day * $bookingDays;

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

test('test changes a booking successfully', function () {
    // Arrange
    $payload = [
        'start_date' => $this->newStartDate,
        'end_date' => $this->newEndDate
    ];

    $endpoint = '/api/v1/bookings/' . $this->bookingId;

    // Act
    $response = $this->withToken($this->token)->patchJson($endpoint, $payload);

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
                'status'
            ]
        ]);

    $this->assertDatabaseHas('bookings', [
        'id' => $this->bookingId,
        'car_id' => $this->car->id,
        'user_id' => $this->user->id,
        'start_date' => $this->newStartDate->startOfDay()->format('Y-m-d H:i:s'),
        'end_date' => $this->newEndDate->startOfDay()->format('Y-m-d H:i:s'),
        'original_price' => $this->newOriginalPrice,
        'status' => BookingStatus::CHANGED->value
    ]);
})
->group('change_booking_integration');

test('test fails to change a booking with wrong booking id', function () {
    // Arrange
    $payload = [
        'start_date' => $this->newStartDate,
        'end_date' => $this->newEndDate
    ];

    $endpoint = '/api/v1/bookings/' . faker()->uuid();

    // Act
    $response = $this->withToken($this->token)->patchJson($endpoint, $payload);

    // Assert
    assertErrorResponse(
        response: $response, 
        errorCode: 'BOOKING_NOT_FOUND', 
        msg: 'Booking not found', 
        httpStatusCode: HttpStatusCode::NOT_FOUND->value
    );
})
->group('change_booking_integration');

test('forbids change of a booking owned by another user', function () {
    $otherToken = User::factory()->create()->createToken('other-token')->plainTextToken;

    $response = $this->withToken($otherToken)->patchJson('/api/v1/bookings/' . $this->bookingId, ['start_date' => $this->newStartDate, 'end_date' => $this->newEndDate]);

    assertErrorResponse(
        response: $response,
        errorCode: 'BOOKING_ACCESS_DENIED',
        msg: 'You do not have access to this booking',
        httpStatusCode: HttpStatusCode::FORBIDDEN->value
    );
})
->group('change_booking_integration');

test('fails to change a booking when required fields are missing', function () {
    $response = $this->withToken($this->token)->patchJson('/api/v1/bookings/' . $this->bookingId, []);

    $response->assertStatus(HttpStatusCode::UNPROCESSABLE_ENTITY->value)
        ->assertJson(['error_code' => 'VALIDATION_ERROR'])
        ->assertJsonValidationErrors(['start_date'], 'errors');
})
->group('change_booking_integration');
