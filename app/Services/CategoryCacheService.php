<?php

namespace App\Services;

use App\Models\LeagueCategory;
use App\Enums\Gender;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class CategoryCacheService
{
    private const CACHE_PREFIX = 'categories';
    private const DEFAULT_TTL = 3600; // 1 hora
    private const METRICS_KEY = 'cache_metrics';

    /**
     * Obtiene las categorías para una liga específica con caché
     */
    public function getCategoriesForLeague(int $leagueId, ?Gender $gender = null, bool $activeOnly = true): Collection
    {
        $cacheKey = $this->buildCacheKey('league', $leagueId, $gender?->value, $activeOnly);
        
        $this->incrementMetric('requests');
        
        return Cache::tags([self::CACHE_PREFIX, "league_{$leagueId}"])
            ->remember($cacheKey, self::DEFAULT_TTL, function () use ($leagueId, $gender, $activeOnly) {
                $this->incrementMetric('misses');
                
                $query = LeagueCategory::where('league_id', $leagueId)
                    ->with(['league'])
                    ->orderBy('min_age')
                    ->orderBy('name');
                
                if ($gender) {
                    $query->where('gender', $gender);
                }
                
                if ($activeOnly) {
                    $query->where('is_active', true);
                }
                
                return $query->get();
            });
    }

    /**
     * Invalida el caché de una liga específica
     */
    public function invalidateLeagueCache(int $leagueId): void
    {
        Cache::tags(["league_{$leagueId}"])->flush();
        
        Log::info('Cache invalidated for league', ['league_id' => $leagueId]);
    }

    /**
     * Precarga las categorías de una liga en caché
     */
    public function preloadLeagueCategories(int $leagueId): void
    {
        // Precargar para todos los géneros
        $this->getCategoriesForLeague($leagueId, Gender::Female);
        $this->getCategoriesForLeague($leagueId, Gender::Male);
        $this->getCategoriesForLeague($leagueId, Gender::Mixed);
        $this->getCategoriesForLeague($leagueId); // Sin filtro de género
        
        Log::info('Categories preloaded for league', ['league_id' => $leagueId]);
    }

    /**
     * Precalienta el caché con las ligas más activas
     */
    public function warmupCache(): void
    {
        $activeLeagues = \App\Models\League::where('is_active', true)
            ->withCount('categories')
            ->having('categories_count', '>', 0)
            ->orderBy('categories_count', 'desc')
            ->limit(10)
            ->pluck('id');

        foreach ($activeLeagues as $leagueId) {
            $this->preloadLeagueCategories($leagueId);
        }
        
        Log::info('Cache warmed up', ['leagues_count' => $activeLeagues->count()]);
    }

    /**
     * Obtiene métricas de cache hit/miss
     */
    public function getCacheMetrics(): array
    {
        $metrics = Cache::get(self::METRICS_KEY, ['requests' => 0, 'misses' => 0]);
        
        $hitRate = $metrics['requests'] > 0 
            ? round((($metrics['requests'] - $metrics['misses']) / $metrics['requests']) * 100, 2)
            : 0;
            
        return [
            'requests' => $metrics['requests'],
            'hits' => $metrics['requests'] - $metrics['misses'],
            'misses' => $metrics['misses'],
            'hit_rate' => $hitRate
        ];
    }

    /**
     * Resetea las métricas de caché
     */
    public function resetMetrics(): void
    {
        Cache::forget(self::METRICS_KEY);
    }

    /**
     * Invalida todo el caché de categorías
     */
    public function invalidateAllCache(): void
    {
        Cache::tags([self::CACHE_PREFIX])->flush();
        
        Log::info('All categories cache invalidated');
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

    /**
     * Incrementa una métrica específica
     */
    private function incrementMetric(string $metric): void
    {
        $metrics = Cache::get(self::METRICS_KEY, ['requests' => 0, 'misses' => 0]);
        $metrics[$metric] = ($metrics[$metric] ?? 0) + 1;
        
        Cache::put(self::METRICS_KEY, $metrics, now()->addDay());
    }
}