<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar autenticación Sanctum
        if (!auth('sanctum')->check()) {
            return response()->json([
                'error' => 'Token de acceso requerido',
                'message' => 'Debes autenticarte para acceder a este endpoint'
            ], 401);
        }

        $user = auth('sanctum')->user();
        
        // Verificar roles requeridos
        $hasRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            return response()->json([
                'error' => 'Permisos insuficientes',
                'message' => 'No tienes permisos para acceder a este recurso',
                'required_roles' => $roles,
                'user_roles' => $user->getRoleNames()
            ], 403);
        }

        return $next($request);
    }
}