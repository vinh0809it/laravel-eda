<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use Illuminate\Testing\TestResponse;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function mockEventDispatcher()
{
    $dispatcher = mock(\Illuminate\Contracts\Events\Dispatcher::class);
    $dispatcher->shouldReceive('dispatch')->andReturnUsing(function ($event) {
        return $event;
    });
    app()->instance('events', $dispatcher);
    return $dispatcher;
}

function generateBookingDates(Faker\Generator $faker, int $days = 5): array {
    $start = $faker->dateTimeBetween('now', '+1 week');
    $end = (clone $start)->modify("+{$days} days");
    return [
        'start' => $start->format('Y-m-d'),
        'end' => $end->format('Y-m-d'),
    ];
}

function randomMoney(Faker\Generator $faker, int $from = 100, int $to = 1000): float {
    return $faker->randomFloat(2, $from, $to);
}

function assertErrorResponse(TestResponse $response, ?string $errorCode, ?string $msg, int $httpStatusCode = 500) {
    
    $response->assertStatus($httpStatusCode)
            ->assertJsonStructure([
                'error_code',
                'msg'
            ])
            ->assertJson([
                'error_code' => $errorCode,
                'msg' => $msg
            ]);
}