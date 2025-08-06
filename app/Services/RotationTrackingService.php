<?php

namespace App\Services;

use App\Models\VolleyMatch;
use App\Models\Player;
use App\Models\Team;
use App\Models\MatchEvent;
use App\Models\PlayerRotation;
use App\Events\PlayerRotationUpdated;

use App\Enums\Position;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class RotationTrackingService
{
    /**
     * Actualizar rotación completa de un equipo
     */
    public function updateTeamRotation(VolleyMatch $match, string $team, array $playerIds): array
    {
        try {
            return DB::transaction(function () use ($match, $team, $playerIds) {
                // Validar entrada
                $this->validateRotationInput($match, $team, $playerIds);
                
                // Obtener rotación actual
                $currentRotation = $this->getCurrentRotation($match, $team);
                
                // Crear nueva rotación
                $newRotation = $this->createRotation($match, $team, $playerIds);
                
                // Registrar cambios
                $this->logRotationChanges($match, $team, $currentRotation, $newRotation);
                
                // Actualizar cache
                $this->updateRotationCache($match, $team, $newRotation);
                
                // Broadcast cambios
                broadcast(new PlayerRotationUpdated($match, $team, $newRotation));
                
                Log::info("Rotación actualizada", [
                    'match_id' => $match->id,
                    'team' => $team,
                    'players' => $playerIds
                ]);
                
                return [
                    'success' => true,
                    'rotation' => $newRotation,
                    'changes' => $this->getRotationChanges($currentRotation, $newRotation),
                    'message' => 'Rotación actualizada exitosamente'
                ];
            });
        } catch (Exception $e) {
            Log::error("Error actualizando rotación: {$e->getMessage()}", [
                'match_id' => $match->id,
                'team' => $team
            ]);
            throw $e;
        }
    }

    /**
     * Rotar jugadores (cambio de posiciones)
     */
    public function rotatePositions(VolleyMatch $match, string $team, string $direction = 'clockwise'): array
    {
        try {
            return DB::transaction(function () use ($match, $team, $direction) {
                $currentRotation = $this->getCurrentRotation($match, $team);
                
                if (empty($currentRotation)) {
                    throw new Exception('No hay rotación activa para rotar');
                }
                
                // Realizar rotación
                $newRotation = $this->performRotation($currentRotation, $direction);
                
                // Actualizar rotación
                $this->updateRotationPositions($match, $team, $newRotation);
                
                // Registrar evento
                $this->recordRotationEvent($match, $team, 'position_rotation', [
                    'direction' => $direction,
                    'previous_rotation' => $currentRotation,
                    'new_rotation' => $newRotation
                ]);
                
                // Actualizar cache
                $this->updateRotationCache($match, $team, $newRotation);
                
                // Broadcast
                broadcast(new PlayerRotationUpdated($match, $team, $newRotation));
                
                return [
                    'success' => true,
                    'rotation' => $newRotation,
                    'direction' => $direction,
                    'message' => 'Rotación de posiciones completada'
                ];
            });
        } catch (Exception $e) {
            Log::error("Error rotando posiciones: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Realizar sustitución de jugador
     */
    public function substitutePlayer(VolleyMatch $match, string $team, int $playerOutId, int $playerInId, int $position): array
    {
        try {
            return DB::transaction(function () use ($match, $team, $playerOutId, $playerInId, $position) {
                // Validar sustitución
                $this->validateSubstitution($match, $team, $playerOutId, $playerInId, $position);
                
                $currentRotation = $this->getCurrentRotation($match, $team);
                
                // Realizar sustitución
                $newRotation = $this->performSubstitution($currentRotation, $playerOutId, $playerInId, $position);
                
                // Actualizar rotación
                $this->updateRotationPositions($match, $team, $newRotation);
                
                // Registrar sustitución
                $this->recordSubstitution($match, $team, $playerOutId, $playerInId, $position);
                
                // Actualizar cache
                $this->updateRotationCache($match, $team, $newRotation);
                
                // Broadcast
                broadcast(new PlayerRotationUpdated($match, $team, $newRotation));
                
                return [
                    'success' => true,
                    'rotation' => $newRotation,
                    'substitution' => [
                        'player_out' => $playerOutId,
                        'player_in' => $playerInId,
                        'position' => $position
                    ],
                    'message' => 'Sustitución realizada exitosamente'
                ];
            });
        } catch (Exception $e) {
            Log::error("Error en sustitución: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Obtener rotación actual de un equipo
     */
    public function getCurrentRotation(VolleyMatch $match, string $team): array
    {
        $cacheKey = "rotation_{$match->id}_{$team}";
        
        return Cache::remember($cacheKey, 300, function () use ($match, $team) {
            $teamModel = $team === 'home' ? $match->homeTeam : $match->awayTeam;
            
            $rotation = PlayerRotation::where('match_id', $match->id)
                ->where('team_id', $teamModel->id)
                ->where('is_active', true)
                ->orderBy('position')
                ->with('player')
                ->get();
            
            return $rotation->map(function ($item) {
                return [
                    'position' => $item->position,
                    'player_id' => $item->player_id,
                    'player_name' => $item->player->name,
                    'player_number' => $item->player->number,
                    'is_libero' => $item->is_libero,
                    'substitution_count' => $item->substitution_count
                ];
            })->toArray();
        });
    }

    /**
     * Obtener historial de rotaciones
     */
    public function getRotationHistory(VolleyMatch $match, string $team): array
    {
        $teamModel = $team === 'home' ? $match->homeTeam : $match->awayTeam;
        
        $events = MatchEvent::where('match_id', $match->id)
            ->whereIn('event_type', [
                'rotation',
                'substitution',
                'player_in',
                'player_out'
            ])
            ->whereJsonContains('data->team', $team)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return $events->map(function ($event) {
            return [
                'id' => $event->id,
                'type' => $event->event_type,
                'data' => $event->data,
                'timestamp' => $event->created_at,
                'set_number' => $event->data['set_number'] ?? null
            ];
        })->toArray();
    }

    /**
     * Validar formación inicial
     */
    public function validateInitialFormation(VolleyMatch $match, string $team, array $playerIds): array
    {
        $errors = [];
        $warnings = [];
        
        // Verificar número de jugadores
        if (count($playerIds) !== 6) {
            $errors[] = 'Se requieren exactamente 6 jugadores en la formación inicial';
        }
        
        // Verificar jugadores únicos
        if (count($playerIds) !== count(array_unique($playerIds))) {
            $errors[] = 'No se pueden repetir jugadores en la formación';
        }
        
        // Verificar que los jugadores pertenezcan al equipo
        $teamModel = $team === 'home' ? $match->homeTeam : $match->awayTeam;
        $teamPlayerIds = $teamModel->players()->pluck('id')->toArray();
        
        foreach ($playerIds as $playerId) {
            if (!in_array($playerId, $teamPlayerIds)) {
                $errors[] = "El jugador {$playerId} no pertenece al equipo";
            }
        }
        
        // Verificar libero (opcional)
        $liberos = Player::whereIn('id', $playerIds)
            ->where('position', Position::Libero)
            ->count();
        
        if ($liberos > 1) {
            $warnings[] = 'Solo puede haber un libero en la formación inicial';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Obtener estadísticas de rotación
     */
    public function getRotationStats(VolleyMatch $match, string $team): array
    {
        $teamModel = $team === 'home' ? $match->homeTeam : $match->awayTeam;
        
        $rotations = PlayerRotation::where('match_id', $match->id)
            ->where('team_id', $teamModel->id)
            ->with('player')
            ->get();
        
        $substitutions = MatchEvent::where('match_id', $match->id)
            ->where('event_type', 'substitution')
            ->whereJsonContains('data->team', $team)
            ->count();
        
        $positionRotations = MatchEvent::where('match_id', $match->id)
            ->where('event_type', 'rotation')
            ->whereJsonContains('data->team', $team)
            ->count();
        
        return [
            'total_players_used' => $rotations->unique('player_id')->count(),
            'total_substitutions' => $substitutions,
            'total_position_rotations' => $positionRotations,
            'players_by_position' => $this->getPlayersByPosition($rotations),
            'substitution_efficiency' => $this->calculateSubstitutionEfficiency($match, $team),
            'most_used_players' => $this->getMostUsedPlayers($rotations)
        ];
    }

    /**
     * Resetear rotación (para nuevo set)
     */
    public function resetRotationForNewSet(VolleyMatch $match, string $team): array
    {
        try {
            return DB::transaction(function () use ($match, $team) {
                $teamModel = $team === 'home' ? $match->homeTeam : $match->awayTeam;
                
                // Desactivar rotación actual
                PlayerRotation::where('match_id', $match->id)
                    ->where('team_id', $teamModel->id)
                    ->update(['is_active' => false]);
                
                // Limpiar cache
                $cacheKey = "rotation_{$match->id}_{$team}";
                Cache::forget($cacheKey);
                
                // Registrar evento
                $this->recordRotationEvent($match, $team, 'rotation_reset', [
                    'set_number' => $match->current_set,
                    'reason' => 'new_set'
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Rotación reseteada para nuevo set'
                ];
            });
        } catch (Exception $e) {
            Log::error("Error reseteando rotación: {$e->getMessage()}");
            throw $e;
        }
    }

    // Métodos privados de apoyo

    private function validateRotationInput(VolleyMatch $match, string $team, array $playerIds): void
    {
        if (!in_array($team, ['home', 'away'])) {
            throw new Exception('Equipo inválido');
        }
        
        if (count($playerIds) !== 6) {
            throw new Exception('Se requieren exactamente 6 jugadores');
        }
        
        if (count($playerIds) !== count(array_unique($playerIds))) {
            throw new Exception('No se pueden repetir jugadores');
        }
    }

    private function createRotation(VolleyMatch $match, string $team, array $playerIds): array
    {
        $teamModel = $team === 'home' ? $match->homeTeam : $match->awayTeam;
        
        // Desactivar rotación anterior
        PlayerRotation::where('match_id', $match->id)
            ->where('team_id', $teamModel->id)
            ->update(['is_active' => false]);
        
        $rotation = [];
        
        foreach ($playerIds as $position => $playerId) {
            $player = Player::find($playerId);
            
            $rotationItem = PlayerRotation::create([
                'match_id' => $match->id,
                'team_id' => $teamModel->id,
                'player_id' => $playerId,
                'position' => $position + 1, // Posiciones 1-6
                'is_libero' => $player->position === Position::Libero,
                'is_active' => true,
                'set_number' => $match->current_set,
                'substitution_count' => 0
            ]);
            
            $rotation[] = [
                'position' => $position + 1,
                'player_id' => $playerId,
                'player_name' => $player->name,
                'player_number' => $player->number,
                'is_libero' => $player->position === Position::Libero,
                'substitution_count' => 0
            ];
        }
        
        return $rotation;
    }

    private function performRotation(array $currentRotation, string $direction): array
    {
        if ($direction === 'clockwise') {
            // Mover cada jugador a la siguiente posición
            $newRotation = [];
            foreach ($currentRotation as $item) {
                $newPosition = $item['position'] === 6 ? 1 : $item['position'] + 1;
                $newRotation[] = array_merge($item, ['position' => $newPosition]);
            }
            return $newRotation;
        } else {
            // Contrareloj
            $newRotation = [];
            foreach ($currentRotation as $item) {
                $newPosition = $item['position'] === 1 ? 6 : $item['position'] - 1;
                $newRotation[] = array_merge($item, ['position' => $newPosition]);
            }
            return $newRotation;
        }
    }

    private function performSubstitution(array $currentRotation, int $playerOutId, int $playerInId, int $position): array
    {
        $newRotation = [];
        
        foreach ($currentRotation as $item) {
            if ($item['position'] === $position && $item['player_id'] === $playerOutId) {
                $playerIn = Player::find($playerInId);
                $newRotation[] = [
                    'position' => $position,
                    'player_id' => $playerInId,
                    'player_name' => $playerIn->name,
                    'player_number' => $playerIn->number,
                    'is_libero' => $playerIn->position === Position::Libero,
                    'substitution_count' => $item['substitution_count'] + 1
                ];
            } else {
                $newRotation[] = $item;
            }
        }
        
        return $newRotation;
    }

    private function validateSubstitution(VolleyMatch $match, string $team, int $playerOutId, int $playerInId, int $position): void
    {
        // Verificar que el jugador que sale esté en la posición indicada
        $currentRotation = $this->getCurrentRotation($match, $team);
        $playerInPosition = collect($currentRotation)->firstWhere('position', $position);
        
        if (!$playerInPosition || $playerInPosition['player_id'] !== $playerOutId) {
            throw new Exception('El jugador no está en la posición indicada');
        }
        
        // Verificar límites de sustituciones
        $substitutions = MatchEvent::where('match_id', $match->id)
            ->where('event_type', 'substitution')
            ->whereJsonContains('data->team', $team)
            ->count();
        
        if ($substitutions >= 6) {
            throw new Exception('Se ha alcanzado el límite de sustituciones por set');
        }
    }

    private function updateRotationPositions(VolleyMatch $match, string $team, array $newRotation): void
    {
        $teamModel = $team === 'home' ? $match->homeTeam : $match->awayTeam;
        
        foreach ($newRotation as $item) {
            PlayerRotation::where('match_id', $match->id)
                ->where('team_id', $teamModel->id)
                ->where('player_id', $item['player_id'])
                ->where('is_active', true)
                ->update([
                    'position' => $item['position'],
                    'substitution_count' => $item['substitution_count']
                ]);
        }
    }

    private function recordRotationEvent(VolleyMatch $match, string $team, string $eventType, array $data): void
    {
        MatchEvent::create([
            'match_id' => $match->id,
            'event_type' => 'rotation',
            'data' => array_merge($data, [
                'team' => $team,
                'event_subtype' => $eventType,
                'timestamp' => now()->toISOString()
            ]),
            'created_by' => Auth::id()
        ]);
    }

    private function recordSubstitution(VolleyMatch $match, string $team, int $playerOutId, int $playerInId, int $position): void
    {
        MatchEvent::create([
            'match_id' => $match->id,
            'event_type' => 'substitution',
            'data' => [
                'team' => $team,
                'player_out' => $playerOutId,
                'player_in' => $playerInId,
                'position' => $position,
                'set_number' => $match->current_set,
                'timestamp' => now()->toISOString()
            ],
            'created_by' => Auth::id()
        ]);
    }

    private function updateRotationCache(VolleyMatch $match, string $team, array $rotation): void
    {
        $cacheKey = "rotation_{$match->id}_{$team}";
        Cache::put($cacheKey, $rotation, 300);
    }

    private function logRotationChanges(VolleyMatch $match, string $team, array $oldRotation, array $newRotation): void
    {
        Log::info("Cambio de rotación", [
            'match_id' => $match->id,
            'team' => $team,
            'old_rotation' => $oldRotation,
            'new_rotation' => $newRotation,
            'changes' => $this->getRotationChanges($oldRotation, $newRotation)
        ]);
    }

    private function getRotationChanges(array $oldRotation, array $newRotation): array
    {
        $changes = [];
        
        foreach ($newRotation as $newItem) {
            $oldItem = collect($oldRotation)->firstWhere('position', $newItem['position']);
            
            if (!$oldItem || $oldItem['player_id'] !== $newItem['player_id']) {
                $changes[] = [
                    'position' => $newItem['position'],
                    'old_player' => $oldItem['player_id'] ?? null,
                    'new_player' => $newItem['player_id']
                ];
            }
        }
        
        return $changes;
    }

    private function getPlayersByPosition(\Illuminate\Database\Eloquent\Collection $rotations): array
    {
        return $rotations->groupBy('position')
            ->map(function ($items) {
                return $items->unique('player_id')->count();
            })
            ->toArray();
    }

    private function calculateSubstitutionEfficiency(VolleyMatch $match, string $team): float
    {
        // Lógica para calcular eficiencia de sustituciones
        // Por ahora retorna un valor por defecto
        return 0.85;
    }

    private function getMostUsedPlayers(\Illuminate\Database\Eloquent\Collection $rotations): array
    {
        return $rotations->groupBy('player_id')
            ->map(function ($items, $playerId) {
                $player = $items->first()->player;
                return [
                    'player_id' => $playerId,
                    'player_name' => $player->name,
                    'times_used' => $items->count(),
                    'positions_played' => $items->pluck('position')->unique()->values()->toArray()
                ];
            })
            ->sortByDesc('times_used')
            ->take(5)
            ->values()
            ->toArray();
    }
}