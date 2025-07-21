<?php

namespace App\Services;

use App\Models\League;
use App\Models\Player;
use App\Models\LeagueCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class MigrationValidationService
{
    /**
     * Verifica la integridad completa después de una migración
     */
    public function verifyMigrationIntegrity(League $league): array
    {
        Log::info('Iniciando verificación de integridad post-migración', [
            'league_id' => $league->id,
            'league_name' => $league->name
        ]);

        $report = [
            'league_id' => $league->id,
            'league_name' => $league->name,
            'verification_date' => now()->toISOString(),
            'overall_status' => 'valid',
            'checks_performed' => [],
            'issues_found' => [],
            'statistics' => [],
            'recommendations' => []
        ];

        try {
            // 1. Verificar configuración de categorías
            $categoryCheck = $this->verifyCategoryConfiguration($league);
            $report['checks_performed']['category_configuration'] = $categoryCheck;

            // 2. Verificar asignaciones de jugadoras
            $playerCheck = $this->verifyPlayerAssignments($league);
            $report['checks_performed']['player_assignments'] = $playerCheck;

            // 3. Verificar consistencia de datos
            $consistencyCheck = $this->verifyDataConsistency($league);
            $report['checks_performed']['data_consistency'] = $consistencyCheck;

            // 4. Verificar integridad referencial
            $referentialCheck = $this->verifyReferentialIntegrity($league);
            $report['checks_performed']['referential_integrity'] = $referentialCheck;

            // 5. Generar estadísticas
            $report['statistics'] = $this->generateMigrationStatistics($league);

            // Consolidar problemas encontrados
            $allIssues = array_merge(
                $categoryCheck['issues'] ?? [],
                $playerCheck['issues'] ?? [],
                $consistencyCheck['issues'] ?? [],
                $referentialCheck['issues'] ?? []
            );

            $report['issues_found'] = $allIssues;

            // Determinar estado general
            $criticalIssues = array_filter($allIssues, fn($issue) => $issue['severity'] === 'critical');
            if (!empty($criticalIssues)) {
                $report['overall_status'] = 'critical';
            } elseif (!empty($allIssues)) {
                $report['overall_status'] = 'warning';
            }

            // Generar recomendaciones
            $report['recommendations'] = $this->generateRecommendations($report);

            Log::info('Verificación de integridad completada', [
                'league_id' => $league->id,
                'status' => $report['overall_status'],
                'issues_count' => count($allIssues)
            ]);
        } catch (\Exception $e) {
            $report['overall_status'] = 'error';
            $report['verification_error'] = $e->getMessage();

            Log::error('Error durante verificación de integridad', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);
        }

        return $report;
    }

    /**
     * Verifica la configuración de categorías
     */
    protected function verifyCategoryConfiguration(League $league): array
    {
        $check = [
            'name' => 'Configuración de Categorías',
            'status' => 'passed',
            'issues' => [],
            'details' => []
        ];

        try {
            // Verificar que existen categorías
            $categoriesCount = $league->categories()->count();
            $check['details']['categories_count'] = $categoriesCount;

            if ($categoriesCount === 0) {
                $check['status'] = 'failed';
                $check['issues'][] = [
                    'type' => 'missing_categories',
                    'severity' => 'critical',
                    'message' => 'La liga no tiene categorías configuradas',
                    'recommendation' => 'Ejecutar php artisan categories:setup --league=' . $league->id
                ];
                return $check;
            }

            // Verificar categorías activas
            $activeCategories = $league->getActiveCategories();
            $check['details']['active_categories'] = $activeCategories->count();

            if ($activeCategories->isEmpty()) {
                $check['status'] = 'failed';
                $check['issues'][] = [
                    'type' => 'no_active_categories',
                    'severity' => 'critical',
                    'message' => 'No hay categorías activas configuradas',
                    'recommendation' => 'Activar al menos una categoría en el panel de administración'
                ];
                return $check;
            }

            // Verificar cobertura de rangos de edad
            $ageGaps = $this->findAgeGaps($activeCategories);
            if (!empty($ageGaps)) {
                $check['status'] = 'warning';
                foreach ($ageGaps as $gap) {
                    $check['issues'][] = [
                        'type' => 'age_gap',
                        'severity' => 'warning',
                        'message' => "Gap de edad no cubierto: {$gap['start']}-{$gap['end']} años",
                        'recommendation' => 'Considerar ajustar rangos de categorías'
                    ];
                }
            }

            // Verificar superposiciones problemáticas
            $overlaps = $this->findProblematicOverlaps($activeCategories);
            if (!empty($overlaps)) {
                $check['status'] = 'warning';
                foreach ($overlaps as $overlap) {
                    $check['issues'][] = [
                        'type' => 'category_overlap',
                        'severity' => 'warning',
                        'message' => "Superposición entre '{$overlap['category1']}' y '{$overlap['category2']}'",
                        'recommendation' => 'Revisar rangos de edad para evitar ambigüedades'
                    ];
                }
            }

            $check['details']['age_coverage'] = [
                'min_age' => $activeCategories->min('min_age'),
                'max_age' => $activeCategories->max('max_age'),
                'gaps_found' => count($ageGaps),
                'overlaps_found' => count($overlaps)
            ];
        } catch (\Exception $e) {
            $check['status'] = 'error';
            $check['issues'][] = [
                'type' => 'verification_error',
                'severity' => 'critical',
                'message' => 'Error verificando configuración: ' . $e->getMessage(),
                'recommendation' => 'Revisar logs del sistema'
            ];
        }

        return $check;
    }

    /**
     * Verifica las asignaciones de jugadoras
     */
    protected function verifyPlayerAssignments(League $league): array
    {
        $check = [
            'name' => 'Asignaciones de Jugadoras',
            'status' => 'passed',
            'issues' => [],
            'details' => []
        ];

        try {
            $players = $league->players()->with('user')->get();
            $check['details']['total_players'] = $players->count();

            $playersWithoutCategory = 0;
            $playersWithInvalidCategory = 0;
            $playersWithIncorrectCategory = 0;

            foreach ($players as $player) {
                $currentCategory = $player->category?->value ?? $player->category;
                $age = $player->user->age ?? 0;
                $gender = $player->user->gender ?? 'unknown';

                // Verificar que tiene categoría
                if (empty($currentCategory)) {
                    $playersWithoutCategory++;
                    continue;
                }

                // Verificar que la categoría existe en la configuración
                if ($league->hasCustomCategories()) {
                    $categoryExists = $league->categories()
                        ->where('name', $currentCategory)
                        ->exists();

                    if (!$categoryExists) {
                        $playersWithInvalidCategory++;
                        continue;
                    }

                    // Verificar que la categoría es apropiada para la jugadora
                    $appropriateCategory = $league->findCategoryForPlayer($age, $gender);
                    if ($appropriateCategory && $appropriateCategory->name !== $currentCategory) {
                        $playersWithIncorrectCategory++;
                    }
                }
            }

            $check['details']['players_without_category'] = $playersWithoutCategory;
            $check['details']['players_with_invalid_category'] = $playersWithInvalidCategory;
            $check['details']['players_with_incorrect_category'] = $playersWithIncorrectCategory;

            // Generar issues basados en los problemas encontrados
            if ($playersWithoutCategory > 0) {
                $check['status'] = 'failed';
                $check['issues'][] = [
                    'type' => 'missing_category_assignments',
                    'severity' => 'critical',
                    'message' => "{$playersWithoutCategory} jugadora(s) sin categoría asignada",
                    'recommendation' => 'Ejecutar php artisan categories:validate --fix'
                ];
            }

            if ($playersWithInvalidCategory > 0) {
                $check['status'] = 'failed';
                $check['issues'][] = [
                    'type' => 'invalid_category_assignments',
                    'severity' => 'critical',
                    'message' => "{$playersWithInvalidCategory} jugadora(s) con categoría inválida",
                    'recommendation' => 'Reasignar categorías usando el sistema dinámico'
                ];
            }

            if ($playersWithIncorrectCategory > 0) {
                $check['status'] = 'warning';
                $check['issues'][] = [
                    'type' => 'incorrect_category_assignments',
                    'severity' => 'warning',
                    'message' => "{$playersWithIncorrectCategory} jugadora(s) con categoría posiblemente incorrecta",
                    'recommendation' => 'Revisar asignaciones y considerar reasignación automática'
                ];
            }
        } catch (\Exception $e) {
            $check['status'] = 'error';
            $check['issues'][] = [
                'type' => 'verification_error',
                'severity' => 'critical',
                'message' => 'Error verificando asignaciones: ' . $e->getMessage(),
                'recommendation' => 'Revisar logs del sistema'
            ];
        }

        return $check;
    }

    /**
     * Verifica la consistencia de datos
     */
    protected function verifyDataConsistency(League $league): array
    {
        $check = [
            'name' => 'Consistencia de Datos',
            'status' => 'passed',
            'issues' => [],
            'details' => []
        ];

        try {
            // Verificar consistencia entre jugadoras y categorías
            $categoryDistribution = $this->getCategoryDistribution($league);
            $check['details']['category_distribution'] = $categoryDistribution;

            // Verificar categorías huérfanas (sin jugadoras)
            $emptyCategories = [];
            if ($league->hasCustomCategories()) {
                foreach ($league->getActiveCategories() as $category) {
                    $playerCount = $categoryDistribution[$category->name] ?? 0;
                    if ($playerCount === 0) {
                        $emptyCategories[] = $category->name;
                    }
                }
            }

            if (!empty($emptyCategories)) {
                $check['status'] = 'warning';
                $check['issues'][] = [
                    'type' => 'empty_categories',
                    'severity' => 'warning',
                    'message' => 'Categorías sin jugadoras: ' . implode(', ', $emptyCategories),
                    'recommendation' => 'Considerar desactivar categorías no utilizadas'
                ];
            }

            // Verificar distribución equilibrada
            $totalPlayers = array_sum($categoryDistribution);
            if ($totalPlayers > 0) {
                $imbalancedCategories = [];
                foreach ($categoryDistribution as $category => $count) {
                    $percentage = ($count / $totalPlayers) * 100;
                    if ($percentage > 60) { // Más del 60% en una categoría
                        $imbalancedCategories[] = [
                            'category' => $category,
                            'percentage' => round($percentage, 1),
                            'count' => $count
                        ];
                    }
                }

                if (!empty($imbalancedCategories)) {
                    $check['status'] = 'warning';
                    foreach ($imbalancedCategories as $imbalance) {
                        $check['issues'][] = [
                            'type' => 'category_imbalance',
                            'severity' => 'warning',
                            'message' => "Categoría '{$imbalance['category']}' tiene {$imbalance['percentage']}% de las jugadoras",
                            'recommendation' => 'Revisar rangos de edad para mejor distribución'
                        ];
                    }
                }
            }

            $check['details']['empty_categories'] = $emptyCategories;
            $check['details']['total_players'] = $totalPlayers;
        } catch (\Exception $e) {
            $check['status'] = 'error';
            $check['issues'][] = [
                'type' => 'verification_error',
                'severity' => 'critical',
                'message' => 'Error verificando consistencia: ' . $e->getMessage(),
                'recommendation' => 'Revisar logs del sistema'
            ];
        }

        return $check;
    }

    /**
     * Verifica la integridad referencial
     */
    protected function verifyReferentialIntegrity(League $league): array
    {
        $check = [
            'name' => 'Integridad Referencial',
            'status' => 'passed',
            'issues' => [],
            'details' => []
        ];

        try {
            // Verificar que todas las categorías pertenecen a la liga correcta
            $categoriesWithWrongLeague = LeagueCategory::where('league_id', '!=', $league->id)
                ->whereIn('id', $league->categories()->pluck('id'))
                ->count();

            if ($categoriesWithWrongLeague > 0) {
                $check['status'] = 'failed';
                $check['issues'][] = [
                    'type' => 'wrong_league_reference',
                    'severity' => 'critical',
                    'message' => "{$categoriesWithWrongLeague} categoría(s) con referencia incorrecta a liga",
                    'recommendation' => 'Corregir referencias en base de datos'
                ];
            }

            // Verificar que todas las jugadoras pertenecen a clubes de la liga
            $playersInWrongLeague = $league->players()
                ->whereHas('currentClub', function ($query) use ($league) {
                    $query->where('league_id', '!=', $league->id);
                })
                ->count();

            if ($playersInWrongLeague > 0) {
                $check['status'] = 'failed';
                $check['issues'][] = [
                    'type' => 'wrong_club_league',
                    'severity' => 'critical',
                    'message' => "{$playersInWrongLeague} jugadora(s) en clubes de otra liga",
                    'recommendation' => 'Corregir asignaciones de club'
                ];
            }

            // Verificar integridad de claves foráneas
            $orphanedPlayers = Player::whereNotNull('current_club_id')
                ->whereDoesntHave('currentClub')
                ->whereHas('currentClub', function ($query) use ($league) {
                    $query->where('league_id', $league->id);
                })
                ->count();

            if ($orphanedPlayers > 0) {
                $check['status'] = 'failed';
                $check['issues'][] = [
                    'type' => 'orphaned_players',
                    'severity' => 'critical',
                    'message' => "{$orphanedPlayers} jugadora(s) con referencia a club inexistente",
                    'recommendation' => 'Limpiar referencias huérfanas'
                ];
            }

            $check['details'] = [
                'categories_with_wrong_league' => $categoriesWithWrongLeague,
                'players_in_wrong_league' => $playersInWrongLeague,
                'orphaned_players' => $orphanedPlayers
            ];
        } catch (\Exception $e) {
            $check['status'] = 'error';
            $check['issues'][] = [
                'type' => 'verification_error',
                'severity' => 'critical',
                'message' => 'Error verificando integridad referencial: ' . $e->getMessage(),
                'recommendation' => 'Revisar logs del sistema'
            ];
        }

        return $check;
    }

    /**
     * Genera estadísticas de migración
     */
    protected function generateMigrationStatistics(League $league): array
    {
        $stats = [
            'league_info' => [
                'id' => $league->id,
                'name' => $league->name,
                'has_dynamic_categories' => $league->hasCustomCategories()
            ],
            'categories' => [],
            'players' => [],
            'distribution' => []
        ];

        try {
            // Estadísticas de categorías
            if ($league->hasCustomCategories()) {
                $categories = $league->getActiveCategories();
                $stats['categories'] = [
                    'total' => $categories->count(),
                    'active' => $categories->count(),
                    'age_range' => [
                        'min' => $categories->min('min_age'),
                        'max' => $categories->max('max_age')
                    ],
                    'gender_distribution' => $categories->groupBy('gender')->map->count()->toArray()
                ];
            }

            // Estadísticas de jugadoras
            $players = $league->players()->with('user')->get();
            $stats['players'] = [
                'total' => $players->count(),
                'with_category' => $players->whereNotNull('category')->count(),
                'without_category' => $players->whereNull('category')->count(),
                'age_distribution' => $this->getAgeDistribution($players),
                'gender_distribution' => $this->getGenderDistribution($players)
            ];

            // Distribución por categorías
            $stats['distribution'] = $this->getCategoryDistribution($league);
        } catch (\Exception $e) {
            Log::warning('Error generando estadísticas de migración', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);
        }

        return $stats;
    }

    /**
     * Encuentra gaps en rangos de edad
     */
    protected function findAgeGaps(Collection $categories): array
    {
        $gaps = [];
        $sortedCategories = $categories->sortBy('min_age');
        $previousMaxAge = null;

        foreach ($sortedCategories as $category) {
            if ($previousMaxAge !== null && $category->min_age > $previousMaxAge + 1) {
                $gaps[] = [
                    'start' => $previousMaxAge + 1,
                    'end' => $category->min_age - 1
                ];
            }
            $previousMaxAge = max($previousMaxAge ?? 0, $category->max_age);
        }

        return $gaps;
    }

    /**
     * Encuentra superposiciones problemáticas
     */
    protected function findProblematicOverlaps(Collection $categories): array
    {
        $overlaps = [];
        $categoriesArray = $categories->toArray();

        for ($i = 0; $i < count($categoriesArray); $i++) {
            for ($j = $i + 1; $j < count($categoriesArray); $j++) {
                $cat1 = $categoriesArray[$i];
                $cat2 = $categoriesArray[$j];

                // Verificar superposición de edad y género
                $ageOverlap = !($cat1['max_age'] < $cat2['min_age'] || $cat1['min_age'] > $cat2['max_age']);
                $genderOverlap = ($cat1['gender'] === 'mixed' || $cat2['gender'] === 'mixed' || $cat1['gender'] === $cat2['gender']);

                if ($ageOverlap && $genderOverlap) {
                    $overlapYears = min($cat1['max_age'], $cat2['max_age']) - max($cat1['min_age'], $cat2['min_age']) + 1;

                    // Solo reportar superposiciones significativas (más de 1 año)
                    if ($overlapYears > 1) {
                        $overlaps[] = [
                            'category1' => $cat1['name'],
                            'category2' => $cat2['name'],
                            'overlap_years' => $overlapYears
                        ];
                    }
                }
            }
        }

        return $overlaps;
    }

    /**
     * Obtiene distribución de jugadoras por categoría
     */
    protected function getCategoryDistribution(League $league): array
    {
        return $league->players()
            ->join('users', 'players.user_id', '=', 'users.id')
            ->where('users.status', 'active')
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Obtiene distribución por edad
     */
    protected function getAgeDistribution(Collection $players): array
    {
        $distribution = [];

        foreach ($players as $player) {
            $age = $player->user->age ?? 0;
            $ageGroup = $this->getAgeGroup($age);
            $distribution[$ageGroup] = ($distribution[$ageGroup] ?? 0) + 1;
        }

        return $distribution;
    }

    /**
     * Obtiene distribución por género
     */
    protected function getGenderDistribution(Collection $players): array
    {
        $distribution = [];

        foreach ($players as $player) {
            $gender = $player->user->gender ?? 'unknown';
            $distribution[$gender] = ($distribution[$gender] ?? 0) + 1;
        }

        return $distribution;
    }

    /**
     * Obtiene grupo de edad para estadísticas
     */
    protected function getAgeGroup(int $age): string
    {
        return match (true) {
            $age < 8 => 'Menor a 8',
            $age <= 12 => '8-12',
            $age <= 16 => '13-16',
            $age <= 20 => '17-20',
            $age <= 30 => '21-30',
            $age <= 40 => '31-40',
            $age > 40 => 'Mayor a 40',
            default => 'Sin edad'
        };
    }

    /**
     * Genera recomendaciones basadas en el reporte
     */
    protected function generateRecommendations(array $report): array
    {
        $recommendations = [];

        // Recomendaciones basadas en el estado general
        switch ($report['overall_status']) {
            case 'critical':
                $recommendations[] = 'URGENTE: Corregir problemas críticos antes de usar el sistema en producción';
                $recommendations[] = 'Ejecutar php artisan categories:validate --fix para intentar correcciones automáticas';
                break;

            case 'warning':
                $recommendations[] = 'Revisar advertencias para optimizar la configuración';
                $recommendations[] = 'Considerar ajustes en rangos de categorías';
                break;

            case 'valid':
                $recommendations[] = 'Sistema validado correctamente';
                $recommendations[] = 'Realizar validaciones periódicas para mantener integridad';
                break;
        }

        // Recomendaciones específicas basadas en estadísticas
        $stats = $report['statistics'];

        if (isset($stats['players']['without_category']) && $stats['players']['without_category'] > 0) {
            $recommendations[] = 'Asignar categorías a jugadoras sin clasificar';
        }

        if (isset($stats['categories']['total']) && $stats['categories']['total'] < 3) {
            $recommendations[] = 'Considerar agregar más categorías para mejor segmentación';
        }

        // Recomendaciones de mantenimiento
        $recommendations[] = 'Programar validaciones automáticas periódicas';
        $recommendations[] = 'Monitorear distribución de jugadoras por categoría';
        $recommendations[] = 'Revisar configuración cuando cambien normativas de la liga';

        return array_unique($recommendations);
    }

    /**
     * Detecta y corrige inconsistencias automáticamente
     */
    public function autoFixInconsistencies(League $league): array
    {
        $results = [
            'league_id' => $league->id,
            'fixes_attempted' => 0,
            'fixes_successful' => 0,
            'fixes_failed' => 0,
            'details' => []
        ];

        try {
            DB::transaction(function () use ($league, &$results) {
                // 1. Corregir jugadoras sin categoría
                $playersWithoutCategory = $league->players()
                    ->whereNull('category')
                    ->orWhere('category', '')
                    ->with('user')
                    ->get();

                foreach ($playersWithoutCategory as $player) {
                    $results['fixes_attempted']++;

                    try {
                        $appropriateCategory = $league->findCategoryForPlayer(
                            $player->user->age ?? 19,
                            $player->user->gender ?? 'female'
                        );

                        if ($appropriateCategory) {
                            $player->update(['category' => $appropriateCategory->name]);
                            $results['fixes_successful']++;
                            $results['details'][] = [
                                'type' => 'category_assigned',
                                'player_id' => $player->id,
                                'category' => $appropriateCategory->name
                            ];
                        } else {
                            $results['fixes_failed']++;
                            $results['details'][] = [
                                'type' => 'category_assignment_failed',
                                'player_id' => $player->id,
                                'reason' => 'No se encontró categoría apropiada'
                            ];
                        }
                    } catch (\Exception $e) {
                        $results['fixes_failed']++;
                        $results['details'][] = [
                            'type' => 'fix_error',
                            'player_id' => $player->id,
                            'error' => $e->getMessage()
                        ];
                    }
                }

                Log::info('Corrección automática de inconsistencias completada', [
                    'league_id' => $league->id,
                    'attempted' => $results['fixes_attempted'],
                    'successful' => $results['fixes_successful'],
                    'failed' => $results['fixes_failed']
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error durante corrección automática', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);

            $results['error'] = $e->getMessage();
        }

        return $results;
    }
}
