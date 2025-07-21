<?php

namespace App\Services;

use App\Models\League;
use App\Models\LeagueCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class CategoryValidationService
{
    /**
     * Valida rangos de edad para evitar superposiciones problemáticas
     */
    public function validateAgeRanges(Collection $categories): array
    {
        $errors = [];
        $warnings = [];

        Log::info('Validando rangos de edad', ['categories_count' => $categories->count()]);

        // Ordenar categorías por edad mínima
        $sortedCategories = $categories->sortBy('min_age');

        foreach ($sortedCategories as $i => $category) {
            // Validar rango individual
            if ($category->min_age > $category->max_age) {
                $errors[] = "La categoría '{$category->name}' tiene un rango inválido: {$category->min_age}-{$category->max_age}";
                continue;
            }

            // Validar rangos extremos
            if ($category->min_age < 5) {
                $warnings[] = "La categoría '{$category->name}' tiene una edad mínima muy baja ({$category->min_age} años)";
            }

            if ($category->max_age > 80) {
                $warnings[] = "La categoría '{$category->name}' tiene una edad máxima muy alta ({$category->max_age} años)";
            }

            // Validar superposiciones con otras categorías
            foreach ($sortedCategories->slice($i + 1) as $otherCategory) {
                $overlap = $this->calculateAgeOverlap($category, $otherCategory);

                if ($overlap['has_overlap']) {
                    $severity = $this->determineOverlapSeverity($overlap);

                    if ($severity === 'error') {
                        $errors[] = "Superposición crítica entre '{$category->name}' y '{$otherCategory->name}': {$overlap['overlap_range']}";
                    } else {
                        $warnings[] = "Superposición entre '{$category->name}' y '{$otherCategory->name}': {$overlap['overlap_range']}";
                    }
                }
            }

            // Validar gaps entre categorías consecutivas
            $nextCategory = $sortedCategories->get($i + 1);
            if ($nextCategory && $nextCategory->min_age > $category->max_age + 1) {
                $gapStart = $category->max_age + 1;
                $gapEnd = $nextCategory->min_age - 1;
                $warnings[] = "Gap de edad entre '{$category->name}' y '{$nextCategory->name}': {$gapStart}-{$gapEnd} años no cubiertos";
            }
        }

        return compact('errors', 'warnings');
    }

    /**
     * Calcula superposición entre dos categorías
     */
    protected function calculateAgeOverlap(LeagueCategory $cat1, LeagueCategory $cat2): array
    {
        $overlapStart = max($cat1->min_age, $cat2->min_age);
        $overlapEnd = min($cat1->max_age, $cat2->max_age);

        $hasOverlap = $overlapStart <= $overlapEnd;

        return [
            'has_overlap' => $hasOverlap,
            'overlap_start' => $hasOverlap ? $overlapStart : null,
            'overlap_end' => $hasOverlap ? $overlapEnd : null,
            'overlap_range' => $hasOverlap ? "{$overlapStart}-{$overlapEnd} años" : null,
            'overlap_years' => $hasOverlap ? ($overlapEnd - $overlapStart + 1) : 0
        ];
    }

    /**
     * Determina la severidad de una superposición
     */
    protected function determineOverlapSeverity(array $overlap): string
    {
        if (!$overlap['has_overlap']) {
            return 'none';
        }

        // Superposición de más de 2 años es crítica
        if ($overlap['overlap_years'] > 2) {
            return 'error';
        }

        // Superposición de 1-2 años es advertencia
        return 'warning';
    }

    /**
     * Detecta superposición de rangos por género
     */
    public function validateGenderOverlaps(Collection $categories): array
    {
        $errors = [];
        $warnings = [];

        Log::info('Validando superposiciones por género');

        // Agrupar por género
        $byGender = $categories->groupBy('gender');

        foreach (['male', 'female', 'mixed'] as $gender) {
            $genderCategories = $byGender->get($gender, collect());

            if ($genderCategories->count() < 2) {
                continue;
            }

            // Validar superposiciones dentro del mismo género
            $genderValidation = $this->validateAgeRanges($genderCategories);

            foreach ($genderValidation['errors'] as $error) {
                $errors[] = "Género {$gender}: {$error}";
            }

            foreach ($genderValidation['warnings'] as $warning) {
                $warnings[] = "Género {$gender}: {$warning}";
            }
        }

        // Validar conflictos entre categorías mixtas y específicas de género
        $this->validateMixedGenderConflicts($byGender, $errors, $warnings);

        return compact('errors', 'warnings');
    }

    /**
     * Valida conflictos entre categorías mixtas y específicas de género
     */
    protected function validateMixedGenderConflicts(Collection $byGender, array &$errors, array &$warnings): void
    {
        $mixedCategories = $byGender->get('mixed', collect());
        $maleCategories = $byGender->get('male', collect());
        $femaleCategories = $byGender->get('female', collect());

        // Verificar conflictos entre mixtas y masculinas
        foreach ($mixedCategories as $mixedCat) {
            foreach ($maleCategories as $maleCat) {
                $overlap = $this->calculateAgeOverlap($mixedCat, $maleCat);
                if ($overlap['has_overlap']) {
                    $warnings[] = "Conflicto potencial: categoría mixta '{$mixedCat->name}' se superpone con masculina '{$maleCat->name}' en {$overlap['overlap_range']}";
                }
            }
        }

        // Verificar conflictos entre mixtas y femeninas
        foreach ($mixedCategories as $mixedCat) {
            foreach ($femaleCategories as $femaleCat) {
                $overlap = $this->calculateAgeOverlap($mixedCat, $femaleCat);
                if ($overlap['has_overlap']) {
                    $warnings[] = "Conflicto potencial: categoría mixta '{$mixedCat->name}' se superpone con femenina '{$femaleCat->name}' en {$overlap['overlap_range']}";
                }
            }
        }
    }

    /**
     * Valida reglas especiales y consistencia de datos
     */
    public function validateSpecialRulesConsistency(Collection $categories): array
    {
        $errors = [];
        $warnings = [];

        Log::info('Validando consistencia de reglas especiales');

        foreach ($categories as $category) {
            if (!$category->special_rules) {
                continue;
            }

            $ruleValidation = $this->validateCategorySpecialRules($category);
            $errors = array_merge($errors, $ruleValidation['errors']);
            $warnings = array_merge($warnings, $ruleValidation['warnings']);
        }

        return compact('errors', 'warnings');
    }

    /**
     * Valida reglas especiales de una categoría individual
     */
    protected function validateCategorySpecialRules(LeagueCategory $category): array
    {
        $errors = [];
        $warnings = [];

        foreach ($category->special_rules as $rule) {
            $ruleType = $rule['type'] ?? 'unknown';

            switch ($ruleType) {
                case 'age_override':
                    $this->validateAgeOverrideRule($category, $rule, $errors, $warnings);
                    break;

                case 'gender_restriction':
                    $this->validateGenderRestrictionRule($category, $rule, $errors, $warnings);
                    break;

                case 'combined_condition':
                    $this->validateCombinedConditionRule($category, $rule, $errors, $warnings);
                    break;

                case 'priority_rule':
                    $this->validatePriorityRule($category, $rule, $errors, $warnings);
                    break;

                default:
                    $warnings[] = "Categoría '{$category->name}': tipo de regla desconocido '{$ruleType}'";
            }
        }

        return compact('errors', 'warnings');
    }

    /**
     * Valida reglas de override de edad
     */
    protected function validateAgeOverrideRule(LeagueCategory $category, array $rule, array &$errors, array &$warnings): void
    {
        if (isset($rule['min_age_override'])) {
            $minOverride = $rule['min_age_override'];
            if ($minOverride < $category->min_age) {
                $warnings[] = "Categoría '{$category->name}': override de edad mínima ({$minOverride}) es menor que el rango base ({$category->min_age})";
            }
            if ($minOverride < 5 || $minOverride > 80) {
                $warnings[] = "Categoría '{$category->name}': override de edad mínima ({$minOverride}) parece inusual";
            }
        }

        if (isset($rule['max_age_override'])) {
            $maxOverride = $rule['max_age_override'];
            if ($maxOverride > $category->max_age) {
                $warnings[] = "Categoría '{$category->name}': override de edad máxima ({$maxOverride}) es mayor que el rango base ({$category->max_age})";
            }
            if ($maxOverride < 5 || $maxOverride > 80) {
                $warnings[] = "Categoría '{$category->name}': override de edad máxima ({$maxOverride}) parece inusual";
            }
        }

        if (isset($rule['excluded_ages']) && is_array($rule['excluded_ages'])) {
            foreach ($rule['excluded_ages'] as $excludedAge) {
                if ($excludedAge < $category->min_age || $excludedAge > $category->max_age) {
                    $warnings[] = "Categoría '{$category->name}': edad excluida ({$excludedAge}) está fuera del rango base";
                }
            }
        }
    }

    /**
     * Valida reglas de restricción de género
     */
    protected function validateGenderRestrictionRule(LeagueCategory $category, array $rule, array &$errors, array &$warnings): void
    {
        $allowedGenders = $rule['allowed_genders'] ?? [];

        if (!is_array($allowedGenders)) {
            $errors[] = "Categoría '{$category->name}': allowed_genders debe ser un array";
            return;
        }

        foreach ($allowedGenders as $gender) {
            if (!in_array($gender, ['male', 'female'])) {
                $errors[] = "Categoría '{$category->name}': género permitido inválido '{$gender}'";
            }
        }

        // Verificar consistencia con el género base de la categoría
        if ($category->gender !== 'mixed' && !in_array($category->gender, $allowedGenders)) {
            $warnings[] = "Categoría '{$category->name}': restricción de género no incluye el género base de la categoría";
        }
    }

    /**
     * Valida reglas de condición combinada
     */
    protected function validateCombinedConditionRule(LeagueCategory $category, array $rule, array &$errors, array &$warnings): void
    {
        $conditions = $rule['conditions'] ?? [];
        $operator = $rule['operator'] ?? 'AND';

        if (!is_array($conditions)) {
            $errors[] = "Categoría '{$category->name}': conditions debe ser un array";
            return;
        }

        if (!in_array($operator, ['AND', 'OR'])) {
            $errors[] = "Categoría '{$category->name}': operador '{$operator}' no válido (debe ser AND u OR)";
        }

        foreach ($conditions as $i => $condition) {
            if (!is_array($condition)) {
                $errors[] = "Categoría '{$category->name}': condición {$i} debe ser un array";
                continue;
            }

            $this->validateConditionStructure($category, $condition, $i, $errors, $warnings);
        }
    }

    /**
     * Valida estructura de una condición
     */
    protected function validateConditionStructure(LeagueCategory $category, array $condition, int $index, array &$errors, array &$warnings): void
    {
        $field = $condition['field'] ?? '';
        $operator = $condition['operator'] ?? '';
        $value = $condition['value'] ?? null;

        if (!in_array($field, ['age', 'gender'])) {
            $errors[] = "Categoría '{$category->name}': campo '{$field}' no válido en condición {$index}";
        }

        $validOperators = ['=', '!=', '>', '<', '>=', '<=', 'in', 'not_in'];
        if (!in_array($operator, $validOperators)) {
            $errors[] = "Categoría '{$category->name}': operador '{$operator}' no válido en condición {$index}";
        }

        if ($value === null) {
            $warnings[] = "Categoría '{$category->name}': valor nulo en condición {$index}";
        }

        // Validaciones específicas por campo
        if ($field === 'age' && is_numeric($value)) {
            if ($value < 5 || $value > 80) {
                $warnings[] = "Categoría '{$category->name}': valor de edad inusual ({$value}) en condición {$index}";
            }
        }

        if ($field === 'gender' && is_string($value)) {
            if (!in_array($value, ['male', 'female', 'mixed'])) {
                $errors[] = "Categoría '{$category->name}': valor de género inválido '{$value}' en condición {$index}";
            }
        }
    }

    /**
     * Valida reglas de prioridad
     */
    protected function validatePriorityRule(LeagueCategory $category, array $rule, array &$errors, array &$warnings): void
    {
        $priority = $rule['priority'] ?? 'normal';
        $conditions = $rule['conditions'] ?? [];

        if (!in_array($priority, ['low', 'normal', 'high', 'critical'])) {
            $errors[] = "Categoría '{$category->name}': prioridad '{$priority}' no válida";
        }

        if (!is_array($conditions)) {
            $errors[] = "Categoría '{$category->name}': conditions en regla de prioridad debe ser un array";
            return;
        }

        foreach ($conditions as $i => $condition) {
            $this->validateConditionStructure($category, $condition, $i, $errors, $warnings);
        }

        // Advertir sobre prioridades críticas
        if ($priority === 'critical') {
            $warnings[] = "Categoría '{$category->name}': usa prioridad crítica, verifique que sea necesaria";
        }
    }

    /**
     * Genera reporte completo de validación
     */
    public function generateValidationReport(League $league): array
    {
        Log::info('Generando reporte completo de validación', ['league_id' => $league->id]);

        $categories = $league->getActiveCategories();
        $report = [
            'league_id' => $league->id,
            'league_name' => $league->name,
            'validation_date' => now()->toISOString(),
            'categories_count' => $categories->count(),
            'overall_status' => 'valid',
            'validations' => [],
            'summary' => [
                'total_errors' => 0,
                'total_warnings' => 0,
                'critical_issues' => [],
                'recommendations' => []
            ]
        ];

        // Validar rangos de edad
        $ageValidation = $this->validateAgeRanges($categories);
        $report['validations']['age_ranges'] = $ageValidation;

        // Validar superposiciones de género
        $genderValidation = $this->validateGenderOverlaps($categories);
        $report['validations']['gender_overlaps'] = $genderValidation;

        // Validar reglas especiales
        $rulesValidation = $this->validateSpecialRulesConsistency($categories);
        $report['validations']['special_rules'] = $rulesValidation;

        // Calcular totales
        $allErrors = array_merge(
            $ageValidation['errors'],
            $genderValidation['errors'],
            $rulesValidation['errors']
        );

        $allWarnings = array_merge(
            $ageValidation['warnings'],
            $genderValidation['warnings'],
            $rulesValidation['warnings']
        );

        $report['summary']['total_errors'] = count($allErrors);
        $report['summary']['total_warnings'] = count($allWarnings);

        // Determinar estado general
        if (count($allErrors) > 0) {
            $report['overall_status'] = 'invalid';
            $report['summary']['critical_issues'] = $allErrors;
        } elseif (count($allWarnings) > 0) {
            $report['overall_status'] = 'valid_with_warnings';
        }

        // Generar recomendaciones
        $report['summary']['recommendations'] = $this->generateRecommendations($categories, $allErrors, $allWarnings);

        Log::info('Reporte de validación generado', [
            'league_id' => $league->id,
            'status' => $report['overall_status'],
            'errors' => $report['summary']['total_errors'],
            'warnings' => $report['summary']['total_warnings']
        ]);

        return $report;
    }

    /**
     * Genera recomendaciones basadas en los resultados de validación
     */
    protected function generateRecommendations(Collection $categories, array $errors, array $warnings): array
    {
        $recommendations = [];

        // Recomendaciones basadas en errores
        if (count($errors) > 0) {
            $recommendations[] = 'Corrija los errores críticos antes de activar las categorías';

            if (count($errors) > 5) {
                $recommendations[] = 'Considere revisar completamente la configuración debido al alto número de errores';
            }
        }

        // Recomendaciones basadas en advertencias
        if (count($warnings) > 3) {
            $recommendations[] = 'Revise las advertencias para optimizar la configuración';
        }

        // Recomendaciones basadas en estadísticas
        if ($categories->count() < 3) {
            $recommendations[] = 'Considere agregar más categorías para mejor segmentación';
        }

        if ($categories->count() > 8) {
            $recommendations[] = 'Muchas categorías pueden complicar la gestión';
        }

        // Recomendaciones de cobertura
        $ageSpan = $categories->max('max_age') - $categories->min('min_age');
        if ($ageSpan < 20) {
            $recommendations[] = 'La cobertura de edad es limitada, considere ampliar rangos';
        }

        if ($ageSpan > 60) {
            $recommendations[] = 'La cobertura de edad es muy amplia, considere segmentar mejor';
        }

        return $recommendations;
    }
}
