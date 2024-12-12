<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up', // Optional health check endpoint
    )
    ->withMiddleware(function (Middleware $middleware) {
        return [
            // Middleware for API routes
            'api' => [
                \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
                'throttle:api',
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ],
            // Custom middleware groups
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ];
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configure global exception handling here if needed
        $exceptions->map(\Throwable::class, function ($e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        });
    })->create();

