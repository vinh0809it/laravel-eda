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
    $this->car = Car::factory()->create([
        'booked_count' => 10
    ]);

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

    app(EventSourcingService::class)->save($aggregate);
});

test('test completes a booking successfully', function () {
    // Arrange
    $payload = [
        'completion_note' => faker()->sentence()
    ];

    $endpoint = '/api/v1/bookings/' . $this->bookingId . '/complete';

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
                'actual_end_date',
                'original_price',
                'final_price',
                'completion_note',
                'status'
            ]
        ]);

    $this->assertDatabaseHas('bookings', [
        'id' => $this->bookingId,
        'car_id' => $this->car->id,
        'user_id' => $this->user->id,
        'status' => BookingStatus::COMPLETED->value
    ]);

    $expectedPopularityFeeUpdated = $this->car->price_per_day * 0.1;
    
    $this->assertDatabaseHas('cars', [
        'id' => $this->car->id,
        'last_booking_completed_at' => now()->toDateTimeString(),
        'popularity_fee' => $expectedPopularityFeeUpdated
    ]);
})
->group('complete_booking_integration');

test('test fails to complete a booking with wrong booking id', function () {
    // Arrange
    $payload = [
        'completion_note' => faker()->sentence()
    ];
    
    $endpoint = '/api/v1/bookings/' . fakeUuid() . '/complete';

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
->group('complete_booking_integration');
