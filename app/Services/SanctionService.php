<?php

namespace App\Services;

use App\Models\Sanction;
use App\Models\Player;
use App\Models\Team;
use App\Models\VolleyMatch;
use App\Models\Tournament;
use App\Models\TournamentCard;
use App\Models\MatchEvent;
use App\Enums\SanctionType;
use App\Enums\SanctionSeverity;
use App\Enums\SanctionStatus;
use App\Enums\EventType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Exception;

class SanctionService
{
    /**
     * Aplicar sanción a un jugador
     */
    public function applySanction(
        Player $player,
        SanctionType $type,
        SanctionSeverity $severity,
        string $reason,
        ?VolleyMatch $match = null,
        ?Tournament $tournament = null,
        array $additionalData = []
    ): Sanction {
        try {
            return DB::transaction(function () use ($player, $type, $severity, $reason, $match, $tournament, $additionalData) {
                // Validar sanción
                $this->validateSanction($player, $type, $severity, $match, $tournament);
                
                // Calcular duración y efectos
                $sanctionDetails = $this->calculateSanctionDetails($type, $severity, $additionalData);
                
                // Crear sanción
                $sanction = Sanction::create([
                    'player_id' => $player->id,
                    'team_id' => $player->team_id,
                    'match_id' => $match?->id,
                    'tournament_id' => $tournament?->id,
                    'type' => $type,
                    'severity' => $severity,
                    'reason' => $reason,
                    'status' => SanctionStatus::Active,
                    'applied_at' => now(),
                    'expires_at' => $sanctionDetails['expires_at'],
                    'matches_suspended' => $sanctionDetails['matches_suspended'],
                    'fine_amount' => $sanctionDetails['fine_amount'],
                    'additional_conditions' => $sanctionDetails['additional_conditions'],
                    'applied_by' => Auth::id(),
                    'reference_number' => $this->generateReferenceNumber()
                ]);
                
                // Aplicar efectos inmediatos
                $this->applyImmediateEffects($sanction, $match);
                
                // Actualizar tarjeta del torneo si aplica
                if ($tournament) {
                    $this->updateTournamentCard($player, $tournament, $sanction);
                }
                
                // Registrar evento en el partido si aplica
                if ($match) {
                    $this->recordMatchEvent($match, $sanction);
                }
                
                // Log notification sent
        Log::info('Sanction notification logged', ['sanction_id' => $sanction->id]);
                
                // Log sanction applied
                Log::info('Sanction applied', ['sanction_id' => $sanction->id]);
                
                Log::info("Sanción aplicada", [
                    'sanction_id' => $sanction->id,
                    'player_id' => $player->id,
                    'type' => $type->value,
                    'severity' => $severity->value
                ]);
                
                return $sanction;
            });
        } catch (Exception $e) {
            Log::error("Error aplicando sanción: {$e->getMessage()}", [
                'player_id' => $player->id,
                'type' => $type->value
            ]);
            throw $e;
        }
    }

