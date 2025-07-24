<?php

namespace App\Services;

use App\Models\Player;
use App\Enums\FederationStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PlayerCacheService
{
    private const CACHE_PREFIX = 'players';
    private const DEFAULT_TTL = 1800; // 30 minutos
    private const FEDERATION_TTL = 900; // 15 minutos para datos de federación

    /**
     * Obtiene un jugador con información de federación desde caché
     */
    public function getPlayerWithFederation(int $playerId): ?Player
    {
        $cacheKey = $this->buildCacheKey('player_federation', $playerId);
        
        return Cache::remember($cacheKey, self::FEDERATION_TTL, function () use ($playerId) {
            return Player::with([
                'currentClub.league',
                'playerCard',
                'medicalCertificate',
                'playerDocuments',
                'userProfile'
            ])
            ->where('id', $playerId)
            ->first();
        });
    }

    /**
     * Obtiene jugadores federados con paginación
     */
    public function getFederatedPlayers(int $page = 1, int $perPage = 50, ?int $leagueId = null): LengthAwarePaginator
    {
        $cacheKey = $this->buildCacheKey('federated_players', $page, $perPage, $leagueId);
        
        return Cache::remember($cacheKey, self::FEDERATION_TTL, function () use ($page, $perPage, $leagueId) {
            $query = Player::with([
                'currentClub.league',
                'playerCard',
                'userProfile'
            ])
            ->whereNotNull('federation_status')
            ->where('federation_status', '!=', 'not_federated');
            
            if ($leagueId) {
                $query->whereHas('currentClub', function ($q) use ($leagueId) {
                    $q->where('league_id', $leagueId);
                });
            }
            
            return $query->orderBy('federation_expires_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        });
    }

    /**
     * Obtiene jugadores por club desde caché
     */
    public function getPlayersByClub(int $clubId, bool $activeOnly = true): Collection
    {
        $cacheKey = $this->buildCacheKey('club_players', $clubId, $activeOnly);
        
        return Cache::tags([self::CACHE_PREFIX, "club_{$clubId}"])
            ->remember($cacheKey, self::DEFAULT_TTL, function () use ($clubId, $activeOnly) {
                $query = Player::with([
                    'userProfile',
                    'playerCard',
                    'medicalCertificate'
                ])
                ->where('current_club_id', $clubId);
                
                if ($activeOnly) {
                    $query->where('is_active', true);
                }
                
                return $query->orderBy('created_at', 'desc')->get();
            });
    }

    /**
     * Obtiene estadísticas de jugadores por categoría
     */
    public function getPlayerStatsByCategory(int $leagueId): array
    {
        $cacheKey = $this->buildCacheKey('category_stats', $leagueId);
        
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($leagueId) {
            return Player::whereHas('currentClub', function ($q) use ($leagueId) {
                $q->where('league_id', $leagueId);
            })
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();
        });
    }

    /**
     * Obtiene jugadores con certificados médicos próximos a vencer
     */
    public function getPlayersWithExpiringMedicalCerts(int $daysAhead = 30): Collection
    {
        $cacheKey = $this->buildCacheKey('expiring_medical', $daysAhead);
        
        return Cache::remember($cacheKey, 3600, function () use ($daysAhead) {
            return Player::with([
                'medicalCertificate',
                'currentClub.league',
                'userProfile'
            ])
            ->whereHas('medicalCertificate', function ($q) use ($daysAhead) {
                $q->where('expires_at', '<=', now()->addDays($daysAhead))
                  ->where('expires_at', '>', now());
            })
            ->orderBy('medicalCertificate.expires_at')
            ->get();
        });
    }

    /**
     * Obtiene jugadores con carnets próximos a vencer
     */
    public function getPlayersWithExpiringCards(int $daysAhead = 30): Collection
    {
        $cacheKey = $this->buildCacheKey('expiring_cards', $daysAhead);
        
        return Cache::remember($cacheKey, 3600, function () use ($daysAhead) {
            return Player::with([
                'playerCard',
                'currentClub.league',
                'userProfile'
            ])
            ->whereHas('playerCard', function ($q) use ($daysAhead) {
                $q->where('valid_until', '<=', now()->addDays($daysAhead))
                  ->where('valid_until', '>', now())
                  ->where('is_active', true);
            })
            ->orderBy('playerCard.valid_until')
            ->get();
        });
    }

    /**
     * Invalida el caché de un jugador específico
     */
    public function invalidatePlayerCache(int $playerId): void
    {
        // Invalidar caché específico del jugador
        $patterns = [
            $this->buildCacheKey('player_federation', $playerId),
        ];
        
        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
        
        // Invalidar caché relacionado con el club del jugador
        $player = Player::find($playerId);
        if ($player && $player->current_club_id) {
            $this->invalidateClubCache($player->current_club_id);
        }
        
        // Invalidar listas generales
        $this->invalidateFederatedPlayersCache();
        
        Log::info('Player cache invalidated', ['player_id' => $playerId]);
    }

    /**
     * Invalida el caché de un club específico
     */
    public function invalidateClubCache(int $clubId): void
    {
        Cache::tags(["club_{$clubId}"])->flush();
        
        Log::info('Club players cache invalidated', ['club_id' => $clubId]);
    }

    /**
     * Invalida el caché de jugadores federados
     */
    public function invalidateFederatedPlayersCache(): void
    {
        // Buscar y eliminar todas las claves de jugadores federados
        $pattern = self::CACHE_PREFIX . ':federated_players:*';
        
        // En Redis, podríamos usar SCAN, pero para simplicidad usamos tags
        Cache::tags([self::CACHE_PREFIX, 'federated'])->flush();
        
        Log::info('Federated players cache invalidated');
    }

    /**
     * Invalida todo el caché de jugadores
     */
    public function invalidateAllCache(): void
    {
        Cache::tags([self::CACHE_PREFIX])->flush();
        
        Log::info('All players cache invalidated');
    }

    /**
     * Precarga jugadores de un club en caché
     */
    public function preloadClubPlayers(int $clubId): void
    {
        $this->getPlayersByClub($clubId, true);
        $this->getPlayersByClub($clubId, false);
        
        Log::info('Club players preloaded', ['club_id' => $clubId]);
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