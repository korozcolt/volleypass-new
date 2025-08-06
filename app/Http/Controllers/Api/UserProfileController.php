<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * @OA\Tag(
 *     name="User Profile",
 *     description="Gestión de perfiles de usuario"
 * )
 */
class UserProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/users/profile",
     *     summary="Obtener perfil del usuario autenticado",
     *     tags={"User Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Perfil del usuario",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="email", type="string", example="juan@example.com"),
     *                 @OA\Property(property="first_name", type="string", example="Juan"),
     *                 @OA\Property(property="last_name", type="string", example="Pérez"),
     *                 @OA\Property(property="phone", type="string", example="+57 300 123 4567"),
     *                 @OA\Property(property="birth_date", type="string", format="date", example="1990-05-15"),
     *                 @OA\Property(property="gender", type="string", example="male"),
     *                 @OA\Property(property="address", type="string", example="Calle 123 #45-67"),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"Player"}),
     *                 @OA\Property(property="user_type", type="string", example="player"),
     *                 @OA\Property(
     *                     property="profile",
     *                     type="object",
     *                     @OA\Property(property="nickname", type="string", example="Juancho"),
     *                     @OA\Property(property="bio", type="string", example="Jugador de voleibol desde 2010"),
     *                     @OA\Property(property="avatar_url", type="string", nullable=true),
     *                     @OA\Property(property="emergency_contact_name", type="string", example="María Pérez"),
     *                     @OA\Property(property="emergency_contact_phone", type="string", example="+57 300 987 6543"),
     *                     @OA\Property(property="blood_type", type="string", example="O+")
     *                 ),
     *                 @OA\Property(
     *                     property="player_info",
     *                     type="object",
     *                     nullable=true,
     *                     @OA\Property(property="position", type="string", example="Libero"),
     *                     @OA\Property(property="jersey_number", type="integer", example=10),
     *                     @OA\Property(property="height", type="number", format="float", example=1.75),
     *                     @OA\Property(property="weight", type="number", format="float", example=70.5),
     *                     @OA\Property(property="current_club", type="string", example="Club Deportivo ABC")
     *                 ),
     *                 @OA\Property(
     *                     property="location",
     *                     type="object",
     *                     @OA\Property(property="city", type="string", example="Bogotá"),
     *                     @OA\Property(property="department", type="string", example="Cundinamarca"),
     *                     @OA\Property(property="country", type="string", example="Colombia")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No autenticado")
     *         )
     *     )
     * )
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load([
                'profile',
                'player.currentClub',
                'player.position',
                'city',
                'department',
                'country',
                'roles'
            ]);

            $userType = $this->getUserType($user);
            
            $profileData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'birth_date' => $user->birth_date?->format('Y-m-d'),
                'gender' => $user->gender,
                'address' => $user->address,
                'roles' => $user->getRoleNames(),
                'user_type' => $userType,
                'profile' => [
                    'nickname' => $user->profile?->nickname,
                    'bio' => $user->profile?->bio,
                    'avatar_url' => $user->avatar_url,
                    'emergency_contact_name' => $user->profile?->emergency_contact_name,
                    'emergency_contact_phone' => $user->profile?->emergency_contact_phone,
                    'emergency_contact_relationship' => $user->profile?->emergency_contact_relationship,
                    'blood_type' => $user->profile?->blood_type,
                    'allergies' => $user->profile?->allergies,
                    'medical_conditions' => $user->profile?->medical_conditions,
                    't_shirt_size' => $user->profile?->t_shirt_size,
                    'social_media' => $user->profile?->social_media,
                ],
                'location' => [
                    'city' => $user->city?->name,
                    'department' => $user->department?->name,
                    'country' => $user->country?->name,
                ]
            ];

            // Agregar información específica del jugador si aplica
            if ($userType === 'player' && $user->player) {
                $profileData['player_info'] = [
                    'position' => $user->player->position?->getLabel(),
                    'jersey_number' => $user->player->jersey_number,
                    'height' => $user->player->height,
                    'weight' => $user->player->weight,
                    'current_club' => $user->player->currentClub?->name,
                    'category' => $user->player->category?->getLabel(),
                    'years_playing' => $user->profile?->years_playing,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $profileData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el perfil del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/profile",
     *     summary="Actualizar perfil del usuario autenticado",
     *     tags={"User Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", example="Juan"),
     *             @OA\Property(property="last_name", type="string", example="Pérez"),
     *             @OA\Property(property="phone", type="string", example="+57 300 123 4567"),
     *             @OA\Property(property="address", type="string", example="Calle 123 #45-67"),
     *             @OA\Property(property="nickname", type="string", example="Juancho"),
     *             @OA\Property(property="bio", type="string", example="Jugador de voleibol desde 2010"),
     *             @OA\Property(property="emergency_contact_name", type="string", example="María Pérez"),
     *             @OA\Property(property="emergency_contact_phone", type="string", example="+57 300 987 6543"),
     *             @OA\Property(property="emergency_contact_relationship", type="string", example="Madre"),
     *             @OA\Property(property="blood_type", type="string", example="O+"),
     *             @OA\Property(property="allergies", type="string", example="Ninguna conocida"),
     *             @OA\Property(property="medical_conditions", type="string", example="Ninguna"),
     *             @OA\Property(property="t_shirt_size", type="string", example="M"),
     *             @OA\Property(
     *                 property="social_media",
     *                 type="object",
     *                 @OA\Property(property="instagram", type="string", example="@juancho_volley"),
     *                 @OA\Property(property="facebook", type="string", example="Juan Pérez")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Perfil actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Perfil actualizado exitosamente"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $validated = $request->validate([
                'first_name' => 'sometimes|string|max:100',
                'last_name' => 'sometimes|string|max:100',
                'phone' => 'sometimes|string|max:20',
                'address' => 'sometimes|string|max:255',
                'nickname' => 'sometimes|string|max:50',
                'bio' => 'sometimes|string|max:500',
                'emergency_contact_name' => 'sometimes|string|max:100',
                'emergency_contact_phone' => 'sometimes|string|max:20',
                'emergency_contact_relationship' => 'sometimes|string|max:50',
                'blood_type' => 'sometimes|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
                'allergies' => 'sometimes|string|max:500',
                'medical_conditions' => 'sometimes|string|max:500',
                't_shirt_size' => 'sometimes|string|in:XS,S,M,L,XL,XXL',
                'social_media' => 'sometimes|array',
                'social_media.instagram' => 'sometimes|string|max:100',
                'social_media.facebook' => 'sometimes|string|max:100',
                'social_media.twitter' => 'sometimes|string|max:100',
            ]);

            // Actualizar datos del usuario
            $userFields = ['first_name', 'last_name', 'phone', 'address'];
            $userData = array_intersect_key($validated, array_flip($userFields));
            
            if (!empty($userData)) {
                $user->update($userData);
            }

            // Actualizar datos del perfil
            $profileFields = [
                'nickname', 'bio', 'emergency_contact_name', 'emergency_contact_phone',
                'emergency_contact_relationship', 'blood_type', 'allergies', 
                'medical_conditions', 't_shirt_size', 'social_media'
            ];
            $profileData = array_intersect_key($validated, array_flip($profileFields));
            
            if (!empty($profileData)) {
                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );
            }

            // Recargar el usuario con las relaciones actualizadas
            $user->load(['profile', 'player.currentClub', 'roles']);
            
            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'data' => $this->formatUserProfile($user)
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{userId}/profile",
     *     summary="Obtener perfil público de un usuario",
     *     tags={"User Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Perfil público del usuario",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function showPublic(int $userId): JsonResponse
    {
        try {
            $user = User::with([
                'profile',
                'player.currentClub',
                'player.position',
                'city',
                'department',
                'country',
                'roles'
            ])->findOrFail($userId);

            $userType = $this->getUserType($user);
            
            // Solo mostrar información pública
            $profileData = [
                'id' => $user->id,
                'name' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'user_type' => $userType,
                'profile' => [
                    'nickname' => $user->profile?->nickname,
                    'bio' => $user->profile?->bio,
                    'avatar_url' => $user->avatar_url,
                ],
                'location' => [
                    'city' => $user->city?->name,
                    'department' => $user->department?->name,
                    'country' => $user->country?->name,
                ]
            ];

            // Mostrar información de contacto solo si el usuario lo permite
            if ($user->profile?->show_phone) {
                $profileData['phone'] = $user->phone;
            }
            if ($user->profile?->show_email) {
                $profileData['email'] = $user->email;
            }
            if ($user->profile?->show_address) {
                $profileData['address'] = $user->address;
            }

            // Agregar información pública del jugador si aplica
            if ($userType === 'player' && $user->player) {
                $profileData['player_info'] = [
                    'position' => $user->player->position?->getLabel(),
                    'jersey_number' => $user->player->jersey_number,
                    'current_club' => $user->player->currentClub?->name,
                    'category' => $user->player->category?->getLabel(),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $profileData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el perfil del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Determinar el tipo de usuario basado en sus roles
     */
    private function getUserType(User $user): string
    {
        if ($user->player) {
            return 'player';
        }
        
        if ($user->hasRole('Coach')) {
            return 'coach';
        }
        
        if ($user->hasRole('Referee')) {
            return 'referee';
        }
        
        if ($user->hasRole('ClubAdmin')) {
            return 'club_admin';
        }
        
        if ($user->hasRole('LeagueAdmin')) {
            return 'league_admin';
        }
        
        if ($user->hasRole('SuperAdmin')) {
            return 'super_admin';
        }
        
        return 'user';
    }

    /**
     * Formatear datos del perfil de usuario
     */
    private function formatUserProfile(User $user): array
    {
        $userType = $this->getUserType($user);
        
        $profileData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'address' => $user->address,
            'user_type' => $userType,
            'profile' => [
                'nickname' => $user->profile?->nickname,
                'bio' => $user->profile?->bio,
                'avatar_url' => $user->avatar_url,
                'emergency_contact_name' => $user->profile?->emergency_contact_name,
                'emergency_contact_phone' => $user->profile?->emergency_contact_phone,
                'blood_type' => $user->profile?->blood_type,
            ]
        ];

        if ($userType === 'player' && $user->player) {
            $profileData['player_info'] = [
                'position' => $user->player->position?->getLabel(),
                'jersey_number' => $user->player->jersey_number,
                'current_club' => $user->player->currentClub?->name,
            ];
        }

        return $profileData;
    }
}