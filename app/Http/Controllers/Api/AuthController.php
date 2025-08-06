<?php
// app/Http/Controllers/Api/AuthController.php - VERSIÓN FINAL CORREGIDA

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Authentication"},
     *     summary="Login para verificadores móviles",
     *     description="Autenticación de usuarios con permisos de verificador, administrador de liga o super administrador",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "device_name"},
     *             @OA\Property(property="email", type="string", format="email", example="verifier@volleypass.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="device_name", type="string", example="iPhone 15 Pro")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="1|abcdef123456..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=2592000),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="email", type="string", example="verifier@volleypass.com"),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"Verifier"}),
     *                 @OA\Property(property="abilities", type="array", @OA\Items(type="string")),
     *                 @OA\Property(
     *                     property="profile",
     *                     type="object",
     *                     @OA\Property(property="avatar_url", type="string", nullable=true),
     *                     @OA\Property(property="phone", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Credenciales inválidas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Demasiados intentos de login",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Demasiados intentos de login. Intente en 300 segundos.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Rate limiting por IP
            $clientIp = $request->getClientIp() ?? $request->ip() ?? '127.0.0.1';
            $key = 'login-attempts:' . $clientIp;
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'error' => 'Demasiados intentos de login. Intente en ' .
                              RateLimiter::availableIn($key) . ' segundos.'
                ], 429);
            }

            $credentials = $request->validated();

            // Buscar usuario con permisos de verificador
            $user = User::where('email', $credentials['email'])
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['Verifier', 'LeagueAdmin', 'SuperAdmin']);
                })
                ->where('status', 'active')
                ->first();

            // Validar credenciales
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                RateLimiter::hit($key, 300); // 5 minutos penalty

                Log::warning('Failed API login attempt', [
                    'email' => $credentials['email'],
                    'ip' => $clientIp,
                    'user_agent' => $request->header('User-Agent') ?? 'Unknown'
                ]);

                return response()->json([
                    'error' => 'Credenciales inválidas'
                ], 401);
            }

            // Revocar tokens anteriores del mismo dispositivo
            $user->tokens()
                ->where('name', $credentials['device_name'])
                ->delete();

            // Crear token con abilities específicas
            $abilities = $this->getTokenAbilities($user);
            $token = $user->createToken(
                $credentials['device_name'],
                $abilities,
                now()->addDays(30) // Expiración 30 días
            );

            // Log successful login
            Log::info('Successful API login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'device' => $credentials['device_name'],
                'ip' => $clientIp
            ]);

            // Clear rate limiting on success
            RateLimiter::clear($key);

            return response()->json([
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_in' => 30 * 24 * 60 * 60, // 30 días en segundos
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'abilities' => $abilities,
                    'profile' => [
                        'avatar_url' => $user->profile?->avatar_url,
                        'phone' => $user->profile?->phone,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            $clientIp = $request->getClientIp() ?? $request->ip() ?? '127.0.0.1';
            Log::error('API login error', [
                'error' => $e->getMessage(),
                'email' => $request->get('email', 'unknown'),
                'ip' => $clientIp
            ]);

            return response()->json([
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Authentication"},
     *     summary="Cerrar sesión actual",
     *     description="Revoca el token de acceso actual del usuario",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sesión cerrada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sesión cerrada exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user) {
                // ✅ MÉTODO 1: Usando el token actual directamente
                $currentToken = $user->currentAccessToken();
                $tokenName = $currentToken?->name ?? 'unknown';

                if ($currentToken instanceof PersonalAccessToken) {
                    $currentToken->delete();
                }

                Log::info('API logout successful', [
                    'user_id' => $user->id,
                    'token_name' => $tokenName,
                    'ip' => $request->ip()
                ]);
            }

            return response()->json([
                'message' => 'Sesión cerrada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('API logout error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error cerrando sesión'
            ], 500);
        }
    }

    /**
     * Logout alternativo usando relationships
     * ✅ MÉTODO ALTERNATIVO 100% seguro
     */
    public function logoutSafe(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user) {
                // ✅ MÉTODO 2: Usando relationships (siempre funciona)
                $tokenId = $user->currentAccessToken()?->id;
                $tokenName = $user->currentAccessToken()?->name ?? 'unknown';

                if ($tokenId) {
                    // Eliminar por ID usando relationship
                    $user->tokens()->where('id', $tokenId)->delete();

                    // O usando el modelo directamente
                    PersonalAccessToken::where('id', $tokenId)->delete();
                }

                Log::info('API logout safe successful', [
                    'user_id' => $user->id,
                    'token_name' => $tokenName,
                    'token_id' => $tokenId,
                    'ip' => $request->ip()
                ]);
            }

            return response()->json([
                'message' => 'Sesión cerrada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('API logout safe error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'error' => 'Error cerrando sesión'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout-all",
     *     tags={"Authentication"},
     *     summary="Cerrar sesión en todos los dispositivos",
     *     description="Revoca todos los tokens de acceso del usuario",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sesiones cerradas en todos los dispositivos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sesión cerrada en todos los dispositivos (3 tokens revocados)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user) {
                // ✅ Este método SIEMPRE funciona
                $tokenCount = $user->tokens()->count();
                $user->tokens()->delete();

                Log::info('API logout all devices', [
                    'user_id' => $user->id,
                    'tokens_revoked' => $tokenCount,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'message' => "Sesión cerrada en todos los dispositivos ({$tokenCount} tokens revocados)"
                ]);
            }

            return response()->json([
                'message' => 'No hay sesiones activas'
            ]);

        } catch (\Exception $e) {
            Log::error('API logout all error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'error' => 'Error cerrando sesiones'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/user",
     *     tags={"Authentication"},
     *     summary="Obtener información del usuario autenticado",
     *     description="Retorna la información del usuario actual y detalles del token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Información del usuario",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="email", type="string", example="verifier@volleypass.com"),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"Verifier"}),
     *                 @OA\Property(property="abilities", type="array", @OA\Items(type="string")),
     *                 @OA\Property(
     *                     property="profile",
     *                     type="object",
     *                     @OA\Property(property="avatar_url", type="string", nullable=true),
     *                     @OA\Property(property="phone", type="string", nullable=true)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="token_info",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="iPhone 15 Pro"),
     *                 @OA\Property(property="abilities", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $token = $user->currentAccessToken();

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'abilities' => $token?->abilities ?? ['*'],
                    'profile' => [
                        'avatar_url' => $user->profile?->avatar_url,
                        'phone' => $user->profile?->phone,
                    ]
                ],
                'token_info' => [
                    'name' => $token?->name,
                    'abilities' => $token?->abilities,
                    'created_at' => $token?->created_at,
                    'expires_at' => $token?->expires_at,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API user info error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'error' => 'Error obteniendo información del usuario'
            ], 500);
        }
    }

    /**
     * Revocar un token específico por ID
     */
    public function revokeToken(Request $request): JsonResponse
    {
        $request->validate([
            'token_id' => 'required|integer|exists:personal_access_tokens,id'
        ]);

        try {
            $user = $request->user();
            $tokenId = $request->input('token_id');

            // Verificar que el token pertenece al usuario
            $token = $user->tokens()->where('id', $tokenId)->first();

            if (!$token) {
                return response()->json([
                    'error' => 'Token no encontrado o no autorizado'
                ], 404);
            }

            $tokenName = $token->name;
            $token->delete();

            Log::info('Token revoked by user', [
                'user_id' => $user->id,
                'token_id' => $tokenId,
                'token_name' => $tokenName
            ]);

            return response()->json([
                'message' => "Token '{$tokenName}' revocado exitosamente"
            ]);

        } catch (\Exception $e) {
            Log::error('Token revocation error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'token_id' => $request->input('token_id')
            ]);

            return response()->json([
                'error' => 'Error revocando token'
            ], 500);
        }
    }

    /**
     * Listar tokens activos del usuario
     */
    public function listTokens(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $currentTokenId = $user->currentAccessToken()?->id;

            $tokens = $user->tokens()->select([
                'id', 'name', 'abilities', 'created_at', 'expires_at', 'last_used_at'
            ])->get()->map(function ($token) use ($currentTokenId) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'created_at' => $token->created_at,
                    'expires_at' => $token->expires_at,
                    'last_used_at' => $token->last_used_at,
                    'is_current' => $token->id === $currentTokenId
                ];
            });

            return response()->json([
                'tokens' => $tokens,
                'total' => $tokens->count()
            ]);

        } catch (\Exception $e) {
            Log::error('List tokens error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'error' => 'Error listando tokens'
            ], 500);
        }
    }

    /**
     * Definir abilities según rol
     */
    private function getTokenAbilities(User $user): array
    {
        if ($user->hasRole('SuperAdmin')) {
            return ['*']; // Todos los permisos
        }

        if ($user->hasRole('LeagueAdmin')) {
            return [
                'verify:qr',
                'verify:batch',
                'view:stats',
                'view:reports',
                'manage:events',
                'invalidate:cache'
            ];
        }

        if ($user->hasRole('Verifier')) {
            return [
                'verify:qr',
                'verify:batch',
                'view:own-stats',
                'manage:scanner-session'
            ];
        }

        return []; // Sin permisos por defecto
    }
}
