<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Src\Shared\Domain\Exceptions\BusinessException;
use Src\Shared\Presentation\Exceptions\ApiExceptionRenderer;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Modules register their own API routes, so the `api` group (and its
        // throttle) is enabled here instead of through withRouting(api: ...).
        $middleware->throttleApi();

        $middleware->web(append: [
            \Src\Shared\Presentation\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(new ApiExceptionRenderer());

        // Business errors are expected: log them compactly and skip the default stack trace report
        $exceptions->report(function (BusinessException $e) {
            Log::error($e->getErrorCode(), ['trace' => $e->getTraceData()]);
        })->stop();
    })->create();
