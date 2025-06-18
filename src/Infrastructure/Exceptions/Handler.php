<?php

namespace Src\Infrastructure\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Src\Domain\Shared\Exceptions\BussinessException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if($e instanceof BussinessException) {
                Log::error($e->getErrorCode(), [
                    'trace' => $e->getTraceData()
                ]);
                return false;
            }
        });
    }

    /**
     * render
     *
     * @param  mixed $request
     * @param  mixed $e
     * @return void
     */
    public function render($request, Throwable $e)
    {
        if (str_starts_with($request->route()?->getPrefix(), 'api')) {
            return $this->handleApiException($e);
        }

        return parent::render($request, $e);
    }
    
    /**
     * handleApiException
     *
     * @param  mixed $e
     * @return JsonResponse
     */
    private function handleApiException(Throwable $e): JsonResponse
    {
        $statusCode = 500;
        $message = 'An unexpected error occurred';
        $errorCode = 'UNEXPECTED_ERROR';
        $errorData = [];

        if( $e instanceof BussinessException) {
            $statusCode = $e->getCode();
            $errorCode = $e->getErrorCode();
            $message = $e->getMessage();
        }else if ($e instanceof ValidationException) {
            $statusCode = 422;
            $errorCode = 'VALIDATION_ERROR';
            $message = $e->getMessage();
            $errorData = $e->errors();
        }else if ($e instanceof InvalidArgumentException) {
            $statusCode = 422;
            $errorCode = 'INVALID_ARGUMENT';
            $message = $e->getMessage();
        }else if ($e instanceof AuthenticationException) {
            $statusCode = 401;
            $errorCode = 'UNAUTHORIZED';
            $message = $e->getMessage();
        }else {
            Log::error('Unexpected error occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return response()->json([
            'error_code' => $errorCode,
            'msg' => $message,
            'errors' => $errorData
        ], $statusCode);
    }
} 