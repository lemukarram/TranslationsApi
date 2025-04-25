<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Custom reporting logic if needed
        });

        // Handle API JSON responses
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return $this->handleApiException($request, $e);
            }
        });
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'status' => false,
            'message' => 'Unauthenticated',
            'errors' => 'Authentication token is missing or invalid'
        ], 401);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'status' => false,
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    /**
     * Handle API exceptions
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleApiException($request, Throwable $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => false,
                'message' => 'Resource not found'
            ], 404);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'status' => false,
                'message' => 'Endpoint not found'
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => false,
                'message' => 'Method not allowed for this endpoint'
            ], 405);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return response()->json([
                'status' => false,
                'message' => 'Too many requests',
                'retry_after' => $exception->getHeaders()['Retry-After'] ?? null
            ], 429);
        }

        if ($exception instanceof UnauthorizedHttpException) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 401);
        }

        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1] ?? null;
            
            // Handle duplicate entry
            if ($errorCode == 1062) {
                return response()->json([
                    'status' => false,
                    'message' => 'Duplicate entry',
                    'errors' => 'This record already exists'
                ], 409);
            }
            
            // Handle other database errors
            return response()->json([
                'status' => false,
                'message' => 'Database error occurred',
                'errors' => config('app.debug') ? $exception->getMessage() : 'Please try again later'
            ], 500);
        }

        // Default error response
        $statusCode = method_exists($exception, 'getStatusCode') 
            ? $exception->getStatusCode() 
            : 500;

        $response = [
            'status' => false,
            'message' => $exception->getMessage()
        ];

        // Add debug info if in development
        if (config('app.debug')) {
            $response['exception'] = get_class($exception);
            $response['trace'] = $exception->getTrace();
        }

        return response()->json($response, $statusCode);
    }

    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => method_exists($e, 'errors') ? $e->errors() : null,
            ], $this->getStatusCode($e));
        }

        return parent::render($request, $e);
    }

    protected function getStatusCode(Throwable $e)
    {
        if (method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }
        
        return $e instanceof ValidationException ? 422 : 500;
    }
}