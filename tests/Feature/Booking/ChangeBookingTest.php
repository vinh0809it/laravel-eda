<?php

declare(strict_types=1);

namespace Tests\Feature\Booking;

use Carbon\Carbon;
use Src\Infrastructure\Car\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Shared\Enums\HttpStatusCode;
use Src\Domain\Shared\Services\EventSourcingService;
use Src\Infrastructure\User\ReadModels\User;

uses(RefreshDatabase::class);

beforeEach(function () {

    $this->user = User::factory()->create();
    $this->car = Car::factory()->create();

    $this->token = $this->user->createToken('test-token')->plainTextToken;

    $this->bookingId = fakeUuid();

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

    app(EventSourcingService::class)->save($aggregate);
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

    $this->assertDatabaseHas('cars', [
        'id' => $this->car->id,
        'is_available' => false
    ]);
})
->group('change_booking_integration');

test('test fails to change a booking with wrong booking id', function () {
    // Arrange
    $payload = [
        'start_date' => $this->newStartDate,
        'end_date' => $this->newEndDate
    ];

    $endpoint = '/api/v1/bookings/' . fakeUuid();

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
