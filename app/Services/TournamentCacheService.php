<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\TournamentRegistration;
use App\Enums\TournamentStatus;
use App\Enums\MatchStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TournamentCacheService
{
    private const CACHE_PREFIX = 'tournaments';
    private const DEFAULT_TTL = 1800; // 30 minutos
    private const LIVE_TTL = 300; // 5 minutos para datos en vivo
    private const STANDINGS_TTL = 600; // 10 minutos para standings

    /**
     * Obtiene torneos activos desde caché
     */
    public function getActiveTournaments(?int $leagueId = null): Collection
    {
        $cacheKey = $this->buildCacheKey('active', $leagueId);
        
        return Cache::tags([self::CACHE_PREFIX, 'active'])
            ->remember($cacheKey, self::DEFAULT_TTL, function () use ($leagueId) {
                $query = Tournament::with([
                    'league',
                    'registrations.team.club'
                ])
                ->whereIn('status', [
                    TournamentStatus::Registration_Open,
                    TournamentStatus::In_Progress
                ])
                ->where('end_date', '>=', now());
                
                if ($leagueId) {
                    $query->where('league_id', $leagueId);
                }
                
                return $query->orderBy('start_date')
                    ->orderBy('name')
                    ->get();
            });
    }

    /**
     * Obtiene las posiciones/standings de un torneo
     */
    public function getTournamentStandings(int $tournamentId): array
    {
        $cacheKey = $this->buildCacheKey('standings', $tournamentId);
        
        return Cache::tags([self::CACHE_PREFIX, "tournament_{$tournamentId}"])
            ->remember($cacheKey, self::STANDINGS_TTL, function () use ($tournamentId) {
                // Obtener estadísticas básicas de equipos en el torneo
                $standings = TournamentRegistration::where('tournament_id', $tournamentId)
                    ->with(['team.club', 'team.players.userProfile'])
                    ->get()
                    ->map(function ($registration) {
                        return [
                            'team_id' => $registration->team_id,
                            'team_name' => $registration->team->name,
                            'club_name' => $registration->team->club->name,
                            'players_count' => $registration->team->players->count(),
                            'registration_date' => $registration->created_at,
                            'status' => $registration->status ?? 'registered'
                        ];
                    })
                    ->sortBy('registration_date')
                    ->values()
                    ->toArray();
                
                return $standings;
            });
    }

    /**
     * Obtiene próximos partidos/matches
     */
    public function getUpcomingMatches(?int $tournamentId = null, int $daysAhead = 7): Collection
    {
        $cacheKey = $this->buildCacheKey('upcoming_matches', $tournamentId, $daysAhead);
        
        return Cache::tags([self::CACHE_PREFIX, 'matches'])
            ->remember($cacheKey, self::LIVE_TTL, function () use ($tournamentId, $daysAhead) {
                // Como no tenemos tabla de matches, simulamos con datos de torneos
                $query = Tournament::with([
                    'league',
                    'registrations.team.club'
                ])
                ->where('start_date', '>=', now())
                ->where('start_date', '<=', now()->addDays($daysAhead))
                ->whereIn('status', [
                    TournamentStatus::Registration_Open,
                    TournamentStatus::In_Progress
                ]);
                
                if ($tournamentId) {
                    $query->where('id', $tournamentId);
                }
                
                return $query->orderBy('start_date')
                    ->get();
            });
    }

    /**
     * Obtiene estadísticas generales de torneos
     */
    public function getTournamentStats(?int $leagueId = null): array
    {
        $cacheKey = $this->buildCacheKey('stats', $leagueId);
        
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($leagueId) {
            $query = Tournament::query();
            
            if ($leagueId) {
                $query->where('league_id', $leagueId);
            }
            
            $stats = [
                'total' => $query->count(),
                'active' => $query->clone()->whereIn('status', [
                    TournamentStatus::Registration_Open,
                    TournamentStatus::In_Progress
                ])->count(),
                'upcoming' => $query->clone()
                    ->where('start_date', '>', now())
                    ->where('status', TournamentStatus::Upcoming)
                    ->count(),
                'completed' => $query->clone()
                    ->where('status', TournamentStatus::Finished)
                    ->count(),
            ];
            
            // Estadísticas por tipo
            $byType = $query->clone()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();
            
            $stats['by_type'] = $byType;
            
            return $stats;
        });
    }

    /**
     * Obtiene torneos por liga
     */
    public function getTournamentsByLeague(int $leagueId, ?TournamentStatus $status = null): Collection
    {
        $cacheKey = $this->buildCacheKey('by_league', $leagueId, $status?->value);
        
        return Cache::tags([self::CACHE_PREFIX, "league_{$leagueId}"])
            ->remember($cacheKey, self::DEFAULT_TTL, function () use ($leagueId, $status) {
                $query = Tournament::with([
                    'league',
                    'registrations'
                ])
                ->where('league_id', $leagueId);
                
                if ($status) {
                    $query->where('status', $status);
                }
                
                return $query->orderBy('start_date', 'desc')
                    ->get();
            });
    }

    /**
     * Obtiene registraciones de un torneo
     */
    public function getTournamentRegistrations(int $tournamentId): Collection
    {
        $cacheKey = $this->buildCacheKey('registrations', $tournamentId);
        
        return Cache::tags([self::CACHE_PREFIX, "tournament_{$tournamentId}"])
            ->remember($cacheKey, self::DEFAULT_TTL, function () use ($tournamentId) {
                return TournamentRegistration::with([
                    'team.club.league',
                    'team.players.userProfile'
                ])
                ->where('tournament_id', $tournamentId)
                ->orderBy('created_at')
                ->get();
            });
    }

    /**
     * Invalida el caché de un torneo específico
     */
    public function invalidateTournamentCache(int $tournamentId): void
    {
        Cache::tags(["tournament_{$tournamentId}"])->flush();
        
        // También invalidar listas generales
        Cache::tags(['active', 'matches'])->flush();
        
        Log::info('Tournament cache invalidated', ['tournament_id' => $tournamentId]);
    }

    /**
     * Invalida el caché de una liga específica
     */
    public function invalidateLeagueCache(int $leagueId): void
    {
        Cache::tags(["league_{$leagueId}"])->flush();
        
        Log::info('League tournaments cache invalidated', ['league_id' => $leagueId]);
    }

    /**
     * Invalida el caché de torneos activos
     */
    public function invalidateActiveCache(): void
    {
        Cache::tags(['active'])->flush();
        
        Log::info('Active tournaments cache invalidated');
    }

    /**
     * Invalida el caché de matches/partidos
     */
    public function invalidateMatchesCache(): void
    {
        Cache::tags(['matches'])->flush();
        
        Log::info('Matches cache invalidated');
    }

    /**
     * Invalida todo el caché de torneos
     */
    public function invalidateAllCache(): void
    {
        Cache::tags([self::CACHE_PREFIX])->flush();
        
        Log::info('All tournaments cache invalidated');
    }

    /**
     * Precarga datos de un torneo
     */
    public function preloadTournamentData(int $tournamentId): void
    {
        $this->getTournamentStandings($tournamentId);
        $this->getTournamentRegistrations($tournamentId);
        $this->getUpcomingMatches($tournamentId);
        
        Log::info('Tournament data preloaded', ['tournament_id' => $tournamentId]);
    }

    /**
     * Precarga datos de torneos activos
     */
    public function preloadActiveData(): void
    {
        $this->getActiveTournaments();
        $this->getUpcomingMatches();
        $this->getTournamentStats();
        
        Log::info('Active tournaments data preloaded');
    }

    /**
     * Construye la clave de caché
     */
    private function buildCacheKey(string $type, ...$params): string
    {
        $key = self::CACHE_PREFIX . ':' . $type;
        
        foreach ($params as $param) {
            if ($param !== null) {
                $key .= ':' . (is_bool($param) ? ($param ? '1' : '0') : $param);
            }
        }
        
        return $key;
    }
}