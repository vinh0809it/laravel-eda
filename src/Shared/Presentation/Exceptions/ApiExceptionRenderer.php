<?php

declare(strict_types=1);

namespace Src\Shared\Presentation\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Src\Shared\Domain\Exceptions\BusinessException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Renders every exception thrown under /api/* as the API error envelope:
 * { error_code, msg, errors }. Web requests fall through to Laravel's default.
 */
final class ApiExceptionRenderer
{
    public function __invoke(Throwable $e, Request $request): ?JsonResponse
    {
        if (!$request->is('api/*')) {
            return null;
        }

        [$status, $errorCode, $message, $errors] = match (true) {
            $e instanceof BusinessException => [$e->getCode(), $e->getErrorCode(), $e->getMessage(), []],
            $e instanceof ValidationException => [422, 'VALIDATION_ERROR', $e->getMessage(), $e->errors()],
            $e instanceof InvalidArgumentException => [422, 'INVALID_ARGUMENT', $e->getMessage(), []],
            $e instanceof AuthenticationException => [401, 'UNAUTHORIZED', $e->getMessage(), []],
            $e instanceof HttpExceptionInterface => [$e->getStatusCode(), 'HTTP_ERROR', $e->getMessage() ?: 'HTTP error', []],
            default => $this->unexpected($e),
        };

        return response()->json([
            'error_code' => $errorCode,
            'msg' => $message,
            'errors' => $errors,
        ], $status);
    }

    private function unexpected(Throwable $e): array
    {
        Log::error('Unexpected error occurred', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return [500, 'UNEXPECTED_ERROR', 'An unexpected error occurred', []];
    }
}
