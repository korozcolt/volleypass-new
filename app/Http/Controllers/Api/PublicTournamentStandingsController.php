<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentGroup;
use App\Enums\TournamentStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Public Tournament Standings",
 *     description="Endpoints públicos para consultar tablas de posiciones de torneos"
 * )
 */
class PublicTournamentStandingsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/public/tournaments/{id}/standings",
     *     summary="Obtener tabla de posiciones de un torneo",
     *     tags={"Public Tournament Standings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del torneo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tabla de posiciones del torneo",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="tournament_id", type="integer", example=1),
     *                 @OA\Property(property="tournament_name", type="string", example="Copa Nacional 2024"),
     *                 @OA\Property(property="format", type="string", example="round_robin"),
     *                 @OA\Property(
     *                     property="groups",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="group_id", type="integer", example=1),
     *                         @OA\Property(property="group_name", type="string", example="Grupo A"),
     *                         @OA\Property(
     *                             property="standings",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="position", type="integer", example=1),
     *                                 @OA\Property(property="team_id", type="integer", example=5),
     *                                 @OA\Property(property="team_name", type="string", example="Equipo A"),
     *                                 @OA\Property(property="matches_played", type="integer", example=3),
     *                                 @OA\Property(property="wins", type="integer", example=2),
     *                                 @OA\Property(property="losses", type="integer", example=1),
     *                                 @OA\Property(property="sets_won", type="integer", example=6),
     *                                 @OA\Property(property="sets_lost", type="integer", example=4),
     *                                 @OA\Property(property="points_for", type="integer", example=150),
     *                                 @OA\Property(property="points_against", type="integer", example=120),
     *                                 @OA\Property(property="table_points", type="integer", example=7),
     *                                 @OA\Property(property="set_ratio", type="number", format="float", example=1.5),
     *                                 @OA\Property(property="point_ratio", type="number", format="float", example=1.25)
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="overall_standings",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="position", type="integer", example=1),
     *                         @OA\Property(property="team_id", type="integer", example=5),
     *                         @OA\Property(property="team_name", type="string", example="Equipo A"),
     *                         @OA\Property(property="group_name", type="string", example="Grupo A"),
     *                         @OA\Property(property="matches_played", type="integer", example=3),
     *                         @OA\Property(property="wins", type="integer", example=2),
     *                         @OA\Property(property="losses", type="integer", example=1),
     *                         @OA\Property(property="table_points", type="integer", example=7)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Torneo no encontrado"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $tournament = Tournament::with([
                'groups' => function ($query) {
                    $query->active()->orderBy('group_number');
                },
                'teams:id,name,logo'
            ])
                ->where('is_public', true)
                ->where('status', '!=', TournamentStatus::Draft)
                ->findOrFail($id);

            $response = [
                'tournament_id' => $tournament->id,
                'tournament_name' => $tournament->name,
                'format' => $tournament->format,
                'groups' => [],
                'overall_standings' => []
            ];

            $allStandings = [];

            // Obtener standings por grupo
            foreach ($tournament->groups as $group) {
                $groupStandings = $group->getStandingsTable();
                
                $response['groups'][] = [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'group_letter' => $group->getGroupLetter(),
                    'standings' => $groupStandings
                ];

                // Agregar al ranking general
                foreach ($groupStandings as $standing) {
                    $standing['group_name'] = $group->name;
                    $standing['group_letter'] = $group->getGroupLetter();
                    $allStandings[] = $standing;
                }
            }

            // Ordenar standings generales
            usort($allStandings, function ($a, $b) {
                if ($a['table_points'] !== $b['table_points']) {
                    return $b['table_points'] <=> $a['table_points'];
                }
                if ($a['set_ratio'] !== $b['set_ratio']) {
                    return $b['set_ratio'] <=> $a['set_ratio'];
                }
                return $b['point_ratio'] <=> $a['point_ratio'];
            });

            // Asignar posiciones generales
            foreach ($allStandings as $index => &$standing) {
                $standing['overall_position'] = $index + 1;
            }

            $response['overall_standings'] = $allStandings;

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la tabla de posiciones',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/public/tournaments/{id}/groups/{groupId}/standings",
     *     summary="Obtener tabla de posiciones de un grupo específico",
     *     tags={"Public Tournament Standings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del torneo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="groupId",
     *         in="path",
     *         required=true,
     *         description="ID del grupo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tabla de posiciones del grupo",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="group_id", type="integer", example=1),
     *                 @OA\Property(property="group_name", type="string", example="Grupo A"),
     *                 @OA\Property(property="tournament_id", type="integer", example=1),
     *                 @OA\Property(property="tournament_name", type="string", example="Copa Nacional 2024"),
     *                 @OA\Property(
     *                     property="standings",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="position", type="integer", example=1),
     *                         @OA\Property(property="team_id", type="integer", example=5),
     *                         @OA\Property(property="team_name", type="string", example="Equipo A"),
     *                         @OA\Property(property="matches_played", type="integer", example=3),
     *                         @OA\Property(property="wins", type="integer", example=2),
     *                         @OA\Property(property="losses", type="integer", example=1),
     *                         @OA\Property(property="sets_won", type="integer", example=6),
     *                         @OA\Property(property="sets_lost", type="integer", example=4),
     *                         @OA\Property(property="points_for", type="integer", example=150),
     *                         @OA\Property(property="points_against", type="integer", example=120),
     *                         @OA\Property(property="table_points", type="integer", example=7),
     *                         @OA\Property(property="set_ratio", type="number", format="float", example=1.5),
     *                         @OA\Property(property="point_ratio", type="number", format="float", example=1.25)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grupo o torneo no encontrado"
     *     )
     * )
     */
    public function showGroup(int $id, int $groupId): JsonResponse
    {
        try {
            $tournament = Tournament::where('is_public', true)
                ->where('status', '!=', TournamentStatus::Draft)
                ->findOrFail($id);

            $group = TournamentGroup::where('tournament_id', $id)
                ->where('id', $groupId)
                ->where('is_active', true)
                ->firstOrFail();

            $standings = $group->getStandingsTable();

            return response()->json([
                'success' => true,
                'data' => [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'group_letter' => $group->getGroupLetter(),
                    'tournament_id' => $tournament->id,
                    'tournament_name' => $tournament->name,
                    'standings' => $standings,
                    'matches_progress' => $group->getMatchesProgress()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la tabla de posiciones del grupo',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}