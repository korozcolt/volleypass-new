<?php

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
        // Middleware global para aplicar configuraciones del sistema
        $middleware->web(append: [
            \App\Http\Middleware\ApplySystemConfigMiddleware::class,
            \App\Http\Middleware\SystemMaintenanceMiddleware::class,
        ]);

        // Middleware para API
        $middleware->api(append: [
            \App\Http\Middleware\ApplySystemConfigMiddleware::class,
        ]);

        // Alias de middleware
        $middleware->alias([
            'system.config' => \App\Http\Middleware\ApplySystemConfigMiddleware::class,
            'system.maintenance' => \App\Http\Middleware\SystemMaintenanceMiddleware::class,
            'role.redirect' => \App\Http\Middleware\RedirectBasedOnRole::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'check.admin.panel.access' => \App\Http\Middleware\CheckAdminPanelAccess::class,
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
