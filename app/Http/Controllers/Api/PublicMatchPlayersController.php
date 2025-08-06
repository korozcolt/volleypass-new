<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VolleyMatch;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Public Match Players",
 *     description="Endpoints públicos para consultar listados de jugadores por partido"
 * )
 */
class PublicMatchPlayersController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/public/matches/{id}/players",
     *     summary="Obtener listado de jugadores de un partido",
     *     tags={"Public Match Players"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de jugadores del partido",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="match_id", type="integer", example=1),
     *                 @OA\Property(property="match_date", type="string", format="date-time", example="2024-01-15T15:30:00Z"),
     *                 @OA\Property(property="status", type="string", example="scheduled"),
     *                 @OA\Property(property="tournament_name", type="string", example="Copa Nacional 2024"),
     *                 @OA\Property(property="venue_name", type="string", example="Polideportivo Central"),
     *                 @OA\Property(
     *                     property="home_team",
     *                     type="object",
     *                     @OA\Property(property="team_id", type="integer", example=5),
     *                     @OA\Property(property="team_name", type="string", example="Equipo A"),
     *                     @OA\Property(property="logo", type="string", example="https://example.com/logo.png"),
     *                     @OA\Property(
     *                         property="players",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="player_id", type="integer", example=10),
     *                             @OA\Property(property="name", type="string", example="María García"),
     *                             @OA\Property(property="jersey_number", type="integer", example=7),
     *                             @OA\Property(property="position", type="string", example="Libero"),
     *                             @OA\Property(property="is_captain", type="boolean", example=false),
     *                             @OA\Property(property="is_starter", type="boolean", example=true),
     *                             @OA\Property(property="birth_date", type="string", format="date", example="1995-03-15"),
     *                             @OA\Property(property="height", type="integer", example=175),
     *                             @OA\Property(property="weight", type="integer", example=65)
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="away_team",
     *                     type="object",
     *                     @OA\Property(property="team_id", type="integer", example=8),
     *                     @OA\Property(property="team_name", type="string", example="Equipo B"),
     *                     @OA\Property(property="logo", type="string", example="https://example.com/logo2.png"),
     *                     @OA\Property(
     *                         property="players",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="player_id", type="integer", example=15),
     *                             @OA\Property(property="name", type="string", example="Ana López"),
     *                             @OA\Property(property="jersey_number", type="integer", example=12),
     *                             @OA\Property(property="position", type="string", example="Atacante"),
     *                             @OA\Property(property="is_captain", type="boolean", example=true),
     *                             @OA\Property(property="is_starter", type="boolean", example=true),
     *                             @OA\Property(property="birth_date", type="string", format="date", example="1993-07-22"),
     *                             @OA\Property(property="height", type="integer", example=180),
     *                             @OA\Property(property="weight", type="integer", example=70)
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="match_officials",
     *                     type="object",
     *                     @OA\Property(property="referee", type="string", example="Carlos Mendoza"),
     *                     @OA\Property(property="assistant_referee", type="string", example="Luis Rodríguez"),
     *                     @OA\Property(property="scorer", type="string", example="Elena Vargas")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Partido no encontrado"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $match = VolleyMatch::with([
                'homeTeam:id,name,logo',
                'awayTeam:id,name,logo',
                'tournament:id,name,is_public',
                'venue:id,name,address',
                'homeTeam.players' => function ($query) {
                    $query->select('players.id', 'players.name', 'players.birth_date', 'players.height', 'players.weight')
                        ->withPivot('jersey_number', 'position', 'is_captain', 'is_starter')
                        ->orderBy('team_player.jersey_number');
                },
                'awayTeam.players' => function ($query) {
                    $query->select('players.id', 'players.name', 'players.birth_date', 'players.height', 'players.weight')
                        ->withPivot('jersey_number', 'position', 'is_captain', 'is_starter')
                        ->orderBy('team_player.jersey_number');
                }
            ])
                ->whereHas('tournament', function ($query) {
                    $query->where('is_public', true);
                })
                ->findOrFail($id);

            $response = [
                'match_id' => $match->id,
                'match_date' => $match->match_date,
                'status' => $match->status,
                'tournament_name' => $match->tournament->name,
                'venue_name' => $match->venue ? $match->venue->name : null,
                'venue_address' => $match->venue ? $match->venue->address : null,
                'home_team' => [
                    'team_id' => $match->homeTeam->id,
                    'team_name' => $match->homeTeam->name,
                    'logo' => $match->homeTeam->logo,
                    'players' => $this->formatTeamPlayers($match->homeTeam->players)
                ],
                'away_team' => [
                    'team_id' => $match->awayTeam->id,
                    'team_name' => $match->awayTeam->name,
                    'logo' => $match->awayTeam->logo,
                    'players' => $this->formatTeamPlayers($match->awayTeam->players)
                ],
                'match_officials' => [
                    'referee' => $match->referee,
                    'assistant_referee' => $match->assistant_referee,
                    'scorer' => $match->scorer
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el listado de jugadores del partido',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/public/matches/{id}/teams/{teamId}/players",
     *     summary="Obtener listado de jugadores de un equipo específico en un partido",
     *     tags={"Public Match Players"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del partido",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="teamId",
     *         in="path",
     *         required=true,
     *         description="ID del equipo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de jugadores del equipo en el partido",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="match_id", type="integer", example=1),
     *                 @OA\Property(property="team_id", type="integer", example=5),
     *                 @OA\Property(property="team_name", type="string", example="Equipo A"),
     *                 @OA\Property(property="logo", type="string", example="https://example.com/logo.png"),
     *                 @OA\Property(property="is_home_team", type="boolean", example=true),
     *                 @OA\Property(
     *                     property="players",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="player_id", type="integer", example=10),
     *                         @OA\Property(property="name", type="string", example="María García"),
     *                         @OA\Property(property="jersey_number", type="integer", example=7),
     *                         @OA\Property(property="position", type="string", example="Libero"),
     *                         @OA\Property(property="is_captain", type="boolean", example=false),
     *                         @OA\Property(property="is_starter", type="boolean", example=true),
     *                         @OA\Property(property="birth_date", type="string", format="date", example="1995-03-15"),
     *                         @OA\Property(property="height", type="integer", example=175),
     *                         @OA\Property(property="weight", type="integer", example=65)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Partido o equipo no encontrado"
     *     )
     * )
     */
    public function showTeam(int $id, int $teamId): JsonResponse
    {
        try {
            $match = VolleyMatch::with([
                'homeTeam:id,name,logo',
                'awayTeam:id,name,logo',
                'tournament:id,name,is_public'
            ])
                ->whereHas('tournament', function ($query) {
                    $query->where('is_public', true);
                })
                ->where(function ($query) use ($teamId) {
                    $query->where('home_team_id', $teamId)
                        ->orWhere('away_team_id', $teamId);
                })
                ->findOrFail($id);

            $isHomeTeam = $match->home_team_id == $teamId;
            $team = $isHomeTeam ? $match->homeTeam : $match->awayTeam;

            // Cargar jugadores del equipo específico
            $team->load(['players' => function ($query) {
                $query->select('players.id', 'players.name', 'players.birth_date', 'players.height', 'players.weight')
                    ->withPivot('jersey_number', 'position', 'is_captain', 'is_starter')
                    ->orderBy('team_player.jersey_number');
            }]);

            $response = [
                'match_id' => $match->id,
                'match_date' => $match->match_date,
                'team_id' => $team->id,
                'team_name' => $team->name,
                'logo' => $team->logo,
                'is_home_team' => $isHomeTeam,
                'players' => $this->formatTeamPlayers($team->players)
            ];

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el listado de jugadores del equipo',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Formatear la información de jugadores de un equipo
     */
    private function formatTeamPlayers($players)
    {
        return $players->map(function ($player) {
            return [
                'player_id' => $player->id,
                'name' => $player->name,
                'jersey_number' => $player->pivot->jersey_number,
                'position' => $player->pivot->position,
                'is_captain' => (bool) $player->pivot->is_captain,
                'is_starter' => (bool) $player->pivot->is_starter,
                'birth_date' => $player->birth_date,
                'age' => $player->birth_date ? now()->diffInYears($player->birth_date) : null,
                'height' => $player->height,
                'weight' => $player->weight
            ];
        })->toArray();
    }
}