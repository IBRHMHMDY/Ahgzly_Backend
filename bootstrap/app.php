<?php

use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => Authenticate::class, // ✅ مهم
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return \App\Support\ApiResponse::error(
                    'Validation error',
                    422,
                    $e->errors(),
                    'validation_error'
                );
            }

            return null;
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return \App\Support\ApiResponse::error('Unauthenticated', 401, null, 'unauthenticated');
            }

            return null;
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return \App\Support\ApiResponse::error('Forbidden', 403, null, 'forbidden');
            }

            return null;
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return \App\Support\ApiResponse::error('Resource not found', 404, null, 'not_found');
            }

            return null;
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return \App\Support\ApiResponse::error('Route not found', 404, null, 'route_not_found');
            }

            return null;
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return \App\Support\ApiResponse::error('Method not allowed', 405, null, 'method_not_allowed');
            }

            return null;
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $status = $e->getStatusCode() ?: 400;

                return \App\Support\ApiResponse::error($e->getMessage() ?: 'HTTP error', $status, null, 'http_error');
            }

            return null;
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $message = app()->hasDebugModeEnabled()
                    ? $e->getMessage()
                    : 'Server error';

                return \App\Support\ApiResponse::error($message, 500, null, 'server_error');
            }

            return null;
        });
    })->create();