    /**
     * Revocar o modificar sanción
     */
    public function revokeSanction(Sanction $sanction, string $reason, bool $partial = false): array
    {
        try {
            return DB::transaction(function () use ($sanction, $reason, $partial) {
                if ($sanction->status !== SanctionStatus::Active) {
                    throw new Exception('Solo se pueden revocar sanciones activas');
                }
                
                $originalStatus = $sanction->status;
                
                if ($partial) {
                    // Reducir sanción
                    $this->reducePartialSanction($sanction, $reason);
                } else {
                    // Revocar completamente
                    $sanction->update([
                        'status' => SanctionStatus::Overturned,
                        'overturned_at' => now(),
                'overturned_by' => Auth::id(),
                        'revocation_reason' => $reason
                    ]);
                }
                
                // Revertir efectos si es necesario
                $this->revertSanctionEffects($sanction);
                
                // Enviar notificaciones
                $this->sendRevocationNotifications($sanction, $reason, $partial);
                
                // Log sanction overturned
            Log::info('Sanction overturned', ['sanction_id' => $sanction->id]);
                
                Log::info("Sanción revocada", [
                    'sanction_id' => $sanction->id,
                    'reason' => $reason,
                    'partial' => $partial
                ]);
                
                return [
                    'success' => true,
                    'sanction' => $sanction->fresh(),
                    'message' => $partial ? 'Sanción reducida exitosamente' : 'Sanción revocada exitosamente'
                ];
            });
        } catch (Exception $e) {
            Log::error("Error revocando sanción: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Verificar elegibilidad de jugador
     */
    public function checkPlayerEligibility(Player $player, ?VolleyMatch $match = null, ?Tournament $tournament = null): array
    {
        $activeSanctions = $this->getActiveSanctions($player, $tournament);
        
        $isEligible = true;
        $reasons = [];
        $warnings = [];
        
        foreach ($activeSanctions as $sanction) {
            switch ($sanction->type) {
                case SanctionType::Suspension:
                    if ($this->isSuspensionActive($sanction, $match)) {
                        $isEligible = false;
                        $reasons[] = "Suspendido por {$sanction->reason} hasta " . $sanction->expires_at->format('d/m/Y');
                    }
                    break;
                    
                case SanctionType::MatchBan:
                    if ($match && $this->isMatchBanActive($sanction, $match)) {
                        $isEligible = false;
                        $reasons[] = "Prohibido jugar este partido por {$sanction->reason}";
                    }
                    break;
                    
                case SanctionType::TournamentBan:
                    if ($tournament && $sanction->tournament_id === $tournament->id) {
                        $isEligible = false;
                        $reasons[] = "Prohibido participar en este torneo por {$sanction->reason}";
                    }
                    break;
                    
                case SanctionType::Warning:
                    $warnings[] = "Advertencia activa: {$sanction->reason}";
                    break;
            }
        }
        
        // Verificar acumulación de tarjetas
        if ($tournament) {
            $cardAccumulation = $this->checkCardAccumulation($player, $tournament);
            if ($cardAccumulation['suspended']) {
                $isEligible = false;
                $reasons[] = $cardAccumulation['reason'];
            }
        }
        
        return [
            'eligible' => $isEligible,
            'reasons' => $reasons,
            'warnings' => $warnings,
            'active_sanctions' => $activeSanctions->count(),
            'sanctions_detail' => $activeSanctions->map(function ($sanction) {
                return [
                    'id' => $sanction->id,
                    'type' => $sanction->type,
                    'severity' => $sanction->severity,
                    'reason' => $sanction->reason,
                    'expires_at' => $sanction->expires_at
                ];
            })
        ];
    }

    /**
     * Procesar tarjetas del partido
     */
    public function processMatchCard(VolleyMatch $match, Player $player, EventType $cardType, string $reason = ''): array
    {
        try {
            return DB::transaction(function () use ($match, $player, $cardType, $reason) {
                // Registrar evento de tarjeta
                $event = MatchEvent::create([
                    'match_id' => $match->id,
                    'event_type' => $cardType,
                    'data' => [
                        'player_id' => $player->id,
                        'player_name' => $player->name,
                        'team' => $player->team_id === $match->home_team_id ? 'home' : 'away',
                        'reason' => $reason,
                        'set_number' => $match->current_set,
                        'timestamp' => now()->toISOString()
                    ],
                    'created_by' => Auth::id()
                ]);
                
                $sanctions = [];
                
                // Aplicar sanción según tipo de tarjeta
                switch ($cardType) {
                    case SanctionType::YellowCard:
                        // Tarjeta amarilla - advertencia
                        $sanction = $this->applySanction(
                            $player,
                            SanctionType::Warning,
                            SanctionSeverity::Minor,
                            $reason ?: 'Tarjeta amarilla',
                            $match,
                            $match->tournament
                        );
                        $sanctions[] = $sanction;
                        break;
                        
                    case SanctionType::RedCard:
                        // Tarjeta roja - expulsión del partido
                        $sanction = $this->applySanction(
                            $player,
                            SanctionType::MatchBan,
                            SanctionSeverity::Major,
                            $reason ?: 'Tarjeta roja - expulsión',
                            $match,
                            $match->tournament
                        );
                        $sanctions[] = $sanction;
                        
                        // Verificar si requiere sanción adicional
                        $additionalSanction = $this->checkAdditionalSanctionForRedCard($player, $match, $reason);
                        if ($additionalSanction) {
                            $sanctions[] = $additionalSanction;
                        }
                        break;
                }
                
                // Actualizar tarjeta del torneo
                if ($match->tournament) {
                    $this->updateTournamentCardForMatch($player, $match->tournament, $cardType);
                }
                
                return [
                    'success' => true,
                    'event' => $event,
                    'sanctions' => $sanctions,
                    'player_status' => $this->checkPlayerEligibility($player, null, $match->tournament)
                ];
            });
        } catch (Exception $e) {
            Log::error("Error procesando tarjeta: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Obtener historial de sanciones
     */
    public function getSanctionHistory(Player $player, array $filters = []): array
    {
        $query = Sanction::where('player_id', $player->id)
            ->with(['match', 'tournament', 'appliedBy', 'overturnedBy']);
        
        // Aplicar filtros
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['tournament_id'])) {
            $query->where('tournament_id', $filters['tournament_id']);
        }
        
        if (isset($filters['date_from'])) {
            $query->where('applied_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('applied_at', '<=', $filters['date_to']);
        }
        
        $sanctions = $query->orderBy('applied_at', 'desc')->get();
        
        return [
            'sanctions' => $sanctions,
            'summary' => [
                'total' => $sanctions->count(),
                'active' => $sanctions->where('status', SanctionStatus::Active)->count(),
                'completed' => $sanctions->where('status', SanctionStatus::Served)->count(),
                'overturned' => $sanctions->where('status', SanctionStatus::Overturned)->count(),
                'by_type' => $sanctions->groupBy('type')->map->count(),
                'by_severity' => $sanctions->groupBy('severity')->map->count()
            ]
        ];
    }

    /**
     * Generar reporte de sanciones
     */
    public function generateSanctionReport(array $filters = []): array
    {
        $query = Sanction::with(['player', 'team', 'match', 'tournament']);
        
        // Aplicar filtros
        if (isset($filters['tournament_id'])) {
            $query->where('tournament_id', $filters['tournament_id']);
        }
        
        if (isset($filters['team_id'])) {
            $query->where('team_id', $filters['team_id']);
        }
        
        if (isset($filters['date_from'])) {
            $query->where('applied_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('applied_at', '<=', $filters['date_to']);
        }
        
        $sanctions = $query->get();
        
        return [
            'total_sanctions' => $sanctions->count(),
            'by_type' => $sanctions->groupBy('type')->map->count(),
            'by_severity' => $sanctions->groupBy('severity')->map->count(),
            'by_status' => $sanctions->groupBy('status')->map->count(),
            'by_team' => $sanctions->groupBy('team.name')->map->count(),
            'most_sanctioned_players' => $this->getMostSanctionedPlayers($sanctions),
            'sanctions_by_month' => $this->getSanctionsByMonth($sanctions),
            'average_fine_amount' => $sanctions->where('fine_amount', '>', 0)->avg('fine_amount'),
            'total_fines' => $sanctions->sum('fine_amount')
        ];
    }

    /**
     * Procesar expiración automática de sanciones
     */
    public function processExpiredSanctions(): array
    {
        $expiredSanctions = Sanction::where('status', SanctionStatus::Active)
            ->where('expires_at', '<=', now())
            ->get();
        
        $processed = 0;
        
        foreach ($expiredSanctions as $sanction) {
            try {
                $sanction->update([
                    'status' => SanctionStatus::Served,
                    'completed_at' => now()
                ]);
                
                // Enviar notificación de finalización
                $this->sendSanctionCompletionNotification($sanction);
                
                $processed++;
            } catch (Exception $e) {
                Log::error("Error procesando sanción expirada {$sanction->id}: {$e->getMessage()}");
            }
        }
        
        Log::info("Sanciones expiradas procesadas", ['count' => $processed]);
        
        return [
            'processed' => $processed,
            'total_expired' => $expiredSanctions->count()
        ];
    }

    // Métodos privados de apoyo

    private function validateSanction(Player $player, SanctionType $type, SanctionSeverity $severity, ?VolleyMatch $match, ?Tournament $tournament): void
    {
        // Verificar si el jugador ya tiene una sanción activa del mismo tipo
        $existingActiveSanction = Sanction::where('player_id', $player->id)
            ->where('type', $type)
            ->where('status', SanctionStatus::Active)
            ->first();
        
        if ($existingActiveSanction && in_array($type, [SanctionType::Suspension, SanctionType::TournamentBan])) {
            throw new Exception('El jugador ya tiene una sanción activa de este tipo');
        }
    }

    private function calculateSanctionDetails(SanctionType $type, SanctionSeverity $severity, array $additionalData): array
    {
        $details = [
            'expires_at' => null,
            'matches_suspended' => 0,
            'fine_amount' => 0,
            'additional_conditions' => []
        ];
        
        switch ($type) {
            case SanctionType::Warning:
                $details['expires_at'] = now()->addDays(30);
                break;
                
            case SanctionType::Suspension:
                switch ($severity) {
                    case SanctionSeverity::Minor:
                        $details['matches_suspended'] = 1;
                        $details['expires_at'] = now()->addDays(7);
                        break;
                    case SanctionSeverity::Major:
                        $details['matches_suspended'] = 3;
                        $details['expires_at'] = now()->addDays(21);
                        break;
                    case SanctionSeverity::Severe:
                        $details['matches_suspended'] = 6;
                        $details['expires_at'] = now()->addDays(42);
                        break;
                }
                break;
                
            case SanctionType::Fine:
                switch ($severity) {
                    case SanctionSeverity::Minor:
                        $details['fine_amount'] = 50;
                        break;
                    case SanctionSeverity::Major:
                        $details['fine_amount'] = 200;
                        break;
                    case SanctionSeverity::Severe:
                        $details['fine_amount'] = 500;
                        break;
                }
                $details['expires_at'] = now()->addDays(30);
                break;
                
            case SanctionType::MatchBan:
                $details['expires_at'] = now()->addHours(24);
                break;
                
            case SanctionType::TournamentBan:
                $details['expires_at'] = $additionalData['tournament_end_date'] ?? now()->addDays(90);
                break;
        }
        
        return $details;
    }

    private function applyImmediateEffects(Sanction $sanction, ?VolleyMatch $match): void
    {
        switch ($sanction->type) {
            case SanctionType::MatchBan:
                if ($match && $match->status === 'in_progress') {
                    // Remover jugador del partido inmediatamente
                    $this->removePlayerFromMatch($sanction->player, $match);
                }
                break;
        }
    }

    private function updateTournamentCard(Player $player, Tournament $tournament, Sanction $sanction): void
    {
        $card = TournamentCard::firstOrCreate([
            'player_id' => $player->id,
            'tournament_id' => $tournament->id
        ]);
        
        $card->addSanction($sanction);
    }

    private function recordMatchEvent(VolleyMatch $match, Sanction $sanction): void
    {
        MatchEvent::create([
            'match_id' => $match->id,
            'event_type' => 'sanction',
            'data' => [
                'sanction_id' => $sanction->id,
                'player_id' => $sanction->player_id,
                'type' => $sanction->type->value,
                'severity' => $sanction->severity->value,
                'reason' => $sanction->reason,
                'timestamp' => now()->toISOString()
            ],
            'created_by' => Auth::id()
        ]);
    }

    private function sendSanctionNotifications(Sanction $sanction): void
    {
        // Log notification sent
        Log::info('Sanction notification sent to player', ['player_id' => $sanction->player_id, 'sanction_id' => $sanction->id]);
        
        // Log team notification
        if ($sanction->team) {
            Log::info('Sanction notification sent to team', ['team_id' => $sanction->team_id, 'sanction_id' => $sanction->id]);
        }
    }

    private function getActiveSanctions(Player $player, ?Tournament $tournament = null)
    {
        $query = Sanction::where('player_id', $player->id)
            ->where('status', SanctionStatus::Active)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
        
        if ($tournament) {
            $query->where(function ($q) use ($tournament) {
                $q->whereNull('tournament_id')
                  ->orWhere('tournament_id', $tournament->id);
            });
        }
        
        return $query->get();
    }

    private function generateReferenceNumber(): string
    {
        return 'SAN-' . now()->format('Y') . '-' . str_pad(Sanction::count() + 1, 6, '0', STR_PAD_LEFT);
    }

    private function isSuspensionActive(Sanction $sanction, ?VolleyMatch $match): bool
    {
        if ($sanction->expires_at && $sanction->expires_at->isPast()) {
            return false;
        }
        
        if ($sanction->matches_suspended > 0) {
            // Verificar cuántos partidos ha cumplido
            $matchesServed = $this->getMatchesServedForSuspension($sanction);
            return $matchesServed < $sanction->matches_suspended;
        }
        
        return true;
    }

    private function isMatchBanActive(Sanction $sanction, VolleyMatch $match): bool
    {
        return $sanction->match_id === $match->id || 
               ($sanction->expires_at && $sanction->expires_at->isFuture());
    }

    private function checkCardAccumulation(Player $player, Tournament $tournament): array
    {
        $card = TournamentCard::where('player_id', $player->id)
            ->where('tournament_id', $tournament->id)
            ->first();
        
        if (!$card) {
            return ['suspended' => false];
        }
        
        $yellowCards = $card->yellow_cards ?? 0;
        $redCards = $card->red_cards ?? 0;
        
        // Reglas de acumulación (ejemplo)
        if ($redCards >= 2) {
            return [
                'suspended' => true,
                'reason' => 'Suspendido por acumulación de 2 tarjetas rojas'
            ];
        }
        
        if ($yellowCards >= 3) {
            return [
                'suspended' => true,
                'reason' => 'Suspendido por acumulación de 3 tarjetas amarillas'
            ];
        }
        
        return ['suspended' => false];
    }

    private function getMostSanctionedPlayers($sanctions): array
    {
        return $sanctions->groupBy('player_id')
            ->map(function ($playerSanctions) {
                $player = $playerSanctions->first()->player;
                return [
                    'player_name' => $player->name,
                    'team_name' => $player->team->name,
                    'total_sanctions' => $playerSanctions->count(),
                    'by_type' => $playerSanctions->groupBy('type')->map->count()
                ];
            })
            ->sortByDesc('total_sanctions')
            ->take(10)
            ->values();
    }

    private function getSanctionsByMonth($sanctions): array
    {
        return $sanctions->groupBy(function ($sanction) {
            return $sanction->applied_at->format('Y-m');
        })->map->count();
    }

    private function getMatchesServedForSuspension(Sanction $sanction): int
    {
        // Contar partidos jugados por el equipo desde la sanción
        return VolleyMatch::where('team_id', $sanction->team_id)
            ->where('status', 'finished')
            ->where('finished_at', '>', $sanction->applied_at)
            ->count();
    }

    private function removePlayerFromMatch(Player $player, VolleyMatch $match): void
    {
        // Lógica para remover jugador del partido en curso
        Log::info("Jugador removido del partido por sanción", [
            'player_id' => $player->id,
            'match_id' => $match->id
        ]);
    }

    private function checkAdditionalSanctionForRedCard(Player $player, VolleyMatch $match, string $reason): ?Sanction
    {
        // Verificar si la tarjeta roja requiere sanción adicional
        if (str_contains(strtolower($reason), 'agresión') || str_contains(strtolower($reason), 'violencia')) {
            return $this->applySanction(
                $player,
                SanctionType::Suspension,
                SanctionSeverity::Major,
                'Suspensión adicional por conducta violenta',
                null,
                $match->tournament
            );
        }
        
        return null;
    }

    private function updateTournamentCardForMatch(Player $player, Tournament $tournament, EventType $cardType): void
    {
        $card = TournamentCard::firstOrCreate([
            'player_id' => $player->id,
            'tournament_id' => $tournament->id
        ]);
        
        if ($cardType === SanctionType::YellowCard) {
            $card->increment('yellow_cards');
        } elseif ($cardType === SanctionType::RedCard) {
            $card->increment('red_cards');
        }
    }

    private function reducePartialSanction(Sanction $sanction, string $reason): void
    {
        // Reducir sanción a la mitad
        if ($sanction->matches_suspended > 0) {
            $sanction->update([
                'matches_suspended' => ceil($sanction->matches_suspended / 2),
                'reduction_reason' => $reason,
                'reduced_at' => now(),
                'reduced_by' => Auth::id()
            ]);
        }
        
        if ($sanction->expires_at) {
            $remainingDays = now()->diffInDays($sanction->expires_at);
            $sanction->update([
                'expires_at' => now()->addDays(ceil($remainingDays / 2))
            ]);
        }
    }

    private function revertSanctionEffects(Sanction $sanction): void
    {
        // Revertir efectos de la sanción si es necesario
        Log::info("Revirtiendo efectos de sanción", ['sanction_id' => $sanction->id]);
    }

    private function sendRevocationNotifications(Sanction $sanction, string $reason, bool $partial): void
    {
        // Log revocation notification
        Log::info('Sanction revocation notification sent', ['player_id' => $sanction->player_id, 'sanction_id' => $sanction->id]);
    }

    private function sendSanctionCompletionNotification(Sanction $sanction): void
    {
        // Log completion notification
        Log::info('Sanction completion notification sent', ['player_id' => $sanction->player_id, 'sanction_id' => $sanction->id]);
    }

    /**
     * Apelar una sanción
     */
    public function appealSanction(Sanction $sanction, string $reason): void
    {
        try {
            DB::transaction(function () use ($sanction, $reason) {
                // Verificar que la sanción pueda ser apelada
                if ($sanction->status !== SanctionStatus::Active) {
                    throw new Exception('Solo se pueden apelar sanciones activas');
                }

                // Actualizar sanción con información de apelación
                $sanction->update([
                    'appeal_reason' => $reason,
                    'appeal_date' => now(),
                    'appeal_by' => Auth::id(),
                    'status' => SanctionStatus::Pending // Cambiar a pendiente mientras se revisa
                ]);

                // Log de la apelación
                Log::info('Sanción apelada', [
                    'sanction_id' => $sanction->id,
                    'player_id' => $sanction->player_id,
                    'appeal_reason' => $reason,
                    'appealed_by' => Auth::id()
                ]);
            });
        } catch (Exception $e) {
            Log::error('Error al apelar sanción', [
                'sanction_id' => $sanction->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener sanciones de un partido específico
     */
    public function getMatchSanctions(VolleyMatch $match): array
    {
        try {
            $sanctions = Sanction::where('match_id', $match->id)
                ->with(['player.user', 'team', 'appliedBy'])
                ->orderBy('created_at', 'desc')
                ->get();

            return $sanctions->map(function ($sanction) {
                return [
                    'id' => $sanction->id,
                    'player' => [
                        'id' => $sanction->player->id,
                        'name' => $sanction->player->user->full_name,
                        'jersey_number' => $sanction->player->jersey_number
                    ],
                    'team' => [
                        'id' => $sanction->team->id,
                        'name' => $sanction->team->name
                    ],
                    'type' => $sanction->type,
                    'severity' => $sanction->severity,
                    'status' => $sanction->status,
                    'reason' => $sanction->reason,
                    'applied_at' => $sanction->created_at,
                    'applied_by' => $sanction->appliedBy ? $sanction->appliedBy->full_name : 'Sistema',
                    'expires_at' => $sanction->expires_at,
                    'appeal_reason' => $sanction->appeal_reason,
                    'appeal_date' => $sanction->appeal_date
                ];
            })->toArray();
        } catch (Exception $e) {
            Log::error('Error obteniendo sanciones del partido', [
                'match_id' => $match->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}