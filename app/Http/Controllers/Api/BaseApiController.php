<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="VolleyPass API",
 *     version="1.0.0",
 *     description="API para el sistema de gestión de voleibol VolleyPass",
 *     @OA\Contact(
 *         email="admin@volleypass.com",
 *         name="VolleyPass Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="VolleyPass API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Usar el token de autenticación en el header Authorization: Bearer {token}"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints de autenticación y gestión de tokens"
 * )
 *
 * @OA\Tag(
 *     name="Players",
 *     description="Gestión de jugadores"
 * )
 *
 * @OA\Tag(
 *     name="Teams",
 *     description="Gestión de equipos"
 * )
 *
 * @OA\Tag(
 *     name="Tournaments",
 *     description="Gestión de torneos"
 * )
 *
 * @OA\Tag(
 *     name="Matches",
 *     description="Gestión de partidos y eventos en vivo"
 * )
 *
 * @OA\Tag(
 *     name="Cards",
 *     description="Gestión de tarjetas de jugadores"
 * )
 *
 * @OA\Tag(
 *     name="QR Verification",
 *     description="Verificación de códigos QR"
 * )
 *
 * @OA\Tag(
 *     name="Sanctions",
 *     description="Gestión de sanciones disciplinarias"
 * )
 *
 * @OA\Tag(
 *     name="Transfers",
 *     description="Gestión de transferencias de jugadores"
 * )
 *
 * @OA\Tag(
 *     name="Payments",
 *     description="Gestión de pagos y validaciones"
 * )
 */
class BaseApiController extends Controller
{
    /**
     * Respuesta exitosa estándar
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ], $statusCode);
    }

    /**
     * Respuesta de error estándar
     */
    protected function errorResponse(string $message = 'Error', $errors = null, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toISOString()
        ], $statusCode);
    }

    /**
     * Respuesta paginada estándar
     */
    protected function paginatedResponse($data, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * @OA\Schema(
     *     schema="SuccessResponse",
     *     type="object",
     *     @OA\Property(property="success", type="boolean", example=true),
     *     @OA\Property(property="message", type="string", example="Success"),
     *     @OA\Property(property="data", type="object"),
     *     @OA\Property(property="timestamp", type="string", format="date-time")
     * )
     */

    /**
     * @OA\Schema(
     *     schema="ErrorResponse",
     *     type="object",
     *     @OA\Property(property="success", type="boolean", example=false),
     *     @OA\Property(property="message", type="string", example="Error message"),
     *     @OA\Property(property="errors", type="object"),
     *     @OA\Property(property="timestamp", type="string", format="date-time")
     * )
     */

    /**
     * @OA\Schema(
     *     schema="ValidationErrorResponse",
     *     type="object",
     *     @OA\Property(property="message", type="string", example="The given data was invalid."),
     *     @OA\Property(
     *         property="errors",
     *         type="object",
     *         @OA\AdditionalProperties(
     *             type="array",
     *             @OA\Items(type="string")
     *         ),
     *         example={
     *             "email": {"The email field is required."},
     *             "password": {"The password field is required."}
     *         }
     *     )
     * )
     */

    /**
     * @OA\Schema(
     *     schema="PaginatedResponse",
     *     type="object",
     *     @OA\Property(property="success", type="boolean", example=true),
     *     @OA\Property(property="message", type="string", example="Success"),
     *     @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *     @OA\Property(
     *         property="pagination",
     *         type="object",
     *         @OA\Property(property="current_page", type="integer"),
     *         @OA\Property(property="last_page", type="integer"),
     *         @OA\Property(property="per_page", type="integer"),
     *         @OA\Property(property="total", type="integer"),
     *         @OA\Property(property="from", type="integer"),
     *         @OA\Property(property="to", type="integer")
     *     ),
     *     @OA\Property(property="timestamp", type="string", format="date-time")
     * )
     */

    /**
     * @OA\Schema(
     *     schema="ValidationErrorResponse",
     *     type="object",
     *     @OA\Property(property="success", type="boolean", example=false),
     *     @OA\Property(property="message", type="string", example="Validation failed"),
     *     @OA\Property(
     *         property="errors",
     *         type="object",
     *         @OA\Property(
     *             property="field_name",
     *             type="array",
     *             @OA\Items(type="string", example="The field is required.")
     *         )
     *     ),
     * @OA\Property(property="timestamp", type="string", format="date-time")
     * )
     */

    /**
     * @OA\Schema(
     *     schema="PlayerRotation",
     *     type="object",
     *     title="Player Rotation",
     *     description="Player rotation data",
     *     @OA\Property(property="team_id", type="integer", example=1),
     *     @OA\Property(property="set_number", type="integer", example=1),
     *     @OA\Property(property="rotation_order", type="array", @OA\Items(type="integer")),
     *     @OA\Property(property="current_server", type="integer", example=1),
     *     @OA\Property(property="rotation_count", type="integer", example=3),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */
}