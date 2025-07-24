<?php

namespace App\Services;

use App\Models\Player;
use App\Models\LeagueCategory;
use App\Models\PlayerCard;
use App\Models\Tournament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class QueryOptimizationService
{
    private const SLOW_QUERY_THRESHOLD = 100; // ms
    private const CACHE_TTL = 1800; // 30 minutos

    /**
     * Identifica queries lentas usando Telescope o logs
     */
    public function identifySlowQueries(int $thresholdMs = self::SLOW_QUERY_THRESHOLD): array
    {
        // En un entorno real, esto se integraría con Telescope
        // Por ahora, simulamos con queries conocidas problemáticas
        
        $slowQueries = [];
        
        // Simular detección de queries lentas
        $testQueries = [
            'categories_by_league' => $this->testCategoriesByLeagueQuery(),
            'federated_players' => $this->testFederatedPlayersQuery(),
            'qr_verification' => $this->testQrVerificationQuery(),
            'tournament_standings' => $this->testTournamentStandingsQuery(),
        ];
        
        foreach ($testQueries as $queryName => $executionTime) {
            if ($executionTime > $thresholdMs) {
                $slowQueries[] = [
                    'query_name' => $queryName,
                    'execution_time_ms' => $executionTime,
                    'threshold_ms' => $thresholdMs,
                    'optimization_needed' => true
                ];
            }
        }
        
        return $slowQueries;
    }

    /**
     * Optimiza consulta de categorías por liga
     */
    public function optimizeCategoriesByLeague(int $leagueId, ?string $gender = null): Collection
    {
        $cacheKey = "optimized_categories_{$leagueId}_{$gender}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($leagueId, $gender) {
            $query = LeagueCategory::select([
                'id',
                'league_id', 
                'name',
                'gender',
                'min_age',
                'max_age',
                'is_active'
            ])
            ->where('league_id', $leagueId)
            ->where('is_active', true);
            
            if ($gender) {
                $query->where('gender', $gender);
            }
            
            // Usar índice optimizado
            return $query->orderBy('min_age')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Optimiza consulta de jugadores federados
     */
    public function optimizeFederatedPlayers(?int $leagueId = null, int $limit = 100): Collection
    {
        $cacheKey = "optimized_federated_players_{$leagueId}_{$limit}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($leagueId, $limit) {
            $query = Player::select([
                'players.id',
                'players.federation_status',
                'players.federation_expires_at',
                'players.current_club_id',
                'players.category'
            ])
            ->with([
                'currentClub:id,name,league_id',
                'currentClub.league:id,name',
                'userProfile:user_id,first_name,last_name'
            ])
            ->whereNotNull('federation_status')
            ->where('federation_status', '!=', 'not_federated');
            
            if ($leagueId) {
                $query->whereHas('currentClub', function (Builder $q) use ($leagueId) {
                    $q->where('league_id', $leagueId);
                });
            }
            
            // Usar índice optimizado para federation_status
            return $query->orderBy('federation_expires_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Optimiza consulta de verificación QR
     */
    public function optimizeQrVerification(string $qrToken): ?PlayerCard
    {
        $cacheKey = "optimized_qr_{$qrToken}";
        
        return Cache::remember($cacheKey, 300, function () use ($qrToken) { // 5 min cache
            return PlayerCard::select([
                'id',
                'player_id',
                'qr_token',
                'is_active',
                'valid_until',
                'card_number'
            ])
            ->with([
                'player:id,current_club_id',
                'player.currentClub:id,name,league_id',
                'player.userProfile:user_id,first_name,last_name'
            ])
            ->where('qr_token', $qrToken)
            ->where('is_active', true)
            ->where('valid_until', '>', now())
            ->first();
        });
    }

    /**
     * Optimiza consulta de standings de torneos
     */
    public function optimizeTournamentStandings(int $tournamentId): array
    {
        $cacheKey = "optimized_tournament_standings_{$tournamentId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($tournamentId) {
            // Consulta optimizada usando joins en lugar de relaciones Eloquent
            $standings = DB::table('tournament_registrations as tr')
                ->select([
                    'tr.id as registration_id',
                    'tr.team_id',
                    't.name as team_name',
                    'c.name as club_name',
                    'l.name as league_name',
                    'tr.created_at as registration_date'
                ])
                ->join('teams as t', 'tr.team_id', '=', 't.id')
                ->join('clubs as c', 't.club_id', '=', 'c.id')
                ->join('leagues as l', 'c.league_id', '=', 'l.id')
                ->where('tr.tournament_id', $tournamentId)
                ->orderBy('tr.created_at')
                ->get()
                ->toArray();
            
            return $standings;
        });
    }

    /**
     * Implementa eager loading estratégico
     */
    public function getPlayersWithEagerLoading(array $playerIds): Collection
    {
        return Player::with([
            'userProfile:user_id,first_name,last_name,email',
            'currentClub:id,name,league_id',
            'currentClub.league:id,name',
            'playerCard:id,player_id,card_number,is_active,valid_until',
            'medicalCertificate:id,player_id,expires_at,status'
        ])
        ->whereIn('id', $playerIds)
        ->get();
    }

    /**
     * Optimiza consultas con paginación
     */
    public function getOptimizedPlayersPaginated(int $page = 1, int $perPage = 50, ?int $clubId = null)
    {
        $query = Player::select([
            'id',
            'current_club_id',
            'category',
            'federation_status',
            'is_active',
            'created_at'
        ])
        ->with([
            'userProfile:user_id,first_name,last_name',
            'currentClub:id,name'
        ]);
        
        if ($clubId) {
            $query->where('current_club_id', $clubId);
        }
        
        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Genera reporte de performance de queries
     */
    public function generatePerformanceReport(): array
    {
        $slowQueries = $this->identifySlowQueries();
        
        $report = [
            'timestamp' => now()->toISOString(),
            'slow_queries_count' => count($slowQueries),
            'slow_queries' => $slowQueries,
            'recommendations' => $this->getOptimizationRecommendations($slowQueries),
            'cache_stats' => $this->getCacheStats()
        ];
        
        Log::info('Query performance report generated', $report);
        
        return $report;
    }

    /**
     * Obtiene recomendaciones de optimización
     */
    private function getOptimizationRecommendations(array $slowQueries): array
    {
        $recommendations = [];
        
        foreach ($slowQueries as $query) {
            switch ($query['query_name']) {
                case 'categories_by_league':
                    $recommendations[] = 'Usar índice idx_league_categories_lookup y caché Redis';
                    break;
                case 'federated_players':
                    $recommendations[] = 'Implementar paginación y usar índice idx_players_federation_status';
                    break;
                case 'qr_verification':
                    $recommendations[] = 'Usar índice idx_qr_verification y caché de corta duración';
                    break;
                case 'tournament_standings':
                    $recommendations[] = 'Usar consultas SQL directas en lugar de Eloquent ORM';
                    break;
            }
        }
        
        return $recommendations;
    }

    /**
     * Obtiene estadísticas de caché
     */
    private function getCacheStats(): array
    {
        // En un entorno real, esto obtendría stats de Redis
        return [
            'hit_rate' => '85%',
            'total_keys' => 1250,
            'memory_usage' => '45MB',
            'avg_ttl' => '1800s'
        ];
    }

    /**
     * Simula test de query de categorías por liga
     */
    private function testCategoriesByLeagueQuery(): int
    {
        $start = microtime(true);
        
        // Simular query sin optimización
        LeagueCategory::where('league_id', 1)
            ->where('is_active', true)
            ->get();
        
        $end = microtime(true);
        
        return round(($end - $start) * 1000); // Convertir a ms
    }

    /**
     * Simula test de query de jugadores federados
     */
    private function testFederatedPlayersQuery(): int
    {
        $start = microtime(true);
        
        // Simular query sin optimización
        Player::whereNotNull('federation_status')
            ->with(['currentClub.league'])
            ->get();
        
        $end = microtime(true);
        
        return round(($end - $start) * 1000);
    }

    /**
     * Simula test de query de verificación QR
     */
    private function testQrVerificationQuery(): int
    {
        $start = microtime(true);
        
        // Simular query sin optimización
        PlayerCard::where('qr_token', 'test-token')
            ->where('is_active', true)
            ->with(['player.currentClub'])
            ->first();
        
        $end = microtime(true);
        
        return round(($end - $start) * 1000);
    }

    /**
     * Simula test de query de standings de torneo
     */
    private function testTournamentStandingsQuery(): int
    {
        $start = microtime(true);
        
        // Simular query compleja sin optimización
        Tournament::with([
            'registrations.team.club.league',
            'registrations.team.players'
        ])->find(1);
        
        $end = microtime(true);
        
        return round(($end - $start) * 1000);
    }
}