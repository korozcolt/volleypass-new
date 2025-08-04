<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware global para aplicar configuraciones del sistema
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\ApplySystemConfigMiddleware::class,
            \App\Http\Middleware\SystemMaintenanceMiddleware::class,
            \App\Http\Middleware\EnsureSetupCompleted::class,
        ]);

        // Middleware para API
        $middleware->api(append: [
            \App\Http\Middleware\ApplySystemConfigMiddleware::class,
        ]);

        // Alias de middleware
        $middleware->alias([
            'system.config' => \App\Http\Middleware\ApplySystemConfigMiddleware::class,
            'system.maintenance' => \App\Http\Middleware\SystemMaintenanceMiddleware::class,
            'admin.access' => \App\Http\Middleware\CheckAdminPanelAccess::class,
            'api.role' => \App\Http\Middleware\ApiRoleMiddleware::class,
            'filter.navigation' => \App\Http\Middleware\FilterFilamentNavigation::class,
            'role.redirection' => \App\Http\Middleware\RoleRedirectionMiddleware::class,
            'post.login.redirect' => \App\Http\Middleware\PostLoginRoleRedirection::class,
            'setup.completed' => \App\Http\Middleware\EnsureSetupCompleted::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejo básico de excepciones API
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Datos inválidos',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'No autenticado'
                ], 401);
            }
        });
    })->create();
