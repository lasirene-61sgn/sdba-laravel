<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle authentication exceptions
        if ($exception instanceof AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            
            // Redirect to appropriate login page based on guard
            $guards = $exception->guards();
            $guard = isset($guards[0]) ? $guards[0] : null;
            
            switch ($guard) {
                case 'admin':
                    return redirect()->guest(route('admin.login'));
                case 'customer':
                    return redirect()->guest(route('customer.login'));
                case 'superadmin':
                    return redirect()->guest(route('superadmin.login'));
                default:
                    return redirect()->guest(route('admin.login'));
            }
        }

        // Handle HTTP exceptions (403, 404, etc.)
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'An error occurred',
                    'message' => $exception->getMessage()
                ], $statusCode);
            }
            
            // Show custom error pages for specific status codes
            switch ($statusCode) {
                case 403:
                    return response()->view('errors.403', [], 403);
                case 404:
                    return response()->view('errors.404', [], 404);
                case 500:
                case 503:
                    return response()->view('errors.general', [], $statusCode);
            }
        }

        // For all other exceptions in production, show general error page
        if (app()->environment('production') && !$request->expectsJson()) {
            return response()->view('errors.general', [], 500);
        }

        return parent::render($request, $exception);
    }
}