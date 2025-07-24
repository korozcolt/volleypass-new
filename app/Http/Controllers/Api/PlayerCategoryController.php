<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\CategoryAssignmentService;
use App\Enums\PlayerCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PlayerCategoryController extends Controller
{
    protected CategoryAssignmentService $categoryService;

    public function __construct(CategoryAssignmentService $categoryService)
    {   
        $this->categoryService = $categoryService;
    }

    /**
     * Actualiza la categoría de un jugador
     * 
     * @param Player $player El jugador cuya categoría se actualizará
     * @param Request $request La solicitud con los datos de la nueva categoría
     * @return JsonResponse Respuesta con el resultado de la operación
     */
    public function update(Player $player, Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'category' => 'required|string|in:' . implode(',', array_column(PlayerCategory::cases(), 'value')),
                'reason' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldCategory = $player->category;
            $newCategory = $request->category;
            $reason = $request->reason;

            // Verificar si la categoría es la misma
            if ($oldCategory === $newCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'El jugador ya está asignado a esta categoría'
                ], 422);
            }

            // Actualizar la categoría del jugador
            $result = $this->categoryService->updatePlayerCategory(
                $player,
                $newCategory,
                Auth::user(),
                true,
                $reason
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? []
                ], 422);
            }

            Log::info('Categoría de jugador actualizada via API', [
                'player_id' => $player->id,
                'old_category' => $oldCategory,
                'new_category' => $newCategory,
                'reason' => $reason,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente',
                'player' => [
                    'id' => $player->id,
                    'name' => $player->name,
                    'old_category' => $oldCategory,
                    'new_category' => $newCategory,
                    'reason' => $reason
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando categoría de jugador via API', [
                'player_id' => $player->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Obtiene información sobre la categoría actual de un jugador
     * 
     * @param Player $player El jugador cuya información de categoría se consultará
     * @return JsonResponse Respuesta con la información de la categoría
     */
    public function show(Player $player): JsonResponse
    {
        try {
            $category = $player->category;
            $isCorrectCategory = $player->isInCorrectCategory();

            return response()->json([
                'success' => true,
                'player' => [
                    'id' => $player->id,
                    'name' => $player->name,
                    'age' => $player->age,
                    'gender' => $player->gender,
                    'current_category' => [
                        'name' => $category,
                        'value' => $category,
                        'color' => '#CCCCCC',
                        'icon' => 'default-icon',
                        'age_range' => '0-99',
                    ],
                    'is_correct_category' => $isCorrectCategory,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo información de categoría de jugador via API', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
}