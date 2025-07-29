<?php

namespace App\Services;

use App\Models\VolleyMatch;
use App\Models\MatchSet;
use App\Events\MatchScoreUpdated;
use App\Events\MatchStatusChanged;
use App\Events\SetUpdated;
use App\Events\PlayerRotationUpdated;
use App\Enums\MatchStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MatchRealTimeService
{
    /**
     * Actualizar el marcador de un set en tiempo real
     */
    public function updateSetScore(int $matchId, int $setNumber, int $homeScore, int $awayScore, string $eventType = 'point'): array
    {
        return DB::transaction(function () use ($matchId, $setNumber, $homeScore, $awayScore, $eventType) {
            $match = VolleyMatch::with(['homeTeam', 'awayTeam'])->findOrFail($matchId);
            
            // Buscar o crear el set
            $matchSet = MatchSet::firstOrCreate(
                [
                    'match_id' => $matchId,
                    'set_number' => $setNumber,
                ],
                [
                    'home_score' => 0,
                    'away_score' => 0,
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'created_by' => Auth::check() ? Auth::user()->id : 1,
                ]
            );

            // Actualizar marcador del set
            $matchSet->update([
                'home_score' => $homeScore,
                'away_score' => $awayScore,
            ]);

            // Verificar si el set terminó (25 puntos con diferencia de 2)
            if ($this->isSetCompleted($homeScore, $awayScore)) {
                $this->completeSet($matchSet);
                $this->updateMatchSets($match);
            }

            // Disparar evento de actualización de marcador
            broadcast(new MatchScoreUpdated($match, $setNumber, $homeScore, $awayScore, $eventType));

            // Disparar evento de actualización de set
            broadcast(new SetUpdated($matchSet, 'score_updated'));

            return [
                'success' => true,
                'match' => $match->fresh(),
                'set' => $matchSet->fresh(),
                'set_completed' => $matchSet->status === 'completed',
            ];
        });
    }

    /**
     * Cambiar el estado de un partido
     */
    public function changeMatchStatus(int $matchId, string $newStatus): array
    {
        return DB::transaction(function () use ($matchId, $newStatus) {
            $match = VolleyMatch::with(['homeTeam', 'awayTeam'])->findOrFail($matchId);
            $previousStatus = $match->status->value;

            // Actualizar estado del partido
            $updateData = ['status' => $newStatus];
            
            if ($newStatus === MatchStatus::In_Progress->value && !$match->started_at) {
                $updateData['started_at'] = now();
            } elseif ($newStatus === MatchStatus::Finished->value && !$match->finished_at) {
                $updateData['finished_at'] = now();
                $updateData['duration_minutes'] = $match->started_at ? now()->diffInMinutes($match->started_at) : null;
            }

            $match->update($updateData);

            // Disparar evento de cambio de estado
            broadcast(new MatchStatusChanged($match, $previousStatus, $newStatus));

            return [
                'success' => true,
                'match' => $match->fresh(),
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
            ];
        });
    }

    /**
     * Actualizar rotación de jugadores
     */
    public function updatePlayerRotation(int $matchId, int $teamId, array $rotationData): array
    {
        $match = VolleyMatch::with(['homeTeam', 'awayTeam'])->findOrFail($matchId);

        // Guardar datos de rotación en el campo events del partido
        $events = $match->events ?? [];
        $events[] = [
            'type' => 'rotation',
            'team_id' => $teamId,
            'rotation_data' => $rotationData,
            'timestamp' => now()->toISOString(),
        ];

        $match->update(['events' => $events]);

        // Disparar evento de rotación
        broadcast(new PlayerRotationUpdated($match, $teamId, $rotationData));

        return [
            'success' => true,
            'match' => $match->fresh(),
            'rotation_data' => $rotationData,
        ];
    }

    /**
     * Iniciar un nuevo set
     */
    public function startNewSet(int $matchId): array
    {
        return DB::transaction(function () use ($matchId) {
            $match = VolleyMatch::with(['sets'])->findOrFail($matchId);
            $nextSetNumber = $match->sets->count() + 1;

            $newSet = MatchSet::create([
                'match_id' => $matchId,
                'set_number' => $nextSetNumber,
                'home_score' => 0,
                'away_score' => 0,
                'status' => 'in_progress',
                'started_at' => now(),
                'created_by' => Auth::check() ? Auth::user()->id : 1,
            ]);

            // Disparar evento de nuevo set
            broadcast(new SetUpdated($newSet, 'started'));

            return [
                'success' => true,
                'set' => $newSet,
                'set_number' => $nextSetNumber,
            ];
        });
    }

    /**
     * Verificar si un set está completado
     */
    private function isSetCompleted(int $homeScore, int $awayScore): bool
    {
        // Set normal: 25 puntos con diferencia de 2
        if (($homeScore >= 25 || $awayScore >= 25) && abs($homeScore - $awayScore) >= 2) {
            return true;
        }

        // Set de desempate (5to set): 15 puntos con diferencia de 2
        if (($homeScore >= 15 || $awayScore >= 15) && abs($homeScore - $awayScore) >= 2) {
            return true;
        }

        return false;
    }

    /**
     * Completar un set
     */
    private function completeSet(MatchSet $matchSet): void
    {
        $matchSet->update([
            'status' => 'completed',
            'ended_at' => now(),
            'duration_minutes' => $matchSet->started_at ? now()->diffInMinutes($matchSet->started_at) : null,
        ]);
    }

    /**
     * Actualizar el conteo de sets ganados por cada equipo
     */
    private function updateMatchSets(VolleyMatch $match): void
    {
        $completedSets = $match->sets()->where('status', 'completed')->get();
        
        $homeSets = $completedSets->filter(function ($set) {
            return $set->home_score > $set->away_score;
        })->count();
        
        $awaySets = $completedSets->filter(function ($set) {
            return $set->away_score > $set->home_score;
        })->count();

        $updateData = [
            'home_sets' => $homeSets,
            'away_sets' => $awaySets,
        ];

        // Si un equipo ganó 3 sets, el partido termina
        if ($homeSets >= 3 || $awaySets >= 3) {
            $updateData['status'] = MatchStatus::Finished;
            $updateData['finished_at'] = now();
            $updateData['winner_team_id'] = $homeSets > $awaySets ? $match->home_team_id : $match->away_team_id;
            $updateData['duration_minutes'] = $match->started_at ? now()->diffInMinutes($match->started_at) : null;
        }

        $match->update($updateData);
    }

    /**
     * Obtener el estado actual de un partido en tiempo real
     */
    public function getMatchRealTimeData(int $matchId): array
    {
        $match = VolleyMatch::with([
            'homeTeam',
            'awayTeam',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            },
            'tournament',
        ])->findOrFail($matchId);

        $currentSet = $match->sets()->where('status', 'in_progress')->first();
        
        return [
            'match' => $match,
            'current_set' => $currentSet,
            'sets' => $match->sets,
            'is_live' => $match->status === MatchStatus::In_Progress,
            'events' => $match->events ?? [],
        ];
    }
}