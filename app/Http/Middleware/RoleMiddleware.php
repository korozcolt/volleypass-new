<?php
// app/Http/Middleware/RoleMiddleware.php - CREAR

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Si no hay usuario autenticado
        if (!$user) {
            return response()->json([
                'error' => 'No autenticado',
                'message' => 'Debes iniciar sesión para acceder a este recurso'
            ], 401);
        }

        // Verificar si el usuario tiene alguno de los roles requeridos
        $hasRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            return response()->json([
                'error' => 'Sin permisos',
                'message' => 'No tienes permisos para acceder a este recurso',
                'required_roles' => $roles,
                'user_roles' => $user->getRoleNames()
            ], 403);
        }

        return $next($request);
    }
}
