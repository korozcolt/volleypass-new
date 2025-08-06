<?php

namespace App\Services;

use App\Models\VolleyMatch;
use App\Models\MatchSet;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\Team;
use App\Events\MatchUpdated;
use App\Events\SetCompleted;
use App\Events\MatchFinished;
use App\Events\PlayerRotationUpdated;
use App\Enums\MatchStatus;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class MatchLiveService
{
    private MatchRealTimeService $realTimeService;
    
    public function __construct(MatchRealTimeService $realTimeService)
    {
        $this->realTimeService = $realTimeService;
    }

    /**
     * Iniciar un partido en vivo
     */
    public function startMatch(VolleyMatch $match): array
    {
        try {
            return DB::transaction(function () use ($match) {
                if ($match->status !== MatchStatus::Scheduled) {
                    throw new Exception('El partido no está programado para iniciar');
                }

                // Actualizar estado del partido
                $match->update([
                    'status' => MatchStatus::In_Progress,
                    'started_at' => now(),
                    'current_set' => 1
                ]);

                // Crear primer set
                $firstSet = MatchSet::create([
                    'match_id' => $match->id,
                    'set_number' => 1,
                    'home_score' => 0,
                    'away_score' => 0,
                    'status' => 'in_progress',
                    'started_at' => now()
                ]);

                // Registrar evento de inicio
                $this->recordEvent($match, 'match_start', [
                    'message' => 'Partido iniciado',
                    'timestamp' => now()->toISOString()
                ]);

                // Inicializar rotaciones
                $this->initializeRotations($match);

                // Broadcast del inicio
                broadcast(new MatchUpdated($match->fresh()));

                Log::info("Partido iniciado: {$match->id}");

                return [
                    'success' => true,
                    'match' => $match->fresh(),
                    'current_set' => $firstSet,
                    'message' => 'Partido iniciado exitosamente'
                ];
            });
        } catch (Exception $e) {
            Log::error("Error iniciando partido {$match->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Actualizar puntuación en vivo
     */
    public function updateScore(VolleyMatch $match, string $team, int $points = 1): array
    {
        try {
            return DB::transaction(function () use ($match, $team, $points) {
                $currentSet = $match->currentSet();
                
                if (!$currentSet || $currentSet->status !== 'in_progress') {
                    throw new Exception('No hay set activo para actualizar');
                }

                // Actualizar puntuación
                if ($team === 'home') {
                    $currentSet->increment('home_score', $points);
                } elseif ($team === 'away') {
                    $currentSet->increment('away_score', $points);
                } else {
                    throw new Exception('Equipo inválido');
                }

                $currentSet->refresh();

                // Registrar evento de punto
                $this->recordEvent($match, 'point', [
                    'team' => $team,
                    'points' => $points,
                    'set_number' => $currentSet->set_number,
                    'home_score' => $currentSet->home_score,
                    'away_score' => $currentSet->away_score,
                    'timestamp' => now()->toISOString()
                ]);

                // Verificar si el set terminó
                $setResult = $this->checkSetCompletion($currentSet);
                
                if ($setResult['completed']) {
                    $this->completeSet($match, $currentSet, $setResult['winner']);
                    
                    // Verificar si el partido terminó
                    $matchResult = $this->checkMatchCompletion($match);
                    
                    if ($matchResult['completed']) {
                        $this->completeMatch($match, $matchResult['winner']);
                    } else {
                        // Iniciar siguiente set
                        $this->startNextSet($match);
                    }
                }

                // Broadcast actualización
                broadcast(new MatchUpdated($match->fresh()));

                return [
                    'success' => true,
                    'match' => $match->fresh(),
                    'current_set' => $currentSet->fresh(),
                    'set_completed' => $setResult['completed'] ?? false
                ];
            });
        } catch (Exception $e) {
            Log::error("Error actualizando puntuación: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Registrar evento especial (timeout, sustitución, etc.)
     */
    public function recordSpecialEvent(VolleyMatch $match, string $eventType, array $data = []): array
    {
        try {
            $event = $this->recordEvent($match, $eventType, array_merge($data, [
                'timestamp' => now()->toISOString(),
                'set_number' => $match->current_set
            ]));

            // Manejar eventos especiales
            switch ($eventType) {
                case 'timeout':
                    $this->handleTimeout($match, $data);
                    break;
                case 'substitution':
                    $this->handleSubstitution($match, $data);
                    break;
                case 'yellow_card':
                case 'red_card':
                    $this->handleCard($match, $eventType, $data);
                    break;
            }

            broadcast(new MatchUpdated($match->fresh()));

            return [
                'success' => true,
                'event' => $event,
                'message' => 'Evento registrado exitosamente'
            ];
        } catch (Exception $e) {
            Log::error("Error registrando evento: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Actualizar rotación de jugadores
     */
    public function updateRotation(VolleyMatch $match, string $team, array $rotation): array
    {
        try {
            return DB::transaction(function () use ($match, $team, $rotation) {
                // Validar rotación
                if (count($rotation) !== 6) {
                    throw new Exception('La rotación debe tener exactamente 6 jugadores');
                }

                // Usar el servicio existente
                $result = $this->realTimeService->updatePlayerRotation($match->id, $team, $rotation);

                // Registrar evento
                $this->recordEvent($match, 'rotation', [
                    'team' => $team,
                    'rotation' => $rotation,
                    'set_number' => $match->current_set,
                    'timestamp' => now()->toISOString()
                ]);

                // Broadcast rotación
                broadcast(new PlayerRotationUpdated($match, $team, $rotation));

                return [
                    'success' => true,
                    'rotation' => $result,
                    'message' => 'Rotación actualizada exitosamente'
                ];
            });
        } catch (Exception $e) {
            Log::error("Error actualizando rotación: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Pausar partido
     */
    public function pauseMatch(VolleyMatch $match, string $reason = ''): array
    {
        try {
            $match->update([
                'status' => MatchStatus::Paused,
                'paused_at' => now()
            ]);

            $this->recordEvent($match, 'match_pause', [
                'reason' => $reason,
                'timestamp' => now()->toISOString()
            ]);

            broadcast(new MatchUpdated($match->fresh()));

            return [
                'success' => true,
                'match' => $match->fresh(),
                'message' => 'Partido pausado'
            ];
        } catch (Exception $e) {
            Log::error("Error pausando partido: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Reanudar partido
     */
    public function resumeMatch(VolleyMatch $match): array
    {
        try {
            $match->update([
                'status' => MatchStatus::In_Progress,
                'paused_at' => null
            ]);

            $this->recordEvent($match, 'match_resume', [
                'timestamp' => now()->toISOString()
            ]);

            broadcast(new MatchUpdated($match->fresh()));

            return [
                'success' => true,
                'match' => $match->fresh(),
                'message' => 'Partido reanudado'
            ];
        } catch (Exception $e) {
            Log::error("Error reanudando partido: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Obtener estado en vivo del partido
     */
    public function getLiveMatchData(VolleyMatch $match): array
    {
        $cacheKey = "live_match_{$match->id}";
        
        return Cache::remember($cacheKey, 30, function () use ($match) {
            $match->load([
                'homeTeam',
                'awayTeam',
                'sets' => function ($query) {
                    $query->orderBy('set_number');
                },
                'events' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(20);
                },
                'venue',
                'mainReferee',
                'assistantReferee'
            ]);

            $currentSet = $match->currentSet();
            $rotations = $this->getCurrentRotations($match);
            $stats = $this->calculateLiveStats($match);

            return [
                'match' => $match,
                'current_set' => $currentSet,
                'rotations' => $rotations,
                'stats' => $stats,
                'recent_events' => $match->events,
                'last_updated' => now()->toISOString()
            ];
        });
    }

    /**
     * Obtener partidos en vivo activos
     */
    public function getActiveMatches(): array
    {
        $matches = VolleyMatch::whereIn('status', [
            MatchStatus::In_Progress,
            MatchStatus::Paused
        ])
        ->with(['homeTeam', 'awayTeam', 'currentSet'])
        ->orderBy('started_at')
        ->get();

        return $matches->map(function ($match) {
            return [
                'id' => $match->id,
                'home_team' => $match->homeTeam->name,
                'away_team' => $match->awayTeam->name,
                'status' => $match->status,
                'current_set' => $match->current_set,
                'started_at' => $match->started_at,
                'score_summary' => $this->getScoreSummary($match)
            ];
        })->toArray();
    }

    // Métodos privados de apoyo

    private function recordEvent(VolleyMatch $match, string $eventType, array $data = []): MatchEvent
    {
        return MatchEvent::create([
            'match_id' => $match->id,
            'event_type' => $eventType,
            'data' => $data,
            'created_by' => Auth::id(),
            'created_at' => now()
        ]);
    }

    private function initializeRotations(VolleyMatch $match): void
    {
        // Obtener jugadores de cada equipo
        $homeTeamPlayers = $match->homeTeam->players()->limit(6)->pluck('id')->toArray();
        $awayTeamPlayers = $match->awayTeam->players()->limit(6)->pluck('id')->toArray();

        // Inicializar rotaciones por defecto
        if (count($homeTeamPlayers) >= 6) {
            $this->realTimeService->updatePlayerRotation($match->id, 'home', $homeTeamPlayers);
        }
        
        if (count($awayTeamPlayers) >= 6) {
            $this->realTimeService->updatePlayerRotation($match->id, 'away', $awayTeamPlayers);
        }
    }

    private function checkSetCompletion(MatchSet $set): array
    {
        $homeScore = $set->home_score;
        $awayScore = $set->away_score;
        
        // Reglas básicas de voleibol
        $minPoints = 25;
        $minDifference = 2;
        
        // Set 5 se juega a 15 puntos
        if ($set->set_number === 5) {
            $minPoints = 15;
        }
        
        $completed = false;
        $winner = null;
        
        if ($homeScore >= $minPoints && ($homeScore - $awayScore) >= $minDifference) {
            $completed = true;
            $winner = 'home';
        } elseif ($awayScore >= $minPoints && ($awayScore - $homeScore) >= $minDifference) {
            $completed = true;
            $winner = 'away';
        }
        
        return [
            'completed' => $completed,
            'winner' => $winner
        ];
    }

    private function completeSet(VolleyMatch $match, MatchSet $set, string $winner): void
    {
        $set->update([
            'status' => 'completed',
            'winner' => $winner,
            'completed_at' => now()
        ]);

        // Actualizar contador de sets ganados
        if ($winner === 'home') {
            $match->increment('home_sets');
        } else {
            $match->increment('away_sets');
        }

        $this->recordEvent($match, 'set_end', [
            'set_number' => $set->set_number,
            'winner' => $winner,
            'home_score' => $set->home_score,
            'away_score' => $set->away_score,
            'timestamp' => now()->toISOString()
        ]);

        broadcast(new SetCompleted($match, $set, $winner));
    }

    private function checkMatchCompletion(VolleyMatch $match): array
    {
        $homeSets = $match->home_sets;
        $awaySets = $match->away_sets;
        
        $completed = false;
        $winner = null;
        
        // Mejor de 5 sets
        if ($homeSets >= 3) {
            $completed = true;
            $winner = 'home';
        } elseif ($awaySets >= 3) {
            $completed = true;
            $winner = 'away';
        }
        
        return [
            'completed' => $completed,
            'winner' => $winner
        ];
    }

    private function completeMatch(VolleyMatch $match, string $winner): void
    {
        $match->update([
            'status' => MatchStatus::Finished,
            'winner' => $winner,
            'finished_at' => now()
        ]);

        $this->recordEvent($match, 'match_end', [
            'winner' => $winner,
            'home_sets' => $match->home_sets,
            'away_sets' => $match->away_sets,
            'duration' => $match->started_at->diffInMinutes(now()),
            'timestamp' => now()->toISOString()
        ]);

        broadcast(new MatchFinished($match, $winner));
        
        // Limpiar cache
        Cache::forget("live_match_{$match->id}");
    }

    private function startNextSet(VolleyMatch $match): void
    {
        $nextSetNumber = $match->current_set + 1;
        
        MatchSet::create([
            'match_id' => $match->id,
            'set_number' => $nextSetNumber,
            'home_score' => 0,
            'away_score' => 0,
            'status' => 'in_progress',
            'started_at' => now()
        ]);
        
        $match->update(['current_set' => $nextSetNumber]);
        
        $this->recordEvent($match, 'set_start', [
            'set_number' => $nextSetNumber,
            'timestamp' => now()->toISOString()
        ]);
    }

    private function handleTimeout(VolleyMatch $match, array $data): void
    {
        // Lógica para manejar timeouts
        $team = $data['team'] ?? null;
        $timeoutType = $data['type'] ?? 'technical'; // technical, medical
        
        Log::info("Timeout solicitado", [
            'match_id' => $match->id,
            'team' => $team,
            'type' => $timeoutType
        ]);
    }

    private function handleSubstitution(VolleyMatch $match, array $data): void
    {
        // Lógica para manejar sustituciones
        $team = $data['team'] ?? null;
        $playerOut = $data['player_out'] ?? null;
        $playerIn = $data['player_in'] ?? null;
        
        Log::info("Sustitución realizada", [
            'match_id' => $match->id,
            'team' => $team,
            'player_out' => $playerOut,
            'player_in' => $playerIn
        ]);
    }

    private function handleCard(VolleyMatch $match, string $cardType, array $data): void
    {
        // Lógica para manejar tarjetas
        $player = $data['player'] ?? null;
        $reason = $data['reason'] ?? '';
        
        Log::info("Tarjeta mostrada", [
            'match_id' => $match->id,
            'card_type' => $cardType,
            'player' => $player,
            'reason' => $reason
        ]);
    }

    private function getCurrentRotations(VolleyMatch $match): array
    {
        $cacheKey = "rotations_{$match->id}";
        
        return Cache::get($cacheKey, [
            'home' => [],
            'away' => []
        ]);
    }

    private function calculateLiveStats(VolleyMatch $match): array
    {
        $sets = $match->sets;
        
        return [
            'total_points_home' => $sets->sum('home_score'),
            'total_points_away' => $sets->sum('away_score'),
            'sets_won_home' => $match->home_sets,
            'sets_won_away' => $match->away_sets,
            'current_set_number' => $match->current_set,
            'match_duration' => $match->started_at ? $match->started_at->diffInMinutes(now()) : 0
        ];
    }

    private function getScoreSummary(VolleyMatch $match): array
    {
        return [
            'sets' => [
                'home' => $match->home_sets,
                'away' => $match->away_sets
            ],
            'current_set' => $match->currentSet() ? [
                'number' => $match->current_set,
                'home_score' => $match->currentSet()->home_score,
                'away_score' => $match->currentSet()->away_score
            ] : null
        ];
    }
}