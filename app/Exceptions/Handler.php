<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $response = [
            'success' => false,
            'message' => 'An error occurred'
        ];

        $status = 500;

        if ($exception instanceof ModelNotFoundException) {
            $status = 404;
            $response['message'] = 'Resource not found';
        } elseif ($exception instanceof NotFoundHttpException) {
            $status = 404;
            $response['message'] = 'Invalid request';
        } elseif ($exception instanceof AuthenticationException) {
            $status = 401;
            $response['message'] = 'Unauthorized';
        } elseif ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
            $response['message'] = $exception->getMessage();
        }

        // Log exception on debug mode
        if (config('app.debug')) {
            $response['error']= $exception->getMessage();
            $response['trace'] = $exception->getTrace();
        }

        return response()->json($response, $status);
    }
}
