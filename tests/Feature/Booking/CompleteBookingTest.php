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

use function Laravel\Prompts\error;

uses(RefreshDatabase::class);

function getApiEndpoint(string $bookingId): string
{
    return '/api/v1/booking/' . $bookingId . '/complete';
}
beforeEach(function () {
    $this->faker = \Faker\Factory::create();

    $this->user = User::factory()->create();
    $this->car = Car::factory()->create();

    $this->token = $this->user->createToken('test-token')->plainTextToken;

    $this->bookingId = $this->faker->uuid();

    $aggregate = BookingAggregate::create(
        id: $this->bookingId,
        carId: $this->car->id,
        userId: (string) $this->user->id,
        startDate: Carbon::now()->subDays(3)->toDateString(),
        endDate: Carbon::now()->toDateString(),
        originalPrice: $this->faker->randomFloat(2, 100, 1000)
    );

    app(EventSourcingService::class)->save($aggregate);
});

test('test completes a booking successfully', function () {
    // Arrange
    $payload = [];
    $endpoint = getApiEndpoint($this->bookingId);
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
                'status'
            ]
        ]);

    $this->assertDatabaseHas('bookings', [
        'id' => $this->bookingId,
        'car_id' => $this->car->id,
        'user_id' => $this->user->id,
        'status' => BookingStatus::COMPLETED->value
    ]);

    $this->assertDatabaseHas('cars', [
        'id' => $this->car->id,
        'is_available' => true
    ]);
})
->group('complete_booking_integration');

test('test fails to complete a booking with wrong booking id', function () {
    // Arrange
    $payload = [
        'car_id' => $this->car->id,
        'start_date' => now()->addDays(5)->format('Y-m-d'),
        'end_date' => now()->addDays(1)->format('Y-m-d')
    ];

    $endpoint = getApiEndpoint($this->faker->uuid());

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
