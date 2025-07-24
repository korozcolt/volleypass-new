<?php

namespace App\Console\Commands;

use App\Models\League;
use App\Models\Player;
use App\Enums\PlayerCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ValidateCategoryMigration extends Command
{
    protected $signature = 'categories:validate-migration 
                            {--league= : ID de la liga específica a validar}
                            {--fix : Aplicar correcciones automáticas}
                            {--report : Generar reporte detallado}';

    protected $description = 'Valida la migración del sistema de categorías y detecta inconsistencias';

    public function handle()
    {
        $this->info('🔍 Iniciando validación del sistema de categorías...');
        
        $leagueId = $this->option('league');
        $shouldFix = $this->option('fix');
        $generateReport = $this->option('report');
        
        $leagues = $leagueId 
            ? League::where('id', $leagueId)->get()
            : League::all();
            
        if ($leagues->isEmpty()) {
            $this->error('No se encontraron ligas para validar.');
            return 1;
        }
        
        $totalIssues = 0;
        $reportData = [];
        
        foreach ($leagues as $league) {
            $this->line("\n📋 Validando liga: {$league->name}");
            
            $issues = $this->validateLeague($league, $shouldFix);
            $totalIssues += count($issues);
            
            if ($generateReport) {
                $reportData[$league->id] = [
                    'league' => $league,
                    'issues' => $issues,
                    'stats' => $this->getLeagueStats($league),
                ];
            }
            
            if (empty($issues)) {
                $this->info('✅ Liga validada correctamente');
            } else {
                $this->warn("⚠️  Encontrados " . count($issues) . " problemas");
                foreach ($issues as $issue) {
                    $this->line("   - {$issue['type']}: {$issue['message']}");
                    if ($issue['fixed'] ?? false) {
                        $this->info('     ✓ Corregido automáticamente');
                    }
                }
            }
        }
        
        if ($generateReport) {
            $this->generateReport($reportData);
        }
        
        $this->line("\n📊 Resumen de validación:");
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Ligas validadas', $leagues->count()],
                ['Problemas encontrados', $totalIssues],
                ['Estado general', $totalIssues === 0 ? '✅ Correcto' : '⚠️ Requiere atención'],
            ]
        );
        
        return $totalIssues === 0 ? 0 : 1;
    }
    
    private function validateLeague(League $league, bool $shouldFix = false): array
    {
        $issues = [];
        
        // 1. Validar configuración de categorías
        $issues = array_merge($issues, $this->validateCategoryConfiguration($league, $shouldFix));
        
        // 2. Validar asignación de jugadoras
        $issues = array_merge($issues, $this->validatePlayerAssignments($league, $shouldFix));
        
        // 3. Validar integridad de datos
        $issues = array_merge($issues, $this->validateDataIntegrity($league, $shouldFix));
        
        // 4. Validar rendimiento
        $issues = array_merge($issues, $this->validatePerformance($league));
        
        return $issues;
    }
    
    private function validateCategoryConfiguration(League $league, bool $shouldFix): array
    {
        $issues = [];
        
        if (!$league->hasCustomCategories()) {
            return $issues;
        }
        
        $categories = $league->getActiveCategories();
        
        // Verificar solapamientos de edad
        foreach ($categories as $category1) {
            foreach ($categories as $category2) {
                if ($category1->id !== $category2->id && 
                    $category1->gender === $category2->gender) {
                    
                    $overlap = $this->checkAgeOverlap(
                        $category1->min_age, $category1->max_age,
                        $category2->min_age, $category2->max_age
                    );
                    
                    if ($overlap) {
                        $issues[] = [
                            'type' => 'OVERLAP',
                            'message' => "Solapamiento de edades entre {$category1->name} y {$category2->name}",
                            'severity' => 'high',
                            'data' => [
                                'category1' => $category1,
                                'category2' => $category2,
                            ],
                        ];
                    }
                }
            }
        }
        
        // Verificar gaps de edad
        $ageRanges = $categories->map(function ($cat) {
            return ['min' => $cat->min_age, 'max' => $cat->max_age, 'name' => $cat->name];
        })->sortBy('min')->values();
        
        for ($i = 0; $i < $ageRanges->count() - 1; $i++) {
            $current = $ageRanges[$i];
            $next = $ageRanges[$i + 1];
            
            if ($current['max'] + 1 < $next['min']) {
                $issues[] = [
                    'type' => 'GAP',
                    'message' => "Gap de edades entre {$current['name']} (hasta {$current['max']}) y {$next['name']} (desde {$next['min']})",
                    'severity' => 'medium',
                ];
            }
        }
        
        // Verificar códigos duplicados
        $codes = $categories->pluck('code')->toArray();
        $duplicates = array_diff_assoc($codes, array_unique($codes));
        
        if (!empty($duplicates)) {
            $issues[] = [
                'type' => 'DUPLICATE_CODE',
                'message' => 'Códigos de categoría duplicados: ' . implode(', ', array_unique($duplicates)),
                'severity' => 'high',
            ];
        }
        
        return $issues;
    }
    
    private function validatePlayerAssignments(League $league, bool $shouldFix): array
    {
        $issues = [];
        
        $players = Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })->get();
        
        $unassignedCount = 0;
        $wrongAssignmentCount = 0;
        
        foreach ($players as $player) {
            $currentAge = $player->age;
            $gender = $player->user->gender ?? 'female';
            
            if ($league->hasCustomCategories()) {
                $expectedCategory = $league->getActiveCategories()->first(function ($cat) use ($currentAge) {
                    return $currentAge >= $cat->min_age && $currentAge <= $cat->max_age;
                });
                
                if (!$expectedCategory) {
                    $unassignedCount++;
                }
            } else {
                $traditionalCategory = PlayerCategory::getForAge($currentAge, $gender);
                // Validar asignación tradicional si es necesario
            }
        }
        
        if ($unassignedCount > 0) {
            $issues[] = [
                'type' => 'UNASSIGNED_PLAYERS',
                'message' => "{$unassignedCount} jugadoras sin categoría asignada",
                'severity' => 'high',
                'count' => $unassignedCount,
            ];
        }
        
        return $issues;
    }
    
    private function validateDataIntegrity(League $league, bool $shouldFix): array
    {
        $issues = [];
        
        // Verificar integridad referencial
        $orphanedCategories = DB::table('league_categories')
            ->where('league_id', $league->id)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('leagues')
                    ->whereColumn('leagues.id', 'league_categories.league_id');
            })
            ->count();
            
        if ($orphanedCategories > 0) {
            $issues[] = [
                'type' => 'ORPHANED_CATEGORIES',
                'message' => "{$orphanedCategories} categorías huérfanas encontradas",
                'severity' => 'medium',
            ];
            
            if ($shouldFix) {
                DB::table('league_categories')
                    ->where('league_id', $league->id)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('leagues')
                            ->whereColumn('leagues.id', 'league_categories.league_id');
                    })
                    ->delete();
                    
                $issues[count($issues) - 1]['fixed'] = true;
            }
        }
        
        return $issues;
    }
    
    private function validatePerformance(League $league): array
    {
        $issues = [];
        
        // Medir tiempo de consulta de categorías
        $start = microtime(true);
        $league->getActiveCategories();
        $categoryQueryTime = (microtime(true) - $start) * 1000;
        
        if ($categoryQueryTime > 100) { // 100ms threshold
            $issues[] = [
                'type' => 'SLOW_CATEGORY_QUERY',
                'message' => "Consulta de categorías lenta: {$categoryQueryTime}ms",
                'severity' => 'low',
            ];
        }
        
        return $issues;
    }
    
    private function checkAgeOverlap(int $min1, int $max1, int $min2, int $max2): bool
    {
        return !($max1 < $min2 || $max2 < $min1);
    }
    
    private function getLeagueStats(League $league): array
    {
        $playersCount = Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })->count();
        
        return [
            'total_players' => $playersCount,
            'total_categories' => $league->categories()->active()->count(),
            'has_custom_categories' => $league->hasCustomCategories(),
            'clubs_count' => $league->clubs()->count(),
        ];
    }
    
    private function generateReport(array $reportData): void
    {
        $filename = 'category_validation_report_' . now()->format('Y-m-d_H-i-s') . '.json';
        $path = storage_path('app/reports/' . $filename);
        
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        file_put_contents($path, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info("📄 Reporte generado: {$path}");
    }
}