<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use App\Services\MigrationValidationService;
use App\Services\CategoryValidationService;
use App\Services\CategoryAssignmentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ValidatePostMigration extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'categories:validate-migration
                            {--league= : ID de liga específica para validar}
                            {--fix : Intentar corregir automáticamente inconsistencias encontradas}
                            {--report : Generar reporte detallado de validación}
                            {--export : Exportar resultados a archivo JSON}
                            {--threshold=warning : Nivel mínimo de problemas a reportar (info|warning|critical)}';

    /**
     * The console command description.
     */
    protected $description = 'Valida la integridad de datos después de migración a categorías dinámicas';

    protected MigrationValidationService $migrationValidationService;
    protected CategoryValidationService $categoryValidationService;
    protected CategoryAssignmentService $assignmentService;

    public function __construct(
        MigrationValidationService $migrationValidationService,
        CategoryValidationService $categoryValidationService,
        CategoryAssignmentService $assignmentService
    ) {
        parent::__construct();
        $this->migrationValidationService = $migrationValidationService;
        $this->categoryValidationService = $categoryValidationService;
        $this->assignmentService = $assignmentService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Iniciando validación post-migración del sistema de categorías dinámicas');
        $this->newLine();

        try {
            // Obtener ligas a validar
            $leagues = $this->getLeaguesToValidate();

            if ($leagues->isEmpty()) {
                $this->warn('No se encontraron ligas migradas para validar.');
                return Command::SUCCESS;
            }

            $this->info("📊 Validando {$leagues->count()} liga(s) migrada(s)...");
            $this->newLine();

            // Ejecutar validación completa
            $results = $this->executePostMigrationValidation($leagues);

            // Mostrar resultados
            $this->displayValidationResults($results);

            // Generar reporte si se solicita
            if ($this->option('report')) {
                $this->generateValidationReport($results);
            }

            // Exportar resultados si se solicita
            if ($this->option('export')) {
                $this->exportValidationResults($results);
            }

            // Determinar código de salida
            return $this->determineExitCode($results);
        } catch (\Exception $e) {
            $this->error('Error durante la validación post-migración: ' . $e->getMessage());
            Log::error('Error en validación post-migración', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Obtiene las ligas que necesitan validación post-migración
     */
    protected function getLeaguesToValidate()
    {
        $leagueId = $this->option('league');

        if ($leagueId) {
            $league = League::find($leagueId);
            if (!$league) {
                $this->error("Liga con ID {$leagueId} no encontrada.");
                return collect();
            }

            if (!$league->hasCustomCategories()) {
                $this->warn("La liga '{$league->name}' no tiene categorías dinámicas configuradas.");
                return collect();
            }

            return collect([$league]);
        }

        // Obtener todas las ligas que tienen categorías dinámicas (migradas)
        return League::active()
            ->whereHas('categories')
            ->with(['categories', 'players.user', 'clubs'])
            ->get();
    }
    /**
     * Ejecuta la validación completa post-migración
     */
    protected function executePostMigrationValidation($leagues): array
    {
        $overallResults = [
            'validation_date' => now()->toISOString(),
            'total_leagues' => $leagues->count(),
            'leagues_validated' => 0,
            'leagues_with_critical_issues' => 0,
            'leagues_with_warnings' => 0,
            'leagues_passed' => 0,
            'total_issues_found' => 0,
            'total_issues_fixed' => 0,
            'league_results' => [],
            'summary_statistics' => []
        ];

        $progressBar = $this->output->createProgressBar($leagues->count());
        $progressBar->setFormat('verbose');
        $progressBar->start();

        foreach ($leagues as $league) {
            try {
                $this->line("Validando liga: {$league->name}");

                // Ejecutar validación de integridad completa
                $integrityResult = $this->migrationValidationService->verifyMigrationIntegrity($league);

                // Ejecutar validaciones adicionales específicas
                $additionalValidations = $this->performAdditionalValidations($league);

                // Consolidar resultados
                $leagueResult = $this->consolidateLeagueResults($league, $integrityResult, $additionalValidations);

                // Intentar correcciones automáticas si se solicita
                if ($this->option('fix') && $this->hasFixableIssues($leagueResult)) {
                    $fixResults = $this->attemptAutomaticFixes($league, $leagueResult);
                    $leagueResult['fix_results'] = $fixResults;
                    $overallResults['total_issues_fixed'] += $fixResults['fixes_successful'];
                }

                $overallResults['league_results'][$league->id] = $leagueResult;
                $overallResults['leagues_validated']++;

                // Actualizar contadores por severidad
                switch ($leagueResult['overall_status']) {
                    case 'critical':
                        $overallResults['leagues_with_critical_issues']++;
                        break;
                    case 'warning':
                        $overallResults['leagues_with_warnings']++;
                        break;
                    case 'valid':
                        $overallResults['leagues_passed']++;
                        break;
                }

                $overallResults['total_issues_found'] += count($leagueResult['all_issues']);
            } catch (\Exception $e) {
                $this->error("Error validando liga {$league->name}: " . $e->getMessage());
                Log::error('Error en validación de liga específica', [
                    'league_id' => $league->id,
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Generar estadísticas de resumen
        $overallResults['summary_statistics'] = $this->generateSummaryStatistics($overallResults);

        return $overallResults;
    }

    /**
     * Realiza validaciones adicionales específicas para post-migración
     */
    protected function performAdditionalValidations(League $league): array
    {
        $validations = [];

        try {
            // 1. Validación de completitud de migración
            $validations['migration_completeness'] = $this->validateMigrationCompleteness($league);

            // 2. Validación de consistencia de categorías
            $validations['category_consistency'] = $this->validateCategoryConsistency($league);

            // 3. Validación de asignaciones correctas
            $validations['assignment_accuracy'] = $this->validateAssignmentAccuracy($league);

            // 4. Validación de performance post-migración
            $validations['performance_impact'] = $this->validatePerformanceImpact($league);

            // 5. Validación de compatibilidad con sistema existente
            $validations['backward_compatibility'] = $this->validateBackwardCompatibility($league);
        } catch (\Exception $e) {
            Log::error('Error en validaciones adicionales', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);
        }

        return $validations;
    }

    /**
     * Valida que la migración esté completa
     */
    protected function validateMigrationCompleteness(League $league): array
    {
        $validation = [
            'name' => 'Completitud de Migración',
            'status' => 'passed',
            'issues' => [],
            'metrics' => []
        ];

        try {
            // Verificar que todas las jugadoras tienen categoría asignada
            $playersWithoutCategory = $league->players()
                ->whereNull('category')
                ->orWhere('category', '')
                ->count();

            $totalPlayers = $league->players()->count();
            $migrationCompleteness = $totalPlayers > 0 ? (($totalPlayers - $playersWithoutCategory) / $totalPlayers) * 100 : 100;

            $validation['metrics'] = [
                'total_players' => $totalPlayers,
                'players_without_category' => $playersWithoutCategory,
                'migration_completeness_percentage' => round($migrationCompleteness, 2)
            ];

            if ($playersWithoutCategory > 0) {
                $severity = $playersWithoutCategory > ($totalPlayers * 0.1) ? 'critical' : 'warning';
                $validation['status'] = $severity === 'critical' ? 'failed' : 'warning';
                $validation['issues'][] = [
                    'type' => 'incomplete_migration',
                    'severity' => $severity,
                    'message' => "{$playersWithoutCategory} de {$totalPlayers} jugadoras sin categoría asignada",
                    'recommendation' => 'Completar asignación de categorías faltantes'
                ];
            }

            // Verificar que todas las categorías necesarias están configuradas
            $expectedCategories = $this->getExpectedCategoriesForLeague($league);
            $configuredCategories = $league->getActiveCategories()->pluck('name')->toArray();
            $missingCategories = array_diff($expectedCategories, $configuredCategories);

            if (!empty($missingCategories)) {
                $validation['status'] = 'warning';
                $validation['issues'][] = [
                    'type' => 'missing_categories',
                    'severity' => 'warning',
                    'message' => 'Categorías esperadas no configuradas: ' . implode(', ', $missingCategories),
                    'recommendation' => 'Configurar categorías faltantes según normativas de la liga'
                ];
            }
        } catch (\Exception $e) {
            $validation['status'] = 'error';
            $validation['issues'][] = [
                'type' => 'validation_error',
                'severity' => 'critical',
                'message' => 'Error validando completitud: ' . $e->getMessage()
            ];
        }

        return $validation;
    }

    /**
     * Valida la consistencia de categorías
     */
    protected function validateCategoryConsistency(League $league): array
    {
        $validation = [
            'name' => 'Consistencia de Categorías',
            'status' => 'passed',
            'issues' => [],
            'metrics' => []
        ];

        try {
            $categories = $league->getActiveCategories();

            // Verificar consistencia de rangos de edad
            $ageInconsistencies = 0;
            foreach ($categories as $category) {
                if ($category->min_age > $category->max_age) {
                    $ageInconsistencies++;
                    $validation['issues'][] = [
                        'type' => 'invalid_age_range',
                        'severity' => 'critical',
                        'message' => "Categoría '{$category->name}' tiene rango de edad inválido ({$category->min_age}-{$category->max_age})",
                        'recommendation' => 'Corregir rangos de edad en configuración'
                    ];
                }
            }

            // Verificar superposiciones problemáticas
            $overlaps = $this->findCategoryOverlaps($categories);
            $problematicOverlaps = array_filter($overlaps, fn($overlap) => $overlap['severity'] === 'critical');

            if (!empty($problematicOverlaps)) {
                $validation['status'] = 'failed';
                foreach ($problematicOverlaps as $overlap) {
                    $validation['issues'][] = [
                        'type' => 'category_overlap',
                        'severity' => 'critical',
                        'message' => $overlap['message'],
                        'recommendation' => 'Ajustar rangos para eliminar superposiciones problemáticas'
                    ];
                }
            }

            $validation['metrics'] = [
                'total_categories' => $categories->count(),
                'age_inconsistencies' => $ageInconsistencies,
                'problematic_overlaps' => count($problematicOverlaps)
            ];

            if ($ageInconsistencies > 0 || !empty($problematicOverlaps)) {
                $validation['status'] = 'failed';
            }
        } catch (\Exception $e) {
            $validation['status'] = 'error';
            $validation['issues'][] = [
                'type' => 'validation_error',
                'severity' => 'critical',
                'message' => 'Error validando consistencia: ' . $e->getMessage()
            ];
        }

        return $validation;
    }
    /**

     * Valida la precisión de las asignaciones
     */
    protected function validateAssignmentAccuracy(League $league): array
    {
        $validation = [
            'name' => 'Precisión de Asignaciones',
            'status' => 'passed',
            'issues' => [],
            'metrics' => []
        ];

        try {
            $players = $league->players()->with('user')->get();
            $incorrectAssignments = 0;
            $totalAssignments = 0;

            foreach ($players as $player) {
                if (empty($player->category)) {
                    continue;
                }

                $totalAssignments++;
                $currentCategory = $player->category;
                $suggestedCategory = $this->assignmentService->assignAutomaticCategory($player);

                if ($suggestedCategory && $suggestedCategory !== $currentCategory) {
                    $incorrectAssignments++;

                    // Solo reportar como issue si la diferencia es significativa
                    $age = $player->user->age ?? 0;
                    if ($this->isSignificantCategoryMismatch($currentCategory, $suggestedCategory, $age)) {
                        $validation['issues'][] = [
                            'type' => 'incorrect_assignment',
                            'severity' => 'warning',
                            'message' => "Jugadora {$player->user->full_name} (edad: {$age}) asignada a '{$currentCategory}' pero debería estar en '{$suggestedCategory}'",
                            'recommendation' => 'Revisar y reasignar si es necesario'
                        ];
                    }
                }
            }

            $accuracyPercentage = $totalAssignments > 0 ? (($totalAssignments - $incorrectAssignments) / $totalAssignments) * 100 : 100;

            $validation['metrics'] = [
                'total_assignments' => $totalAssignments,
                'incorrect_assignments' => $incorrectAssignments,
                'accuracy_percentage' => round($accuracyPercentage, 2)
            ];

            if ($accuracyPercentage < 90) {
                $validation['status'] = 'warning';
                $validation['issues'][] = [
                    'type' => 'low_accuracy',
                    'severity' => 'warning',
                    'message' => "Precisión de asignaciones baja: {$accuracyPercentage}%",
                    'recommendation' => 'Revisar configuración de categorías y reasignar jugadoras'
                ];
            }
        } catch (\Exception $e) {
            $validation['status'] = 'error';
            $validation['issues'][] = [
                'type' => 'validation_error',
                'severity' => 'critical',
                'message' => 'Error validando precisión: ' . $e->getMessage()
            ];
        }

        return $validation;
    }

    /**
     * Valida el impacto en performance
     */
    protected function validatePerformanceImpact(League $league): array
    {
        $validation = [
            'name' => 'Impacto en Performance',
            'status' => 'passed',
            'issues' => [],
            'metrics' => []
        ];

        try {
            // Medir tiempo de consultas críticas
            $startTime = microtime(true);

            // Consulta 1: Obtener categorías activas
            $categoriesQuery = $league->getActiveCategories();
            $categoriesTime = microtime(true) - $startTime;

            // Consulta 2: Distribución de jugadoras por categoría
            $startTime = microtime(true);
            $distribution = $league->getPlayersStatsByCategory();
            $distributionTime = microtime(true) - $startTime;

            // Consulta 3: Asignación de categoría para jugadora
            $startTime = microtime(true);
            $samplePlayer = $league->players()->with('user')->first();
            if ($samplePlayer) {
                $this->assignmentService->assignAutomaticCategory($samplePlayer);
            }
            $assignmentTime = microtime(true) - $startTime;

            $validation['metrics'] = [
                'categories_query_time' => round($categoriesTime * 1000, 2), // ms
                'distribution_query_time' => round($distributionTime * 1000, 2), // ms
                'assignment_time' => round($assignmentTime * 1000, 2), // ms
                'total_categories' => $categoriesQuery->count(),
                'total_players' => $league->players()->count()
            ];

            // Alertar si las consultas son muy lentas
            if ($categoriesTime > 0.1) { // 100ms
                $validation['status'] = 'warning';
                $validation['issues'][] = [
                    'type' => 'slow_query',
                    'severity' => 'warning',
                    'message' => 'Consulta de categorías lenta: ' . round($categoriesTime * 1000, 2) . 'ms',
                    'recommendation' => 'Considerar optimización de índices'
                ];
            }

            if ($assignmentTime > 0.05) { // 50ms
                $validation['status'] = 'warning';
                $validation['issues'][] = [
                    'type' => 'slow_assignment',
                    'severity' => 'warning',
                    'message' => 'Asignación de categoría lenta: ' . round($assignmentTime * 1000, 2) . 'ms',
                    'recommendation' => 'Optimizar lógica de asignación o implementar caché'
                ];
            }
        } catch (\Exception $e) {
            $validation['status'] = 'error';
            $validation['issues'][] = [
                'type' => 'validation_error',
                'severity' => 'critical',
                'message' => 'Error validando performance: ' . $e->getMessage()
            ];
        }

        return $validation;
    }

    /**
     * Valida la compatibilidad con el sistema existente
     */
    protected function validateBackwardCompatibility(League $league): array
    {
        $validation = [
            'name' => 'Compatibilidad con Sistema Existente',
            'status' => 'passed',
            'issues' => [],
            'metrics' => []
        ];

        try {
            // Verificar que las categorías dinámicas mapean correctamente al enum
            $dynamicCategories = $league->getActiveCategories()->pluck('name')->toArray();
            $enumValues = ['Mini', 'Pre_Mini', 'Infantil', 'Cadete', 'Juvenil', 'Mayores', 'Masters'];

            $unmappedCategories = array_diff($dynamicCategories, $enumValues);
            $missingEnumCategories = array_diff($enumValues, $dynamicCategories);

            if (!empty($unmappedCategories)) {
                $validation['status'] = 'warning';
                $validation['issues'][] = [
                    'type' => 'unmapped_categories',
                    'severity' => 'warning',
                    'message' => 'Categorías dinámicas no mapeadas al enum: ' . implode(', ', $unmappedCategories),
                    'recommendation' => 'Verificar compatibilidad con código existente'
                ];
            }

            // Verificar que el fallback funciona correctamente
            $playersWithEnumCategories = $league->players()
                ->whereIn('category', $enumValues)
                ->count();

            $totalPlayers = $league->players()->count();
            $enumCompatibility = $totalPlayers > 0 ? ($playersWithEnumCategories / $totalPlayers) * 100 : 100;

            $validation['metrics'] = [
                'dynamic_categories' => count($dynamicCategories),
                'enum_categories' => count($enumValues),
                'unmapped_categories' => count($unmappedCategories),
                'missing_enum_categories' => count($missingEnumCategories),
                'enum_compatibility_percentage' => round($enumCompatibility, 2)
            ];

            if ($enumCompatibility < 95) {
                $validation['status'] = 'warning';
                $validation['issues'][] = [
                    'type' => 'low_enum_compatibility',
                    'severity' => 'warning',
                    'message' => "Compatibilidad con enum baja: {$enumCompatibility}%",
                    'recommendation' => 'Ajustar nombres de categorías para mejor compatibilidad'
                ];
            }
        } catch (\Exception $e) {
            $validation['status'] = 'error';
            $validation['issues'][] = [
                'type' => 'validation_error',
                'severity' => 'critical',
                'message' => 'Error validando compatibilidad: ' . $e->getMessage()
            ];
        }

        return $validation;
    }

    /**
     * Consolida los resultados de todas las validaciones para una liga
     */
    protected function consolidateLeagueResults(League $league, array $integrityResult, array $additionalValidations): array
    {
        $allIssues = $integrityResult['issues_found'] ?? [];

        // Agregar issues de validaciones adicionales
        foreach ($additionalValidations as $validation) {
            if (isset($validation['issues'])) {
                $allIssues = array_merge($allIssues, $validation['issues']);
            }
        }

        // Determinar estado general
        $criticalIssues = array_filter($allIssues, fn($issue) => $issue['severity'] === 'critical');
        $warningIssues = array_filter($allIssues, fn($issue) => $issue['severity'] === 'warning');

        $overallStatus = 'valid';
        if (!empty($criticalIssues)) {
            $overallStatus = 'critical';
        } elseif (!empty($warningIssues)) {
            $overallStatus = 'warning';
        }

        return [
            'league_id' => $league->id,
            'league_name' => $league->name,
            'overall_status' => $overallStatus,
            'integrity_result' => $integrityResult,
            'additional_validations' => $additionalValidations,
            'all_issues' => $allIssues,
            'critical_issues_count' => count($criticalIssues),
            'warning_issues_count' => count($warningIssues),
            'validation_timestamp' => now()->toISOString()
        ];
    }
    /**

     * Intenta correcciones automáticas
     */
    protected function attemptAutomaticFixes(League $league, array $leagueResult): array
    {
        $this->info("🔧 Intentando correcciones automáticas para {$league->name}...");

        return $this->migrationValidationService->autoFixInconsistencies($league);
    }

    /**
     * Determina si hay problemas que se pueden corregir automáticamente
     */
    protected function hasFixableIssues(array $leagueResult): bool
    {
        $fixableTypes = [
            'missing_category_assignments',
            'incorrect_assignment',
            'incomplete_migration'
        ];

        foreach ($leagueResult['all_issues'] as $issue) {
            if (in_array($issue['type'], $fixableTypes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Muestra los resultados de validación
     */
    protected function displayValidationResults(array $results): void
    {
        $this->info('📊 RESULTADOS DE VALIDACIÓN POST-MIGRACIÓN');
        $this->line('═══════════════════════════════════════════════');

        // Resumen general
        $this->line("🏆 Ligas validadas: {$results['total_leagues']}");
        $this->line("✅ Sin problemas: {$results['leagues_passed']}");
        $this->line("⚠️  Con advertencias: {$results['leagues_with_warnings']}");
        $this->line("❌ Con problemas críticos: {$results['leagues_with_critical_issues']}");
        $this->line("🔍 Total de problemas: {$results['total_issues_found']}");

        if ($this->option('fix')) {
            $this->line("🔧 Problemas corregidos: {$results['total_issues_fixed']}");
        }

        $this->newLine();

        // Detalles por liga (filtrado por threshold)
        $threshold = $this->option('threshold');
        $this->info('📋 DETALLES POR LIGA:');

        foreach ($results['league_results'] as $leagueResult) {
            if ($this->shouldDisplayLeague($leagueResult, $threshold)) {
                $this->displayLeagueResult($leagueResult);
            }
        }

        // Estadísticas de resumen
        if (!empty($results['summary_statistics'])) {
            $this->newLine();
            $this->info('📈 ESTADÍSTICAS DE RESUMEN:');
            $this->displaySummaryStatistics($results['summary_statistics']);
        }

        $this->newLine();
        $this->displayOverallConclusion($results);
    }

    /**
     * Determina si se debe mostrar una liga según el threshold
     */
    protected function shouldDisplayLeague(array $leagueResult, string $threshold): bool
    {
        return match ($threshold) {
            'info' => true,
            'warning' => in_array($leagueResult['overall_status'], ['warning', 'critical']),
            'critical' => $leagueResult['overall_status'] === 'critical',
            default => true
        };
    }

    /**
     * Muestra el resultado de una liga específica
     */
    protected function displayLeagueResult(array $result): void
    {
        $statusIcon = match ($result['overall_status']) {
            'valid' => '✅',
            'warning' => '⚠️',
            'critical' => '❌',
            default => '❓'
        };

        $this->line("{$statusIcon} {$result['league_name']}");
        $this->line("   📊 Problemas críticos: {$result['critical_issues_count']} | Advertencias: {$result['warning_issues_count']}");

        // Mostrar métricas clave de validaciones adicionales
        foreach ($result['additional_validations'] as $validation) {
            if (isset($validation['metrics']) && !empty($validation['metrics'])) {
                $this->displayValidationMetrics($validation);
            }
        }

        // Mostrar problemas más importantes
        $criticalIssues = array_filter($result['all_issues'], fn($issue) => $issue['severity'] === 'critical');
        if (!empty($criticalIssues)) {
            $this->line("   🚨 PROBLEMAS CRÍTICOS:");
            foreach (array_slice($criticalIssues, 0, 3) as $issue) {
                $this->line("      • {$issue['message']}");
            }
            if (count($criticalIssues) > 3) {
                $remaining = count($criticalIssues) - 3;
                $this->line("      ... y {$remaining} más");
            }
        }

        if (isset($result['fix_results'])) {
            $fixes = $result['fix_results'];
            $this->line("   🔧 Correcciones: {$fixes['fixes_successful']} exitosas, {$fixes['fixes_failed']} fallidas");
        }

        $this->newLine();
    }

    /**
     * Muestra métricas de una validación específica
     */
    protected function displayValidationMetrics(array $validation): void
    {
        $name = $validation['name'];
        $metrics = $validation['metrics'];

        $keyMetrics = [];

        // Seleccionar métricas más relevantes para mostrar
        if (isset($metrics['migration_completeness_percentage'])) {
            $keyMetrics[] = "Completitud: {$metrics['migration_completeness_percentage']}%";
        }

        if (isset($metrics['accuracy_percentage'])) {
            $keyMetrics[] = "Precisión: {$metrics['accuracy_percentage']}%";
        }

        if (isset($metrics['enum_compatibility_percentage'])) {
            $keyMetrics[] = "Compatibilidad: {$metrics['enum_compatibility_percentage']}%";
        }

        if (!empty($keyMetrics)) {
            $this->line("   📈 {$name}: " . implode(' | ', $keyMetrics));
        }
    }

    /**
     * Muestra estadísticas de resumen
     */
    protected function displaySummaryStatistics(array $stats): void
    {
        foreach ($stats as $key => $value) {
            if (is_numeric($value)) {
                $this->line("   • " . ucfirst(str_replace('_', ' ', $key)) . ": {$value}");
            }
        }
    }

    /**
     * Muestra conclusión general
     */
    protected function displayOverallConclusion(array $results): void
    {
        if ($results['leagues_with_critical_issues'] === 0) {
            if ($results['leagues_with_warnings'] === 0) {
                $this->info('🎉 ¡Todas las ligas pasaron la validación sin problemas!');
            } else {
                $this->warn('⚠️  Validación completada con algunas advertencias.');
                $this->line('   Las advertencias no impiden el funcionamiento pero se recomienda revisarlas.');
            }
        } else {
            $this->error('❌ Se encontraron problemas críticos que requieren atención inmediata.');
            $this->line('   El sistema puede no funcionar correctamente hasta que se corrijan.');
        }

        $this->newLine();
        $this->info('💡 Recomendaciones:');
        $this->line('   • Ejecutar validaciones periódicas: php artisan categories:validate-migration');
        $this->line('   • Usar --fix para correcciones automáticas cuando sea posible');
        $this->line('   • Generar reportes detallados con --report para análisis profundo');
        $this->line('   • Monitorear performance después de cambios en configuración');
    }

    /**
     * Genera reporte detallado de validación
     */
    protected function generateValidationReport(array $results): void
    {
        $this->info('📄 Generando reporte detallado de validación...');

        $reportData = [
            'report_type' => 'post_migration_validation',
            'generated_at' => now()->toISOString(),
            'command_options' => [
                'league' => $this->option('league'),
                'fix' => $this->option('fix'),
                'threshold' => $this->option('threshold')
            ],
            'validation_results' => $results,
            'recommendations' => $this->generateDetailedRecommendations($results)
        ];

        $reportPath = storage_path('logs/post-migration-validation-' . now()->format('Y-m-d-H-i-s') . '.json');
        file_put_contents($reportPath, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("📄 Reporte detallado guardado en: {$reportPath}");
    }

    /**
     * Exporta resultados a archivo JSON
     */
    protected function exportValidationResults(array $results): void
    {
        $this->info('💾 Exportando resultados de validación...');

        $exportData = [
            'export_date' => now()->toISOString(),
            'validation_summary' => [
                'total_leagues' => $results['total_leagues'],
                'leagues_passed' => $results['leagues_passed'],
                'leagues_with_warnings' => $results['leagues_with_warnings'],
                'leagues_with_critical_issues' => $results['leagues_with_critical_issues'],
                'total_issues_found' => $results['total_issues_found'],
                'total_issues_fixed' => $results['total_issues_fixed']
            ],
            'league_results' => $results['league_results']
        ];

        $exportPath = storage_path('exports/migration-validation-' . now()->format('Y-m-d-H-i-s') . '.json');

        // Crear directorio si no existe
        $exportDir = dirname($exportPath);
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        file_put_contents($exportPath, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("💾 Resultados exportados a: {$exportPath}");
    }

    /**
     * Determina el código de salida basado en los resultados
     */
    protected function determineExitCode(array $results): int
    {
        if ($results['leagues_with_critical_issues'] > 0) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Genera estadísticas de resumen
     */
    protected function generateSummaryStatistics(array $results): array
    {
        $stats = [
            'total_players_validated' => 0,
            'total_categories_configured' => 0,
            'average_accuracy_percentage' => 0,
            'average_completeness_percentage' => 0
        ];

        $accuracySum = 0;
        $completenessSum = 0;
        $leaguesWithMetrics = 0;

        foreach ($results['league_results'] as $leagueResult) {
            foreach ($leagueResult['additional_validations'] as $validation) {
                if (isset($validation['metrics'])) {
                    $metrics = $validation['metrics'];

                    if (isset($metrics['total_assignments'])) {
                        $stats['total_players_validated'] += $metrics['total_assignments'];
                    }

                    if (isset($metrics['total_categories'])) {
                        $stats['total_categories_configured'] += $metrics['total_categories'];
                    }

                    if (isset($metrics['accuracy_percentage'])) {
                        $accuracySum += $metrics['accuracy_percentage'];
                        $leaguesWithMetrics++;
                    }

                    if (isset($metrics['migration_completeness_percentage'])) {
                        $completenessSum += $metrics['migration_completeness_percentage'];
                    }
                }
            }
        }

        if ($leaguesWithMetrics > 0) {
            $stats['average_accuracy_percentage'] = round($accuracySum / $leaguesWithMetrics, 2);
            $stats['average_completeness_percentage'] = round($completenessSum / $leaguesWithMetrics, 2);
        }

        return $stats;
    }

    /**
     * Genera recomendaciones detalladas
     */
    protected function generateDetailedRecommendations(array $results): array
    {
        $recommendations = [];

        // Recomendaciones basadas en problemas encontrados
        if ($results['leagues_with_critical_issues'] > 0) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'critical_issues',
                'message' => 'Corregir problemas críticos antes de usar en producción',
                'actions' => [
                    'Revisar configuraciones de categorías con errores',
                    'Ejecutar correcciones automáticas con --fix',
                    'Validar integridad de datos manualmente'
                ]
            ];
        }

        if ($results['total_issues_fixed'] > 0) {
            $recommendations[] = [
                'priority' => 'medium',
                'category' => 'post_fix_validation',
                'message' => 'Validar correcciones automáticas aplicadas',
                'actions' => [
                    'Ejecutar nueva validación sin --fix',
                    'Revisar logs de correcciones aplicadas',
                    'Verificar que las correcciones son apropiadas'
                ]
            ];
        }

        // Recomendaciones de mantenimiento
        $recommendations[] = [
            'priority' => 'low',
            'category' => 'maintenance',
            'message' => 'Establecer rutinas de validación periódica',
            'actions' => [
                'Programar validaciones automáticas semanales',
                'Monitorear métricas de performance',
                'Revisar configuraciones después de cambios normativos'
            ]
        ];

        return $recommendations;
    }

    /**
     * Obtiene categorías esperadas para una liga
     */
    protected function getExpectedCategoriesForLeague(League $league): array
    {
        // Por defecto, esperamos las categorías estándar
        return ['Mini', 'Pre_Mini', 'Infantil', 'Cadete', 'Juvenil', 'Mayores', 'Masters'];
    }

    /**
     * Encuentra superposiciones entre categorías
     */
    protected function findCategoryOverlaps($categories): array
    {
        $overlaps = [];
        $categoriesArray = $categories->toArray();

        for ($i = 0; $i < count($categoriesArray); $i++) {
            for ($j = $i + 1; $j < count($categoriesArray); $j++) {
                $cat1 = $categoriesArray[$i];
                $cat2 = $categoriesArray[$j];

                $ageOverlap = !($cat1['max_age'] < $cat2['min_age'] || $cat1['min_age'] > $cat2['max_age']);
                $genderOverlap = ($cat1['gender'] === 'mixed' || $cat2['gender'] === 'mixed' || $cat1['gender'] === $cat2['gender']);

                if ($ageOverlap && $genderOverlap) {
                    $overlapYears = min($cat1['max_age'], $cat2['max_age']) - max($cat1['min_age'], $cat2['min_age']) + 1;

                    $overlaps[] = [
                        'category1' => $cat1['name'],
                        'category2' => $cat2['name'],
                        'overlap_years' => $overlapYears,
                        'severity' => $overlapYears > 2 ? 'critical' : 'warning',
                        'message' => "Superposición de {$overlapYears} año(s) entre '{$cat1['name']}' y '{$cat2['name']}'"
                    ];
                }
            }
        }

        return $overlaps;
    }

    /**
     * Determina si una diferencia de categoría es significativa
     */
    protected function isSignificantCategoryMismatch(string $current, string $suggested, int $age): bool
    {
        // Definir categorías adyacentes que no son problemáticas
        $adjacentCategories = [
            'Mini' => ['Pre_Mini'],
            'Pre_Mini' => ['Mini', 'Infantil'],
            'Infantil' => ['Pre_Mini', 'Cadete'],
            'Cadete' => ['Infantil', 'Juvenil'],
            'Juvenil' => ['Cadete', 'Mayores'],
            'Mayores' => ['Juvenil', 'Masters'],
            'Masters' => ['Mayores']
        ];

        // Si las categorías son adyacentes y la edad está en el límite, no es significativo
        if (isset($adjacentCategories[$current]) && in_array($suggested, $adjacentCategories[$current])) {
            return false;
        }

        return true;
    }
}
