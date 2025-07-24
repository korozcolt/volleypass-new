<?php

namespace App\Console\Commands;

use App\Models\Club;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncClubStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clubs:sync-stats 
                            {--club= : ID específico del club a sincronizar}
                            {--force : Forzar actualización ignorando cache}
                            {--verbose : Mostrar información detallada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza las estadísticas de los clubes y actualiza contadores';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🏐 Iniciando sincronización de estadísticas de clubes...');
        
        $startTime = microtime(true);
        $clubId = $this->option('club');
        $force = $this->option('force');
        $verbose = $this->option('verbose');
        
        try {
            if ($clubId) {
                $this->syncSingleClub($clubId, $force, $verbose);
            } else {
                $this->syncAllClubs($force, $verbose);
            }
            
            // Actualizar estadísticas generales
            $this->updateGeneralStats($force, $verbose);
            
            // Limpiar cache si se forzó la actualización
            if ($force) {
                $this->clearRelatedCaches($verbose);
            }
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            $this->info("✅ Sincronización completada en {$duration} segundos");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante la sincronización: ' . $e->getMessage());
            
            if ($verbose) {
                $this->error($e->getTraceAsString());
            }
            
            return Command::FAILURE;
        }
    }

    /**
     * Sync statistics for a single club.
     */
    private function syncSingleClub(int $clubId, bool $force, bool $verbose): void
    {
        $club = Club::find($clubId);
        
        if (!$club) {
            throw new \Exception("Club con ID {$clubId} no encontrado");
        }
        
        if ($verbose) {
            $this->line("📊 Sincronizando club: {$club->nombre}");
        }
        
        $this->updateClubCounters($club, $force, $verbose);
        $this->updateClubFederationStatus($club, $verbose);
        $this->generateClubStatsCache($club, $force, $verbose);
    }

    /**
     * Sync statistics for all clubs.
     */
    private function syncAllClubs(bool $force, bool $verbose): void
    {
        $clubs = Club::all();
        $total = $clubs->count();
        
        if ($verbose) {
            $this->line("📊 Sincronizando {$total} clubes...");
        }
        
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();
        
        foreach ($clubs as $club) {
            $this->updateClubCounters($club, $force, false);
            $this->updateClubFederationStatus($club, false);
            $this->generateClubStatsCache($club, $force, false);
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
    }

    /**
     * Update club counters (players, directors, etc.).
     */
    private function updateClubCounters(Club $club, bool $force, bool $verbose): void
    {
        // Contar jugadoras activas
        $activePlayersCount = $club->jugadoras()->where('activa', true)->count();
        
        // Contar jugadoras federadas
        $federatedPlayersCount = $club->jugadoras()
            ->where('activa', true)
            ->where('es_federada', true)
            ->count();
        
        // Contar directivos activos
        $activeDirectorsCount = $club->directivos()
            ->wherePivot('is_active', true)
            ->count();
        
        // Contar torneos participados
        $tournamentsCount = $club->torneos()->count();
        
        // Actualizar contadores en la base de datos si es necesario
        $needsUpdate = $force || 
            $club->jugadoras_count !== $activePlayersCount ||
            $club->directivos_count !== $activeDirectorsCount;
        
        if ($needsUpdate) {
            $club->update([
                'jugadoras_count' => $activePlayersCount,
                'directivos_count' => $activeDirectorsCount,
                'torneos_count' => $tournamentsCount,
                'stats_updated_at' => now(),
            ]);
            
            if ($verbose) {
                $this->line("  ✓ Contadores actualizados: {$activePlayersCount} jugadoras, {$activeDirectorsCount} directivos");
            }
        }
    }

    /**
     * Update club federation status based on expiration dates.
     */
    private function updateClubFederationStatus(Club $club, bool $verbose): void
    {
        if ($club->es_federado && $club->vencimiento_federacion) {
            $isExpired = Carbon::parse($club->vencimiento_federacion)->isPast();
            
            if ($isExpired && $club->es_federado) {
                $club->update([
                    'es_federado' => false,
                    'tipo_federacion' => null,
                ]);
                
                if ($verbose) {
                    $this->line("  ⚠️  Federación expirada para: {$club->nombre}");
                }
            }
        }
    }

    /**
     * Generate and cache club statistics.
     */
    private function generateClubStatsCache(Club $club, bool $force, bool $verbose): void
    {
        $cacheKey = "club_stats_{$club->id}";
        
        if ($force || !Cache::has($cacheKey)) {
            $stats = [
                'total_jugadoras' => $club->jugadoras()->where('activa', true)->count(),
                'jugadoras_federadas' => $club->jugadoras()
                    ->where('activa', true)
                    ->where('es_federada', true)
                    ->count(),
                'directivos_activos' => $club->directivos()
                    ->wherePivot('is_active', true)
                    ->count(),
                'torneos_participados' => $club->torneos()->count(),
                'fecha_fundacion' => $club->fundacion,
                'anos_funcionamiento' => $club->fundacion ? 
                    Carbon::parse($club->fundacion)->diffInYears(now()) : 0,
                'updated_at' => now()->toISOString(),
            ];
            
            Cache::put($cacheKey, $stats, now()->addHours(6));
            
            if ($verbose) {
                $this->line("  ✓ Cache de estadísticas generado");
            }
        }
    }

    /**
     * Update general statistics.
     */
    private function updateGeneralStats(bool $force, bool $verbose): void
    {
        if ($verbose) {
            $this->line("📈 Actualizando estadísticas generales...");
        }
        
        $cacheKey = 'federation_stats';
        
        if ($force || !Cache::has($cacheKey)) {
            $stats = [
                'total_clubs' => Club::count(),
                'federated_clubs' => Club::where('es_federado', true)->count(),
                'clubs_by_department' => Club::select('departamento_id')
                    ->with('departamento:id,name')
                    ->get()
                    ->groupBy('departamento.name')
                    ->map->count(),
                'clubs_by_federation_type' => Club::where('es_federado', true)
                    ->select('tipo_federacion')
                    ->get()
                    ->groupBy('tipo_federacion')
                    ->map->count(),
                'monthly_growth' => $this->calculateMonthlyGrowth(),
                'updated_at' => now()->toISOString(),
            ];
            
            Cache::put($cacheKey, $stats, now()->addHours(12));
            
            if ($verbose) {
                $this->line("  ✓ Estadísticas generales actualizadas");
                $this->line("    - Total clubes: {$stats['total_clubs']}");
                $this->line("    - Clubes federados: {$stats['federated_clubs']}");
            }
        }
    }

    /**
     * Calculate monthly growth statistics.
     */
    private function calculateMonthlyGrowth(): array
    {
        $months = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $count = Club::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            
            $months[] = [
                'month' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
                'count' => $count,
            ];
        }
        
        return $months;
    }

    /**
     * Clear related caches.
     */
    private function clearRelatedCaches(bool $verbose): void
    {
        if ($verbose) {
            $this->line("🧹 Limpiando caches relacionados...");
        }
        
        $cacheKeys = [
            'clubs_count',
            'federated_clubs_count',
            'clubs_by_department',
            'federation_stats',
            'club_stats_widget',
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        if ($verbose) {
            $this->line("  ✓ Caches limpiados");
        }
    }
}