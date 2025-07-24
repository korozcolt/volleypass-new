<?php

namespace App\Services;

use App\Models\League;
use App\Models\LeagueCategory;
use App\Models\Player;
use App\Models\User;
use App\Enums\PlayerCategory;
use App\Services\CategoryNotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CategoryAssignmentService
{
    /**
     * Asigna automáticamente una categoría a una jugadora basándose en su perfil
     */
    public function assignAutomaticCategory(Player $player): ?string
    {
        try {
            $league = $player->currentClub->league;
            $age = $player->user->age ?? 0;
            $gender = $player->user->gender ?? 'unknown';

            Log::info('Iniciando asignación automática de categoría', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'age' => $age,
                'gender' => $gender
            ]);

            // Validar elegibilidad básica
            if (!$this->validateBasicEligibility($age, $gender)) {
                Log::warning('Jugadora no cumple elegibilidad básica', [
                    'player_id' => $player->id,
                    'age' => $age,
                    'gender' => $gender
                ]);
                return null;
            }

            // Intentar asignación dinámica si la liga tiene categorías configuradas
            if ($league->hasCustomCategories()) {
                $category = $this->assignDynamicCategory($league, $age, $gender);
                if ($category) {
                    Log::info('Categoría dinámica asignada exitosamente', [
                        'player_id' => $player->id,
                        'category' => $category
                    ]);
                    return $category;
                }
            }

            // Fallback al enum tradicional
            $fallbackCategory = $this->assignFallbackCategory($age);
            Log::info('Categoría fallback asignada', [
                'player_id' => $player->id,
                'category' => $fallbackCategory,
                'reason' => 'No hay configuración dinámica o no se encontró categoría apropiada'
            ]);

            return $fallbackCategory;
        } catch (\Exception $e) {
            Log::error('Error en asignación automática de categoría', [
                'player_id' => $player->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // En caso de error, usar fallback básico
            return $this->assignFallbackCategory($player->user->age ?? 19);
        }
    }

    /**
     * Asigna categoría usando configuración dinámica de la liga
     */
    protected function assignDynamicCategory(League $league, int $age, string $gender): ?string
    {
        // Usar caché para optimizar consultas frecuentes
        $cacheKey = "league_categories_{$league->id}";
        $categories = Cache::remember($cacheKey, 3600, function () use ($league) {
            return $league->getActiveCategories();
        });

        if ($categories->isEmpty()) {
            Log::info('No hay categorías dinámicas configuradas', [
                'league_id' => $league->id
            ]);
            return null;
        }

        // Buscar categoría elegible con validación completa
        foreach ($categories as $category) {
            if ($this->validateEligibilityForCategory($category, $age, $gender)) {
                Log::info('Categoría dinámica encontrada', [
                    'league_id' => $league->id,
                    'category' => $category->name,
                    'age' => $age,
                    'gender' => $gender
                ]);
                return $category->name;
            }
        }

        Log::warning('No se encontró categoría dinámica apropiada', [
            'league_id' => $league->id,
            'age' => $age,
            'gender' => $gender,
            'available_categories' => $categories->pluck('name')->toArray()
        ]);

        return null;
    }

    /**
     * Valida elegibilidad completa para una categoría específica
     */
    protected function validateEligibilityForCategory(LeagueCategory $category, int $age, string $gender): bool
    {
        // Validación básica de edad y género
        if (!$category->isEligibleForPlayer($age, $gender)) {
            return false;
        }

        // Validar reglas especiales si existen
        if ($category->special_rules) {
            return $this->validateSpecialRules($category, $age, $gender);
        }

        return true;
    }

    /**
     * Valida reglas especiales de una categoría
     */
    protected function validateSpecialRules(LeagueCategory $category, int $age, string $gender): bool
    {
        $specialRules = $category->special_rules;

        if (empty($specialRules)) {
            return true;
        }

        foreach ($specialRules as $rule) {
            if (!$this->evaluateSpecialRule($rule, $age, $gender)) {
                Log::debug('Regla especial no cumplida', [
                    'category' => $category->name,
                    'rule' => $rule,
                    'age' => $age,
                    'gender' => $gender
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Evalúa una regla especial individual
     */
    protected function evaluateSpecialRule(array $rule, int $age, string $gender): bool
    {
        $type = $rule['type'] ?? '';

        return match ($type) {
            'age_override' => $this->evaluateAgeOverride($rule, $age),
            'gender_restriction' => $this->evaluateGenderRestriction($rule, $gender),
            'combined_condition' => $this->evaluateCombinedCondition($rule, $age, $gender),
            'priority_rule' => $this->evaluatePriorityRule($rule, $age, $gender),
            default => true // Reglas desconocidas se consideran válidas
        };
    }

    /**
     * Evalúa reglas de override de edad
     */
    protected function evaluateAgeOverride(array $rule, int $age): bool
    {
        if (isset($rule['min_age_override']) && $age < $rule['min_age_override']) {
            return false;
        }

        if (isset($rule['max_age_override']) && $age > $rule['max_age_override']) {
            return false;
        }

        if (isset($rule['excluded_ages']) && in_array($age, $rule['excluded_ages'])) {
            return false;
        }

        return true;
    }

    /**
     * Evalúa restricciones de género
     */
    protected function evaluateGenderRestriction(array $rule, string $gender): bool
    {
        $allowedGenders = $rule['allowed_genders'] ?? ['male', 'female'];
        return in_array($gender, $allowedGenders);
    }

    /**
     * Evalúa condiciones combinadas
     */
    protected function evaluateCombinedCondition(array $rule, int $age, string $gender): bool
    {
        $conditions = $rule['conditions'] ?? [];
        $operator = $rule['operator'] ?? 'AND';

        $results = [];
        foreach ($conditions as $condition) {
            $results[] = $this->evaluateCondition($condition, $age, $gender);
        }

        return match ($operator) {
            'AND' => !in_array(false, $results),
            'OR' => in_array(true, $results),
            default => true
        };
    }

    /**
     * Evalúa reglas de prioridad
     */
    protected function evaluatePriorityRule(array $rule, int $age, string $gender): bool
    {
        $priority = $rule['priority'] ?? 'normal';
        $conditions = $rule['conditions'] ?? [];

        // Las reglas de alta prioridad siempre se evalúan
        if ($priority === 'high') {
            return true;
        }

        // Evaluar condiciones específicas
        foreach ($conditions as $condition) {
            if (!$this->evaluateCondition($condition, $age, $gender)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evalúa una condición individual
     */
    protected function evaluateCondition(array $condition, int $age, string $gender): bool
    {
        $field = $condition['field'] ?? '';
        $operator = $condition['operator'] ?? '=';
        $value = $condition['value'] ?? null;

        $fieldValue = match ($field) {
            'age' => $age,
            'gender' => $gender,
            default => null
        };

        return match ($operator) {
            '=' => $fieldValue == $value,
            '!=' => $fieldValue != $value,
            '>' => $fieldValue > $value,
            '<' => $fieldValue < $value,
            '>=' => $fieldValue >= $value,
            '<=' => $fieldValue <= $value,
            'in' => in_array($fieldValue, (array) $value),
            'not_in' => !in_array($fieldValue, (array) $value),
            default => true
        };
    }

    /**
     * Asigna categoría usando el enum tradicional como fallback
     */
    protected function assignFallbackCategory(int $age): ?string
    {
        $category = $this->getTraditionalCategoryForAge($age);

        if ($category) {
            Log::info('Categoría tradicional asignada', [
                'category' => $category->value,
                'age' => $age
            ]);
            return $category->value;
        }

        Log::warning('No se pudo asignar categoría tradicional', [
            'age' => $age
        ]);

        return null;
    }

    /**
     * Obtiene categoría tradicional basada en edad
     */
    protected function getTraditionalCategoryForAge(int $age): ?PlayerCategory
    {
        return match (true) {
            $age >= 8 && $age <= 10 => PlayerCategory::Mini,
            $age >= 11 && $age <= 12 => PlayerCategory::Pre_Mini,
            $age >= 13 && $age <= 14 => PlayerCategory::Infantil,
            $age >= 15 && $age <= 16 => PlayerCategory::Cadete,
            $age >= 17 && $age <= 18 => PlayerCategory::Juvenil,
            $age >= 35 => PlayerCategory::Masters,
            $age >= 19 && $age <= 34 => PlayerCategory::Mayores,
            default => null
        };
    }

    /**
     * Valida elegibilidad básica (edad mínima y género válido)
     */
    protected function validateBasicEligibility(int $age, string $gender): bool
    {
        // Edad mínima para jugar
        if ($age < 8 || $age > 100) {
            return false;
        }

        // Género válido
        if (!in_array($gender, ['male', 'female'])) {
            return false;
        }

        return true;
    }

    /**
     * Limpia caché de categorías para una liga
     */
    public function clearCategoryCache(int $leagueId): void
    {
        Cache::forget("league_categories_{$leagueId}");
        Log::info('Caché de categorías limpiado', ['league_id' => $leagueId]);
    }

    // =======================
    // VALIDACIÓN DE CAMBIOS DE CATEGORÍA
    // =======================

    /**
     * Valida si un cambio de categoría es válido para una jugadora
     */
    public function validateCategoryChange(Player $player, string $newCategory): array
    {
        $errors = [];
        $warnings = [];

        try {
            $league = $player->currentClub->league;
            $age = $player->user->age ?? 0;
            $gender = $player->user->gender ?? 'unknown';
            $currentCategory = $player->category?->value ?? $player->category;

            Log::info('Validando cambio de categoría', [
                'player_id' => $player->id,
                'current_category' => $currentCategory,
                'new_category' => $newCategory,
                'age' => $age,
                'gender' => $gender
            ]);

            // Validar que la nueva categoría sea diferente
            if ($currentCategory === $newCategory) {
                $warnings[] = 'La jugadora ya está en la categoría especificada';
                return compact('errors', 'warnings');
            }

            // Validar elegibilidad básica
            if (!$this->validateBasicEligibility($age, $gender)) {
                $errors[] = 'La jugadora no cumple con los requisitos básicos de elegibilidad';
                return compact('errors', 'warnings');
            }

            // Validar según configuración de la liga
            if ($league->hasCustomCategories()) {
                $validationResult = $this->validateDynamicCategoryChange($league, $age, $gender, $newCategory);
            } else {
                $validationResult = $this->validateTraditionalCategoryChange($age, $gender, $newCategory);
            }

            $errors = array_merge($errors, $validationResult['errors']);
            $warnings = array_merge($warnings, $validationResult['warnings']);

            // Validar impacto en jugadoras existentes
            $impactValidation = $this->validateChangeImpact($player, $newCategory);
            $warnings = array_merge($warnings, $impactValidation['warnings']);

            Log::info('Validación de cambio completada', [
                'player_id' => $player->id,
                'errors_count' => count($errors),
                'warnings_count' => count($warnings)
            ]);
        } catch (\Exception $e) {
            Log::error('Error validando cambio de categoría', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);
            $errors[] = 'Error interno validando el cambio de categoría';
        }

        return compact('errors', 'warnings');
    }

    /**
     * Valida cambio de categoría usando configuración dinámica
     */
    protected function validateDynamicCategoryChange(League $league, int $age, string $gender, string $newCategory): array
    {
        $errors = [];
        $warnings = [];

        // Buscar la categoría en la configuración de la liga
        $category = $league->categories()
            ->where('name', $newCategory)
            ->first();

        if (!$category) {
            $errors[] = "La categoría '{$newCategory}' no existe en la configuración de esta liga";
            return compact('errors', 'warnings');
        }

        if (!$category->is_active) {
            $errors[] = "La categoría '{$newCategory}' no está activa en esta liga";
            return compact('errors', 'warnings');
        }

        // Validar elegibilidad para la categoría
        if (!$this->validateEligibilityForCategory($category, $age, $gender)) {
            $errors[] = "La jugadora no es elegible para la categoría '{$newCategory}' según la configuración de la liga";

            // Proporcionar información específica sobre por qué no es elegible
            if (!$category->isEligibleForPlayer($age, $gender)) {
                if ($age < $category->min_age || $age > $category->max_age) {
                    $errors[] = "La edad de la jugadora ({$age} años) no está en el rango permitido para esta categoría ({$category->age_range_label})";
                }

                if ($category->gender !== 'mixed' && $category->gender !== $gender) {
                    $errors[] = "El género de la jugadora no coincide con las restricciones de la categoría ({$category->gender_label})";
                }
            }
        }

        return compact('errors', 'warnings');
    }

    /**
     * Valida cambio de categoría usando enum tradicional
     */
    protected function validateTraditionalCategoryChange(int $age, string $gender, string $newCategory): array
    {
        $errors = [];
        $warnings = [];

        // Verificar que la categoría existe en el enum
        $playerCategory = PlayerCategory::tryFrom($newCategory);
        if (!$playerCategory) {
            $errors[] = "La categoría '{$newCategory}' no es válida";
            return compact('errors', 'warnings');
        }

        // Validar elegibilidad por edad
        $ageRange = $playerCategory->getAgeRange();
        if ($age < $ageRange[0] || $age > $ageRange[1]) {
            $errors[] = "La edad de la jugadora ({$age} años) no corresponde al rango de la categoría {$playerCategory->getLabel()}";
        }

        // Sugerir categoría apropiada si la actual no es correcta
        $suggestedCategory = $this->getTraditionalCategoryForAge($age);
        if ($suggestedCategory && $suggestedCategory->value !== $newCategory) {
            $warnings[] = "La categoría sugerida para la edad de la jugadora es: {$suggestedCategory->getLabel()}";
        }

        return compact('errors', 'warnings');
    }

    /**
     * Valida el impacto del cambio en otras jugadoras
     */
    protected function validateChangeImpact(Player $player, string $newCategory): array
    {
        $warnings = [];

        try {
            // Verificar si hay otras jugadoras en situación similar
            $similarPlayers = Player::where('current_club_id', $player->current_club_id)
                ->whereHas('user', function ($query) use ($player) {
                    $query->where('age', $player->user->age)
                        ->where('gender', $player->user->gender);
                })
                ->where('id', '!=', $player->id)
                ->count();

            if ($similarPlayers > 0) {
                $warnings[] = "Hay {$similarPlayers} jugadora(s) más en el club con edad y género similar que podrían verse afectadas";
            }

            // Verificar si el cambio afecta la distribución de categorías del club
            $categoryStats = Player::where('current_club_id', $player->current_club_id)
                ->selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray();

            $currentCategoryCount = $categoryStats[$player->category?->value ?? $player->category] ?? 0;
            $newCategoryCount = $categoryStats[$newCategory] ?? 0;

            if ($currentCategoryCount <= 1) {
                $warnings[] = "Esta jugadora es la única en su categoría actual en el club";
            }

            if ($newCategoryCount >= 10) {
                $warnings[] = "La categoría de destino ya tiene muchas jugadoras ({$newCategoryCount})";
            }
        } catch (\Exception $e) {
            Log::warning('Error calculando impacto del cambio', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);
        }

        return compact('warnings');
    }

    /**
     * Obtiene alertas para cambios masivos de categoría
     */
    public function getChangeAlertsForLeague(League $league): array
    {
        $alerts = [];

        try {
            $players = $league->players()->with('user')->get();
            $potentialChanges = [];

            foreach ($players as $player) {
                $currentCategory = $player->category?->value ?? $player->category;
                $suggestedCategory = $this->assignAutomaticCategory($player);

                if ($currentCategory !== $suggestedCategory) {
                    $potentialChanges[] = [
                        'player_id' => $player->id,
                        'player_name' => $player->user->full_name,
                        'current_category' => $currentCategory,
                        'suggested_category' => $suggestedCategory,
                        'age' => $player->user->age,
                        'gender' => $player->user->gender
                    ];
                }
            }

            if (count($potentialChanges) > 0) {
                $alerts[] = [
                    'type' => 'category_mismatches',
                    'severity' => count($potentialChanges) > 10 ? 'high' : 'medium',
                    'message' => count($potentialChanges) . ' jugadora(s) podrían necesitar cambio de categoría',
                    'details' => $potentialChanges
                ];
            }

            // Verificar categorías sin jugadoras
            if ($league->hasCustomCategories()) {
                $emptyCategories = $league->getActiveCategories()->filter(function ($category) {
                    return $category->getPlayerStats()['total'] === 0;
                });

                if ($emptyCategories->isNotEmpty()) {
                    $alerts[] = [
                        'type' => 'empty_categories',
                        'severity' => 'low',
                        'message' => 'Hay categorías configuradas sin jugadoras',
                        'details' => $emptyCategories->pluck('name')->toArray()
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error generando alertas de cambios', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);
        }

        return $alerts;
    }

    /**
     * Ejecuta cambios masivos de categoría con validación
     * 
     * @param League $league La liga donde se realizan los cambios
     * @param array $changes Array de cambios con formato [['player_id' => int, 'new_category' => string], ...]
     * @param bool $notifyChanges Si se deben enviar notificaciones de los cambios
     * @param string $changeReason Motivo del cambio para incluir en las notificaciones
     * @return array Resultados de los cambios
     */
    public function executeMassiveCategoryChanges(League $league, array $changes, bool $notifyChanges = true, string $changeReason = 'Actualización de categoría'): array
    {
        $results = [
            'total' => count($changes),
            'successful' => 0,
            'failed' => 0,
            'details' => []
        ];
        
        $playerChanges = [];

        foreach ($changes as $change) {
            try {
                $player = Player::find($change['player_id']);
                if (!$player) {
                    $results['failed']++;
                    $results['details'][] = [
                        'player_id' => $change['player_id'],
                        'status' => 'failed',
                        'error' => 'Jugadora no encontrada'
                    ];
                    continue;
                }

                $validation = $this->validateCategoryChange($player, $change['new_category']);

                if (!empty($validation['errors'])) {
                    $results['failed']++;
                    $results['details'][] = [
                        'player_id' => $change['player_id'],
                        'player_name' => $player->user->full_name,
                        'status' => 'failed',
                        'errors' => $validation['errors']
                    ];
                    continue;
                }

                // Ejecutar el cambio
                $oldCategory = $player->category?->value ?? $player->category;
                $player->update(['category' => $change['new_category']]);

                $results['successful']++;
                $results['details'][] = [
                    'player_id' => $change['player_id'],
                    'player_name' => $player->user->full_name,
                    'status' => 'successful',
                    'old_category' => $oldCategory,
                    'new_category' => $change['new_category'],
                    'warnings' => $validation['warnings'] ?? []
                ];
                
                // Preparar datos para notificación
                if ($notifyChanges) {
                    $playerChanges[] = [
                        'player' => $player,
                        'old_category' => $oldCategory,
                        'new_category' => $change['new_category'],
                        'reason' => $changeReason
                    ];
                }

                Log::info('Cambio masivo de categoría ejecutado', [
                    'player_id' => $player->id,
                    'old_category' => $oldCategory,
                    'new_category' => $change['new_category']
                ]);
            } catch (\Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'player_id' => $change['player_id'] ?? 'unknown',
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];

                Log::error('Error en cambio masivo de categoría', [
                    'change' => $change,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Enviar notificaciones si hay cambios y se solicita
        if ($notifyChanges && !empty($playerChanges)) {
            app(CategoryNotificationService::class)->notifyBulkPlayerCategoryReassignments($playerChanges);
        }

        // Limpiar caché después de cambios masivos
        $this->clearCategoryCache($league->id);

        return $results;
    }
    
    /**
     * Actualiza la categoría de un jugador individual y envía notificaciones si se solicita
     * 
     * @param Player $player El jugador cuya categoría se actualizará
     * @param string $newCategory La nueva categoría a asignar
     * @param User|null $changedBy El usuario que realiza el cambio (opcional)
     * @param bool $notifyChange Si se debe enviar notificación del cambio
     * @param string $changeReason Motivo del cambio para incluir en la notificación
     * @return array Resultado de la operación
     */
    public function updatePlayerCategory(
        Player $player, 
        string $newCategory, 
        ?User $changedBy = null, 
        bool $notifyChange = true,
        string $changeReason = 'Actualización de categoría'
    ): array {
        $result = [
            'success' => false,
            'errors' => [],
            'warnings' => []
        ];
        
        try {
            // Validar el cambio de categoría
            $validation = $this->validateCategoryChange($player, $newCategory);
            
            if (!empty($validation['errors'])) {
                $result['errors'] = $validation['errors'];
                return $result;
            }
            
            // Guardar la categoría anterior para la notificación
            $oldCategory = $player->category?->value ?? $player->category;
            
            // Actualizar la categoría
            $player->update(['category' => $newCategory]);
            
            // Enviar notificación si se solicita
            if ($notifyChange) {
                app(CategoryNotificationService::class)->notifyPlayerCategoryReassigned(
                    $player,
                    $oldCategory,
                    $newCategory,
                    $changeReason
                );
            }
            
            // Limpiar caché si el jugador pertenece a una liga
            if ($player->league_id) {
                $this->clearCategoryCache($player->league_id);
            }
            
            $result['success'] = true;
            $result['warnings'] = $validation['warnings'] ?? [];
            
            Log::info('Categoría de jugador actualizada', [
                'player_id' => $player->id,
                'old_category' => $oldCategory,
                'new_category' => $newCategory,
                'changed_by' => $changedBy ? $changedBy->id : 'sistema'
            ]);
            
        } catch (\Exception $e) {
            $result['errors'][] = 'Error al actualizar la categoría: ' . $e->getMessage();
            
            Log::error('Error al actualizar categoría de jugador', [
                'player_id' => $player->id,
                'new_category' => $newCategory,
                'error' => $e->getMessage()
            ]);
        }
        
        return $result;
    }
}
