<?php

namespace App\Services;

use App\Models\League;
use App\Models\LeagueCategory;
use App\Models\Player;
use App\Enums\PlayerCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CategoryAssignmentService
{
    /**
     * Asigna automáticamente una categoría a una jugadora
     */
    public function assignAutomaticCategory(int $leagueId, int $age, string $gender): ?string
    {
        $league = League::find($leagueId);

        if (!$league) {
            Log::warning("Liga no encontrada para asignación de categoría", [
                'league_id' => $leagueId,
                'age' => $age,
                'gender' => $gender
            ]);
            return null;
        }

        // Intentar asignación dinámica si la liga tiene categorías configuradas
        if ($league->hasCustomCategories()) {
            return $this->assignDynamicCategory($league, $age, $gender);
        }

        // Fallback al enum tradicional
        return $this->assignTraditionalCategory($age, $gender);
    }

    /**
     * Asigna categoría usando configuración dinámica de la liga
     */
    protected function assignDynamicCategory(League $league, int $age, string $gender): ?string
    {
        $category = $league->findCategoryForPlayer($age, $gender);

        if ($category) {
            Log::info("Categoría dinámica asignada", [
                'league_id' => $league->id,
                'category' => $category->name,
                'age' => $age,
                'gender' => $gender
            ]);

            return $category->name;
        }

        Log::warning("No se encontró categoría dinámica apropiada", [
            'league_id' => $league->id,
            'age' => $age,
            'gender' => $gender
        ]);

        // Fallback al enum tradicional si no hay categoría dinámica
        return $this->assignTraditionalCategory($age, $gender);
    }

    /**
     * Asigna categoría usando el enum tradicional
     */
    protected function assignTraditionalCategory(int $age, string $gender): ?string
    {
        $category = $this->getTraditionalCategoryForAge($age);

        if ($category) {
            Log::info("Categoría tradicional asignada", [
                'category' => $category->value,
                'age' => $age,
                'gender' => $gender
            ]);

            return $category->value;
        }

        Log::warning("No se pudo asignar categoría tradicional", [
            'age' => $age,
            'gender' => $gender
        ]);

        return null;
    }

    /**
     * Obtiene la categoría tradicional basada en edad
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
            $age >= 19 => PlayerCategory::Mayores,
            default => null
        };
    }

    /**
     * Valida si una jugadora es elegible para una categoría específica
     */
    public function validateEligibility(int $leagueId, int $age, string $gender, string $categoryName): bool
    {
        $league = League::find($leagueId);

        if (!$league) {
            return false;
        }

        // Validación dinámica si la liga tiene categorías configuradas
        if ($league->hasCustomCategories()) {
            return $this->validateDynamicEligibility($league, $age, $gender, $categoryName);
        }

        // Validación tradicional
        return $this->validateTraditionalEligibility($age, $gender, $categoryName);
    }

    /**
     * Valida elegibilidad usando configuración dinámica
     */
    protected function validateDynamicEligibility(League $league, int $age, string $gender, string $categoryName): bool
    {
        $category = $league->categories()
            ->where('name', $categoryName)
            ->active()
            ->first();

        if (!$category) {
            return false;
        }

        return $category->isEligibleForPlayer($age, $gender);
    }

    /**
     * Valida elegibilidad usando enum tradicional
     */
    protected function validateTraditionalEligibility(int $age, string $gender, string $categoryName): bool
    {
        $playerCategory = PlayerCategory::tryFrom($categoryName);

        if (!$playerCategory) {
            return false;
        }

        $ageRange = $playerCategory->getAgeRange();
        return $age >= $ageRange[0] && $age <= $ageRange[1];
    }

    /**
     * Obtiene todas las categorías disponibles para una jugadora
     */
    public function getAvailableCategories(int $leagueId, int $age, string $gender): array
    {
        $cacheKey = "available_categories_{$leagueId}_{$age}_{$gender}";

        return Cache::remember($cacheKey, 300, function () use ($leagueId, $age, $gender) {
            $league = League::find($leagueId);

            if (!$league) {
                return [];
            }

            if ($league->hasCustomCategories()) {
                return $this->getDynamicAvailableCategories($league, $age, $gender);
            }

            return $this->getTraditionalAvailableCategories($age, $gender);
        });
    }

    /**
     * Obtiene categorías dinámicas disponibles
     */
    protected function getDynamicAvailableCategories(League $league, int $age, string $gender): array
    {
        return $league->getAvailableCategoriesForPlayer($age, $gender)
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'code' => $category->code,
                    'description' => $category->description,
                    'age_range' => $category->age_range_label,
                    'gender' => $category->gender_label,
                    'color' => $category->color,
                    'icon' => $category->icon,
                ];
            })
            ->toArray();
    }

    /**
     * Obtiene categorías tradicionales disponibles
     */
    protected function getTraditionalAvailableCategories(int $age, string $gender): array
    {
        $categories = [];

        foreach (PlayerCategory::cases() as $category) {
            $ageRange = $category->getAgeRange();
            if ($age >= $ageRange[0] && $age <= $ageRange[1]) {
                $categories[] = [
                    'name' => $category->value,
                    'code' => $category->value,
                    'description' => $category->getLabel(),
                    'age_range' => $ageRange[0] . '-' . $ageRange[1] . ' años',
                    'gender' => 'Mixto',
                    'color' => $category->getColor(),
                    'icon' => $category->getIcon(),
                ];
            }
        }

        return $categories;
    }

    /**
     * Reasigna categorías masivamente para una liga
     */
    public function reassignCategoriesForLeague(int $leagueId): array
    {
        $league = League::find($leagueId);

        if (!$league) {
            return ['success' => false, 'message' => 'Liga no encontrada'];
        }

        $players = $league->players()->with('user')->get();
        $results = [
            'total' => $players->count(),
            'updated' => 0,
            'errors' => 0,
            'details' => []
        ];

        foreach ($players as $player) {
            try {
                $age = $player->user->age ?? 0;
                $gender = $player->user->gender ?? 'unknown';

                $newCategory = $this->assignAutomaticCategory($leagueId, $age, $gender);

                if ($newCategory && $newCategory !== $player->category) {
                    $oldCategory = $player->category;
                    $player->update(['category' => $newCategory]);

                    $results['updated']++;
                    $results['details'][] = [
                        'player_id' => $player->id,
                        'old_category' => $oldCategory,
                        'new_category' => $newCategory,
                        'age' => $age,
                        'gender' => $gender
                    ];

                    Log::info("Categoría reasignada", [
                        'player_id' => $player->id,
                        'old_category' => $oldCategory,
                        'new_category' => $newCategory
                    ]);
                }
            } catch (\Exception $e) {
                $results['errors']++;
                Log::error("Error reasignando categoría", [
                    'player_id' => $player->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Limpiar caché
        Cache::tags(['categories', "league_{$leagueId}"])->flush();

        return array_merge($results, ['success' => true]);
    }

    /**
     * Obtiene estadísticas de asignación de categorías
     */
    public function getCategoryAssignmentStats(int $leagueId): array
    {
        $league = League::find($leagueId);

        if (!$league) {
            return [];
        }

        if ($league->hasCustomCategories()) {
            return $this->getDynamicCategoryStats($league);
        }

        return $this->getTraditionalCategoryStats($league);
    }

    /**
     * Obtiene estadísticas de categorías dinámicas
     */
    protected function getDynamicCategoryStats(League $league): array
    {
        $stats = [];

        foreach ($league->getActiveCategories() as $category) {
            $playerStats = $category->getPlayerStats();
            $stats[$category->name] = [
                'total' => $playerStats['total'],
                'male' => $playerStats['male'],
                'female' => $playerStats['female'],
                'age_range' => $category->age_range_label,
                'gender_restriction' => $category->gender_label,
                'by_age' => $playerStats['by_age']
            ];
        }

        return $stats;
    }

    /**
     * Obtiene estadísticas de categorías tradicionales
     */
    protected function getTraditionalCategoryStats(League $league): array
    {
        return $league->getPlayersStatsByCategory();
    }
}
