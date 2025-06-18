<?php

declare(strict_types=1);

namespace Tests\Feature\Booking;

use Src\Infrastructure\Car\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Domain\Car\Exceptions\CarNotAvailableException;
use Src\Infrastructure\Booking\Models\Booking;
use Src\Infrastructure\User\ReadModels\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->car = Car::factory()->create([
        'price_per_day' => 100.00
    ]);

    
    $this->token = $this->user->createToken('test-token')->plainTextToken;
});

test('creates a booking successfully', function () {
    // Arrange
    $startDate = now()->startOfDay()->format('Y-m-d H:i:s');
    $endDate = now()->addDays(5)->startOfDay()->format('Y-m-d H:i:s');

    $payload = [
        'car_id' => $this->car->id,
        'start_date' => $startDate,
        'end_date' => $endDate
    ];

    // Act
    $response = $this->withToken($this->token)
        ->postJson('/api/v1/bookings', $payload);

    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'car_id',
                'user_id',
                'start_date',
                'end_date',
                'original_price',
                'status',
                'version'
            ]
        ]);

    // Verify the booking was created with correct data
    $this->assertDatabaseHas('bookings', [
        'car_id' => $this->car->id,
        'user_id' => $this->user->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'original_price' => 500.00,
        'status' => 'created'
    ]);

    // Verify the event was dispatched by checking its side effects
    $this->assertDatabaseHas('cars', [
        'id' => $this->car->id,
        'is_available' => false
    ]);
})
->group('create_booking_integration');

test('fails to create booking with invalid dates', function () {
    // Arrange
    $payload = [
        'car_id' => $this->car->id,
        'start_date' => now()->addDays(5)->format('Y-m-d'),
        'end_date' => now()->addDays(1)->format('Y-m-d')
    ];

    // Act
    $response = $this->withToken($this->token)
        ->postJson('/api/v1/bookings', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['end_date']);

    // Verify no booking was created
    $this->assertDatabaseMissing('bookings', [
        'car_id' => $this->car->id
    ]);
})
->group('create_booking_integration');

test('fails to create booking with unavailable car', function () {
    // Arrange
    $this->car->update(['is_available' => false]);

    $payload = [
        'car_id' => $this->car->id,
        'start_date' => now()->addDays(1)->format('Y-m-d'),
        'end_date' => now()->addDays(5)->format('Y-m-d')
    ];

    // Act
    $response = $this->withToken($this->token)
        ->postJson('/api/v1/bookings', $payload);

    // Assert
    $response->assertStatus(409)
        ->assertJson([
            'error_code' => 'CAR_NOT_AVAILABLE'
        ]);

    // Verify no booking was created
    $this->assertDatabaseMissing('bookings', [
        'car_id' => $this->car->id
    ]);
})
->group('create_booking_integration');

test('fails to create booking with non-existent car', function () {
    // Arrange
    $payload = [
        'car_id' => 'non-existent-id',
        'start_date' => now()->addDays(1)->format('Y-m-d'),
        'end_date' => now()->addDays(5)->format('Y-m-d')
    ];

    // Act
    $response = $this->withToken($this->token)
        ->postJson('/api/v1/bookings', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['car_id']);

    // Verify no booking was created
    $this->assertDatabaseMissing('bookings', [
        'car_id' => 'non-existent-id'
    ]);
})
->group('create_booking_integration');

test('fails to create booking with overlapping dates', function () {
    // Arrange
    // Create an existing booking
    Booking::factory()->created()->create([
        'car_id' => $this->car->id,
        'user_id' => $this->user->id,
        'start_date' => now()->addDays(2)->format('Y-m-d'),
        'end_date' => now()->addDays(4)->format('Y-m-d')
    ]);

    $payload = [
        'car_id' => $this->car->id,
        'user_id' => $this->user->id,
        'start_date' => now()->addDays(1)->format('Y-m-d'),
        'end_date' => now()->addDays(5)->format('Y-m-d')
    ];

    // Act
    $response = $this->withToken($this->token)
        ->postJson('/api/v1/bookings', $payload);

    // Assert
    $response->assertStatus(409)
        ->assertJson([
            'error_code' => 'BOOKING_CONFLICT'
        ]);

    // Verify only one booking exists
    $this->assertDatabaseCount('bookings', 1);
})
->group('create_booking_integration'); 