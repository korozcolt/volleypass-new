<?php

namespace App\Services;

use App\Models\League;
use App\Models\LeagueCategory;
use App\Enums\PlayerCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class LeagueConfigurationService
{
    /**
     * Crea categorías por defecto para una liga basadas en el enum PlayerCategory
     */
    public function createDefaultCategories(League $league): array
    {
        try {
            Log::info('Creando categorías por defecto', ['league_id' => $league->id]);

            // Verificar si ya tiene categorías configuradas
            if ($league->hasCustomCategories()) {
                Log::warning('La liga ya tiene categorías configuradas', [
                    'league_id' => $league->id,
                    'existing_categories' => $league->categories()->count()
                ]);
                return [
                    'success' => false,
                    'message' => 'La liga ya tiene categorías configuradas',
                    'existing_count' => $league->categories()->count()
                ];
            }

            $defaultCategories = $this->getDefaultCategoryDefinitions();
            $createdCategories = [];

            DB::transaction(function () use ($league, $defaultCategories, &$createdCategories) {
                foreach ($defaultCategories as $categoryData) {
                    $categoryData['league_id'] = $league->id;
                    $category = LeagueCategory::create($categoryData);
                    $createdCategories[] = $category;

                    Log::debug('Categoría por defecto creada', [
                        'league_id' => $league->id,
                        'category_name' => $category->name,
                        'age_range' => "{$category->min_age}-{$category->max_age}"
                    ]);
                }
            });

            // Limpiar caché
            $this->clearLeagueCache($league->id);

            Log::info('Categorías por defecto creadas exitosamente', [
                'league_id' => $league->id,
                'categories_created' => count($createdCategories)
            ]);

            return [
                'success' => true,
                'message' => 'Categorías por defecto creadas exitosamente',
                'categories_created' => count($createdCategories),
                'categories' => collect($createdCategories)->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'age_range' => $category->age_range_label,
                        'gender' => $category->gender_label
                    ];
                })->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Error creando categorías por defecto', [
                'league_id' => $league->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error creando categorías por defecto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las definiciones de categorías por defecto
     */
    protected function getDefaultCategoryDefinitions(): array
    {
        return [
            [
                'name' => 'Mini',
                'code' => 'MINI',
                'description' => 'Categoría Mini - Jugadoras más jóvenes',
                'gender' => 'mixed',
                'min_age' => 8,
                'max_age' => 10,
                'sort_order' => 1,
                'color' => '#ec4899',
                'icon' => 'heroicon-o-heart',
                'is_active' => true,
            ],
            [
                'name' => 'Pre-Mini',
                'code' => 'PRE_MINI',
                'description' => 'Categoría Pre-Mini - Transición hacia infantil',
                'gender' => 'mixed',
                'min_age' => 11,
                'max_age' => 12,
                'sort_order' => 2,
                'color' => '#8b5cf6',
                'icon' => 'heroicon-o-star',
                'is_active' => true,
            ],
            [
                'name' => 'Infantil',
                'code' => 'INFANTIL',
                'description' => 'Categoría Infantil - Desarrollo técnico',
                'gender' => 'mixed',
                'min_age' => 13,
                'max_age' => 14,
                'sort_order' => 3,
                'color' => '#3b82f6',
                'icon' => 'heroicon-o-sparkles',
                'is_active' => true,
            ],
            [
                'name' => 'Cadete',
                'code' => 'CADETE',
                'description' => 'Categoría Cadete - Competencia juvenil',
                'gender' => 'mixed',
                'min_age' => 15,
                'max_age' => 16,
                'sort_order' => 4,
                'color' => '#f59e0b',
                'icon' => 'heroicon-o-fire',
                'is_active' => true,
            ],
            [
                'name' => 'Juvenil',
                'code' => 'JUVENIL',
                'description' => 'Categoría Juvenil - Preparación para mayores',
                'gender' => 'mixed',
                'min_age' => 17,
                'max_age' => 18,
                'sort_order' => 5,
                'color' => '#10b981',
                'icon' => 'heroicon-o-bolt',
                'is_active' => true,
            ],
            [
                'name' => 'Mayores',
                'code' => 'MAYORES',
                'description' => 'Categoría Mayores - Competencia principal',
                'gender' => 'mixed',
                'min_age' => 19,
                'max_age' => 34,
                'sort_order' => 6,
                'color' => '#6366f1',
                'icon' => 'heroicon-o-trophy',
                'is_active' => true,
            ],
            [
                'name' => 'Masters',
                'code' => 'MASTERS',
                'description' => 'Categoría Masters - Veteranas',
                'gender' => 'mixed',
                'min_age' => 35,
                'max_age' => 100,
                'sort_order' => 7,
                'color' => '#6b7280',
                'icon' => 'heroicon-o-academic-cap',
                'is_active' => true,
            ],
        ];
    }

    /**
     * Valida la configuración completa de categorías de una liga
     */
    public function validateCategoryConfiguration(League $league): array
    {
        try {
            Log::info('Validando configuración de categorías', ['league_id' => $league->id]);

            $validation = [
                'valid' => true,
                'errors' => [],
                'warnings' => [],
                'suggestions' => [],
                'statistics' => []
            ];

            $categories = $league->getActiveCategories();

            // Validación básica: debe tener al menos una categoría
            if ($categories->isEmpty()) {
                $validation['valid'] = false;
                $validation['errors'][] = 'La liga debe tener al menos una categoría configurada';
                return $validation;
            }

            // Validar integridad de cada categoría
            $this->validateIndividualCategories($categories, $validation);

            // Validar superposiciones entre categorías
            $this->validateCategoryOverlaps($categories, $validation);

            // Validar cobertura de rangos de edad
            $this->validateAgeRangeCoverage($categories, $validation);

            // Validar distribución de género
            $this->validateGenderDistribution($categories, $validation);

            // Generar estadísticas
            $validation['statistics'] = $this->generateConfigurationStatistics($categories);

            // Generar sugerencias de mejora
            $this->generateImprovementSuggestions($categories, $validation);

            Log::info('Validación de configuración completada', [
                'league_id' => $league->id,
                'valid' => $validation['valid'],
                'errors_count' => count($validation['errors']),
                'warnings_count' => count($validation['warnings'])
            ]);

            return $validation;
        } catch (\Exception $e) {
            Log::error('Error validando configuración de categorías', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'errors' => ['Error interno validando la configuración: ' . $e->getMessage()],
                'warnings' => [],
                'suggestions' => [],
                'statistics' => []
            ];
        }
    }
    /**
     * Valida categorías individuales
     */
    protected function validateIndividualCategories(Collection $categories, array &$validation): void
    {
        foreach ($categories as $category) {
            // Validar rangos de edad
            if ($category->min_age > $category->max_age) {
                $validation['valid'] = false;
                $validation['errors'][] = "La categoría '{$category->name}' tiene un rango de edad inválido ({$category->min_age}-{$category->max_age})";
            }

            if ($category->min_age < 5 || $category->max_age > 100) {
                $validation['warnings'][] = "La categoría '{$category->name}' tiene un rango de edad inusual ({$category->min_age}-{$category->max_age})";
            }

            // Validar nombre y código
            if (empty($category->name)) {
                $validation['valid'] = false;
                $validation['errors'][] = "Hay una categoría sin nombre (ID: {$category->id})";
            }

            if (strlen($category->name) > 50) {
                $validation['warnings'][] = "El nombre de la categoría '{$category->name}' es muy largo";
            }

            // Validar género
            if (!in_array($category->gender, ['male', 'female', 'mixed'])) {
                $validation['valid'] = false;
                $validation['errors'][] = "La categoría '{$category->name}' tiene un género inválido: {$category->gender}";
            }
        }
    }

    /**
     * Valida superposiciones entre categorías
     */
    protected function validateCategoryOverlaps(Collection $categories, array &$validation): void
    {
        $categoriesArray = $categories->toArray();

        for ($i = 0; $i < count($categoriesArray); $i++) {
            for ($j = $i + 1; $j < count($categoriesArray); $j++) {
                $category1 = $categoriesArray[$i];
                $category2 = $categoriesArray[$j];

                if ($this->categoriesOverlap($category1, $category2)) {
                    $validation['warnings'][] = "Las categorías '{$category1['name']}' y '{$category2['name']}' tienen superposición de rangos de edad y género";
                }
            }
        }
    }

    /**
     * Verifica si dos categorías se superponen
     */
    protected function categoriesOverlap(array $cat1, array $cat2): bool
    {
        // Verificar superposición de edad
        $ageOverlap = !($cat1['max_age'] < $cat2['min_age'] || $cat1['min_age'] > $cat2['max_age']);

        // Verificar superposición de género
        $genderOverlap = ($cat1['gender'] === 'mixed' || $cat2['gender'] === 'mixed' || $cat1['gender'] === $cat2['gender']);

        return $ageOverlap && $genderOverlap;
    }

    /**
     * Valida cobertura de rangos de edad
     */
    protected function validateAgeRangeCoverage(Collection $categories, array &$validation): void
    {
        $sortedCategories = $categories->sortBy('min_age');
        $previousMaxAge = null;

        foreach ($sortedCategories as $category) {
            if ($previousMaxAge !== null && $category->min_age > $previousMaxAge + 1) {
                $gapStart = $previousMaxAge + 1;
                $gapEnd = $category->min_age - 1;
                $validation['warnings'][] = "Hay un gap en las edades {$gapStart}-{$gapEnd} que no está cubierto por ninguna categoría";
            }
            $previousMaxAge = max($previousMaxAge ?? 0, $category->max_age);
        }

        // Verificar cobertura de edades comunes
        $commonAges = [8, 12, 16, 20, 25, 30];
        foreach ($commonAges as $age) {
            $covered = $categories->first(function ($category) use ($age) {
                return $age >= $category->min_age && $age <= $category->max_age;
            });

            if (!$covered) {
                $validation['suggestions'][] = "Considere agregar cobertura para la edad {$age} años";
            }
        }
    }

    /**
     * Valida distribución de género
     */
    protected function validateGenderDistribution(Collection $categories, array &$validation): void
    {
        $genderStats = $categories->groupBy('gender')->map->count();

        if (!isset($genderStats['mixed']) && !isset($genderStats['male']) && !isset($genderStats['female'])) {
            $validation['warnings'][] = 'No hay categorías configuradas para ningún género específico';
        }

        if (isset($genderStats['male']) && isset($genderStats['female']) && !isset($genderStats['mixed'])) {
            $validation['suggestions'][] = 'Considere agregar categorías mixtas para mayor flexibilidad';
        }
    }

    /**
     * Genera estadísticas de configuración
     */
    protected function generateConfigurationStatistics(Collection $categories): array
    {
        return [
            'total_categories' => $categories->count(),
            'age_range' => [
                'min' => $categories->min('min_age'),
                'max' => $categories->max('max_age'),
                'span' => $categories->max('max_age') - $categories->min('min_age')
            ],
            'gender_distribution' => $categories->groupBy('gender')->map->count()->toArray(),
            'average_age_span' => round($categories->avg(function ($cat) {
                return $cat->max_age - $cat->min_age;
            }), 1),
            'categories_with_special_rules' => $categories->whereNotNull('special_rules')->count(),
            'inactive_categories' => $categories->where('is_active', false)->count()
        ];
    }

    /**
     * Genera sugerencias de mejora
     */
    protected function generateImprovementSuggestions(Collection $categories, array &$validation): void
    {
        // Sugerir optimizaciones basadas en estadísticas
        $stats = $validation['statistics'];

        if ($stats['total_categories'] < 3) {
            $validation['suggestions'][] = 'Considere agregar más categorías para mejor segmentación por edad';
        }

        if ($stats['total_categories'] > 10) {
            $validation['suggestions'][] = 'Muchas categorías pueden complicar la gestión. Considere consolidar algunas';
        }

        if ($stats['average_age_span'] > 5) {
            $validation['suggestions'][] = 'Los rangos de edad son muy amplios. Considere rangos más específicos';
        }

        if ($stats['average_age_span'] < 2) {
            $validation['suggestions'][] = 'Los rangos de edad son muy estrechos. Considere rangos más amplios';
        }

        // Sugerir categorías faltantes comunes
        $hasYouthCategories = $categories->where('max_age', '<=', 18)->isNotEmpty();
        $hasAdultCategories = $categories->where('min_age', '>=', 19)->isNotEmpty();
        $hasMastersCategories = $categories->where('min_age', '>=', 35)->isNotEmpty();

        if (!$hasYouthCategories) {
            $validation['suggestions'][] = 'Considere agregar categorías juveniles (menores de 18 años)';
        }

        if (!$hasAdultCategories) {
            $validation['suggestions'][] = 'Considere agregar categorías para adultos (19+ años)';
        }

        if (!$hasMastersCategories) {
            $validation['suggestions'][] = 'Considere agregar una categoría Masters para veteranas (35+ años)';
        }
    }

    /**
     * Importa configuración de categorías desde otra liga
     */
    public function importCategoriesFromLeague(League $targetLeague, League $sourceLeague, bool $replaceExisting = false): array
    {
        try {
            Log::info('Importando categorías entre ligas', [
                'target_league_id' => $targetLeague->id,
                'source_league_id' => $sourceLeague->id,
                'replace_existing' => $replaceExisting
            ]);

            // Verificar que la liga origen tenga categorías
            $sourceCategories = $sourceLeague->getActiveCategories();
            if ($sourceCategories->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'La liga origen no tiene categorías configuradas'
                ];
            }

            // Verificar si la liga destino ya tiene categorías
            if ($targetLeague->hasCustomCategories() && !$replaceExisting) {
                return [
                    'success' => false,
                    'message' => 'La liga destino ya tiene categorías. Use replaceExisting=true para reemplazar'
                ];
            }

            $importedCategories = [];

            DB::transaction(function () use ($targetLeague, $sourceCategories, $replaceExisting, &$importedCategories) {
                // Eliminar categorías existentes si se especifica reemplazo
                if ($replaceExisting) {
                    $targetLeague->categories()->delete();
                    Log::info('Categorías existentes eliminadas', ['league_id' => $targetLeague->id]);
                }

                // Importar categorías
                foreach ($sourceCategories as $sourceCategory) {
                    $categoryData = $sourceCategory->toArray();

                    // Remover campos que no deben copiarse
                    unset($categoryData['id'], $categoryData['league_id'], $categoryData['created_at'], $categoryData['updated_at']);

                    // Asignar a la liga destino
                    $categoryData['league_id'] = $targetLeague->id;

                    $newCategory = LeagueCategory::create($categoryData);
                    $importedCategories[] = $newCategory;

                    Log::debug('Categoría importada', [
                        'target_league_id' => $targetLeague->id,
                        'category_name' => $newCategory->name
                    ]);
                }
            });

            // Limpiar caché
            $this->clearLeagueCache($targetLeague->id);

            Log::info('Importación de categorías completada', [
                'target_league_id' => $targetLeague->id,
                'categories_imported' => count($importedCategories)
            ]);

            return [
                'success' => true,
                'message' => 'Categorías importadas exitosamente',
                'categories_imported' => count($importedCategories),
                'categories' => collect($importedCategories)->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'age_range' => $category->age_range_label
                    ];
                })->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Error importando categorías', [
                'target_league_id' => $targetLeague->id,
                'source_league_id' => $sourceLeague->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error importando categorías: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Exporta configuración de categorías de una liga
     */
    public function exportCategoriesConfiguration(League $league): array
    {
        try {
            Log::info('Exportando configuración de categorías', ['league_id' => $league->id]);

            $categories = $league->getActiveCategories();

            if ($categories->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'La liga no tiene categorías configuradas para exportar'
                ];
            }

            $exportData = [
                'league_info' => [
                    'id' => $league->id,
                    'name' => $league->name,
                    'export_date' => now()->toISOString()
                ],
                'categories' => $categories->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'code' => $category->code,
                        'description' => $category->description,
                        'gender' => $category->gender,
                        'min_age' => $category->min_age,
                        'max_age' => $category->max_age,
                        'special_rules' => $category->special_rules,
                        'sort_order' => $category->sort_order,
                        'color' => $category->color,
                        'icon' => $category->icon,
                        'is_active' => $category->is_active
                    ];
                })->toArray(),
                'validation' => $this->validateCategoryConfiguration($league),
                'statistics' => [
                    'total_categories' => $categories->count(),
                    'player_distribution' => $this->getCategoryPlayerDistribution($league)
                ]
            ];

            Log::info('Configuración exportada exitosamente', [
                'league_id' => $league->id,
                'categories_exported' => count($exportData['categories'])
            ]);

            return [
                'success' => true,
                'message' => 'Configuración exportada exitosamente',
                'data' => $exportData
            ];
        } catch (\Exception $e) {
            Log::error('Error exportando configuración', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error exportando configuración: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene distribución de jugadoras por categoría
     */
    protected function getCategoryPlayerDistribution(League $league): array
    {
        $distribution = [];

        foreach ($league->getActiveCategories() as $category) {
            $stats = $category->getPlayerStats();
            $distribution[$category->name] = [
                'total' => $stats['total'],
                'male' => $stats['male'],
                'female' => $stats['female'],
                'percentage' => 0 // Se calculará después
            ];
        }

        // Calcular porcentajes
        $totalPlayers = array_sum(array_column($distribution, 'total'));
        if ($totalPlayers > 0) {
            foreach ($distribution as $categoryName => &$stats) {
                $stats['percentage'] = round(($stats['total'] / $totalPlayers) * 100, 1);
            }
        }

        return $distribution;
    }

    /**
     * Limpia caché relacionado con una liga
     */
    protected function clearLeagueCache(int $leagueId): void
    {
        Cache::forget("league_categories_{$leagueId}");

        // Solo usar tags si el driver lo soporta
        if (config('cache.default') === 'redis' || config('cache.default') === 'memcached') {
            Cache::tags(['categories', "league_{$leagueId}"])->flush();
        }

        Log::debug('Caché de liga limpiado', ['league_id' => $leagueId]);
    }
}
