<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class DatabasePerformanceService
{
    private const SLOW_QUERY_THRESHOLD = 50; // ms
    private const ALERT_THRESHOLD = 100; // ms
    private const CACHE_KEY_PREFIX = 'db_performance';
    private const REPORT_CACHE_TTL = 3600; // 1 hora

    private array $queryLog = [];
    private bool $monitoring = false;

    /**
     * Inicia el monitoreo de queries
     */
    public function startMonitoring(): void
    {
        $this->monitoring = true;
        $this->queryLog = [];
        
        DB::listen(function ($query) {
            if ($this->monitoring) {
                $this->logQuery($query);
            }
        });
        
        Log::info('Database performance monitoring started');
    }

    /**
     * Detiene el monitoreo de queries
     */
    public function stopMonitoring(): void
    {
        $this->monitoring = false;
        
        Log::info('Database performance monitoring stopped', [
            'queries_logged' => count($this->queryLog)
        ]);
    }

    /**
     * Registra una query en el log de performance
     */
    private function logQuery($query): void
    {
        $executionTime = $query->time;
        
        $queryData = [
            'sql' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $executionTime,
            'timestamp' => now()->toISOString(),
            'is_slow' => $executionTime > self::SLOW_QUERY_THRESHOLD
        ];
        
        $this->queryLog[] = $queryData;
        
        // Log queries lentas inmediatamente
        if ($executionTime > self::SLOW_QUERY_THRESHOLD) {
            $this->logSlowQuery($queryData);
        }
        
        // Alertar si la query es extremadamente lenta
        if ($executionTime > self::ALERT_THRESHOLD) {
            $this->alertSlowQuery($queryData);
        }
    }

    /**
     * Registra una query lenta en el log
     */
    private function logSlowQuery(array $queryData): void
    {
        Log::warning('Slow query detected', [
            'execution_time_ms' => $queryData['time'],
            'sql' => $queryData['sql'],
            'bindings' => $queryData['bindings'],
            'threshold_ms' => self::SLOW_QUERY_THRESHOLD
        ]);
        
        // Guardar en caché para reportes
        $this->storeSlowQueryInCache($queryData);
    }

    /**
     * Envía alerta para queries extremadamente lentas
     */
    private function alertSlowQuery(array $queryData): void
    {
        Log::critical('Critical slow query detected', [
            'execution_time_ms' => $queryData['time'],
            'sql' => $queryData['sql'],
            'alert_threshold_ms' => self::ALERT_THRESHOLD
        ]);
        
        // En un entorno real, enviaríamos email o notificación Slack
        $this->sendSlowQueryAlert($queryData);
    }

    /**
     * Almacena query lenta en caché para reportes
     */
    private function storeSlowQueryInCache(array $queryData): void
    {
        $cacheKey = self::CACHE_KEY_PREFIX . ':slow_queries:' . date('Y-m-d-H');
        
        $slowQueries = Cache::get($cacheKey, []);
        $slowQueries[] = $queryData;
        
        // Mantener solo las últimas 100 queries por hora
        if (count($slowQueries) > 100) {
            $slowQueries = array_slice($slowQueries, -100);
        }
        
        Cache::put($cacheKey, $slowQueries, now()->addHours(24));
    }

    /**
     * Genera reporte de performance
     */
    public function generatePerformanceReport(string $period = '1h'): array
    {
        $cacheKey = self::CACHE_KEY_PREFIX . ':report:' . $period . ':' . date('Y-m-d-H');
        
        return Cache::remember($cacheKey, self::REPORT_CACHE_TTL, function () use ($period) {
            $slowQueries = $this->getSlowQueriesForPeriod($period);
            
            $report = [
                'period' => $period,
                'generated_at' => now()->toISOString(),
                'summary' => $this->generateSummary($slowQueries),
                'slow_queries' => $slowQueries,
                'recommendations' => $this->generateRecommendations($slowQueries),
                'database_stats' => $this->getDatabaseStats()
            ];
            
            return $report;
        });
    }

    /**
     * Obtiene queries lentas para un período específico
     */
    private function getSlowQueriesForPeriod(string $period): array
    {
        $hours = $this->parsePeriodToHours($period);
        $slowQueries = [];
        
        for ($i = 0; $i < $hours; $i++) {
            $hour = now()->subHours($i)->format('Y-m-d-H');
            $cacheKey = self::CACHE_KEY_PREFIX . ':slow_queries:' . $hour;
            
            $hourlyQueries = Cache::get($cacheKey, []);
            $slowQueries = array_merge($slowQueries, $hourlyQueries);
        }
        
        // Ordenar por tiempo de ejecución descendente
        usort($slowQueries, function ($a, $b) {
            return $b['time'] <=> $a['time'];
        });
        
        return array_slice($slowQueries, 0, 50); // Top 50 queries más lentas
    }

    /**
     * Genera resumen estadístico
     */
    private function generateSummary(array $slowQueries): array
    {
        if (empty($slowQueries)) {
            return [
                'total_slow_queries' => 0,
                'avg_execution_time' => 0,
                'max_execution_time' => 0,
                'queries_over_100ms' => 0
            ];
        }
        
        $executionTimes = array_column($slowQueries, 'time');
        
        return [
            'total_slow_queries' => count($slowQueries),
            'avg_execution_time' => round(array_sum($executionTimes) / count($executionTimes), 2),
            'max_execution_time' => max($executionTimes),
            'min_execution_time' => min($executionTimes),
            'queries_over_100ms' => count(array_filter($executionTimes, fn($time) => $time > 100)),
            'queries_over_200ms' => count(array_filter($executionTimes, fn($time) => $time > 200))
        ];
    }

    /**
     * Genera recomendaciones de optimización
     */
    private function generateRecommendations(array $slowQueries): array
    {
        $recommendations = [];
        
        foreach ($slowQueries as $query) {
            $sql = strtolower($query['sql']);
            
            if (strpos($sql, 'select') === 0) {
                if (strpos($sql, 'where') === false) {
                    $recommendations[] = 'Query sin WHERE clause detectada - considerar agregar filtros';
                }
                
                if (strpos($sql, 'order by') !== false && strpos($sql, 'limit') === false) {
                    $recommendations[] = 'ORDER BY sin LIMIT detectado - considerar paginación';
                }
                
                if (strpos($sql, 'join') !== false) {
                    $recommendations[] = 'Query con JOINs detectada - verificar índices en columnas de join';
                }
            }
        }
        
        // Recomendaciones generales
        $summary = $this->generateSummary($slowQueries);
        
        if ($summary['queries_over_100ms'] > 10) {
            $recommendations[] = 'Alto número de queries > 100ms - revisar índices de base de datos';
        }
        
        if ($summary['avg_execution_time'] > 75) {
            $recommendations[] = 'Tiempo promedio alto - considerar implementar caché Redis';
        }
        
        return array_unique($recommendations);
    }

    /**
     * Obtiene estadísticas de la base de datos
     */
    private function getDatabaseStats(): array
    {
        try {
            // Estadísticas básicas de MySQL/PostgreSQL
            $stats = [
                'connection_count' => $this->getConnectionCount(),
                'slow_query_log_enabled' => $this->isSlowQueryLogEnabled(),
                'query_cache_enabled' => $this->isQueryCacheEnabled(),
                'innodb_buffer_pool_size' => $this->getInnodbBufferPoolSize()
            ];
            
            return $stats;
        } catch (\Exception $e) {
            Log::error('Error getting database stats', ['error' => $e->getMessage()]);
            return ['error' => 'Unable to retrieve database stats'];
        }
    }

    /**
     * Envía alerta de query lenta
     */
    private function sendSlowQueryAlert(array $queryData): void
    {
        // En un entorno real, esto enviaría un email o notificación
        Log::alert('Slow query alert sent', [
            'execution_time' => $queryData['time'],
            'sql_preview' => substr($queryData['sql'], 0, 100) . '...'
        ]);
    }

    /**
     * Convierte período a horas
     */
    private function parsePeriodToHours(string $period): int
    {
        return match ($period) {
            '1h' => 1,
            '6h' => 6,
            '12h' => 12,
            '24h' => 24,
            default => 1
        };
    }

    /**
     * Obtiene número de conexiones activas
     */
    private function getConnectionCount(): int
    {
        try {
            $result = DB::select('SHOW STATUS LIKE "Threads_connected"');
            return (int) $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Verifica si el slow query log está habilitado
     */
    private function isSlowQueryLogEnabled(): bool
    {
        try {
            $result = DB::select('SHOW VARIABLES LIKE "slow_query_log"');
            return ($result[0]->Value ?? 'OFF') === 'ON';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verifica si el query cache está habilitado
     */
    private function isQueryCacheEnabled(): bool
    {
        try {
            $result = DB::select('SHOW VARIABLES LIKE "query_cache_type"');
            return ($result[0]->Value ?? 'OFF') !== 'OFF';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtiene el tamaño del InnoDB buffer pool
     */
    private function getInnodbBufferPoolSize(): string
    {
        try {
            $result = DB::select('SHOW VARIABLES LIKE "innodb_buffer_pool_size"');
            $bytes = (int) ($result[0]->Value ?? 0);
            return $this->formatBytes($bytes);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Formatea bytes a formato legible
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Limpia logs antiguos de performance
     */
    public function cleanupOldLogs(int $daysToKeep = 7): void
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        // Limpiar caché de queries lentas antiguas
        for ($i = $daysToKeep; $i <= 30; $i++) {
            $date = now()->subDays($i);
            for ($hour = 0; $hour < 24; $hour++) {
                $cacheKey = self::CACHE_KEY_PREFIX . ':slow_queries:' . $date->format('Y-m-d') . '-' . str_pad($hour, 2, '0', STR_PAD_LEFT);
                Cache::forget($cacheKey);
            }
        }
        
        Log::info('Database performance logs cleaned up', [
            'cutoff_date' => $cutoffDate->toISOString(),
            'days_kept' => $daysToKeep
        ]);
    }
}