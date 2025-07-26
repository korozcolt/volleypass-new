<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SystemMaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Permitir acceso completo a rutas de Filament admin sin verificar mantenimiento
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        // Verificar si el modo mantenimiento está activo
        if (is_maintenance_mode()) {
            // Permitir acceso a API de verificación de estado
            if ($request->is('api/status') || $request->is('up')) {
                return $next($request);
            }

            // Mostrar página de mantenimiento
            return response()->view('maintenance', [
                'message' => maintenance_message(),
                'app_name' => app_name(),
                'app_version' => app_version(),
            ], 503);
        }

        return $next($request);
    }
}
