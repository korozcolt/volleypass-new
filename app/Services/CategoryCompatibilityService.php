<?php

namespace App\Services;

use App\Enums\PlayerCategory;
use App\Models\League;
use App\Models\Player;
use App\Models\User;
use App\Services\CategoryNotificationService;

/**
 * Servicio de compatibilidad para mantener la API existente del sistema de categorías
 * mientras se integra el nuevo sistema dinámico
 */
class CategoryCompatibilityService
{
    /**
     * Obtiene la categoría para un jugador usando el sistema dinámico si está disponible,
     * o fallback al sistema tradicional
     */
    public function getCategoryForPlayer(Player $player): ?PlayerCategory
    {
        $league = $player->current_club?->league;
        $age = $player->user?->age ?? 0;
        $gender = $player->user?->gender ?? 'mixed';

        return $this->getCategoryForAge($age, $gender, $league);
    }

    /**
     * Obtiene la categoría para una edad y género específicos
     * Wrapper method que mantiene compatibilidad con código existente
     */
    public function getCategoryForAge(int $age, string $gender, ?League $league = null): ?PlayerCategory
    {
        // Intentar usar sistema dinámico primero
        if ($league && $league->hasCustomCategories()) {
            $dynamicCategory = PlayerCategory::getForAge($age, $gender, $league);
            if ($dynamicCategory !== null) {
                return $dynamicCategory;
            }

            // Si no hay mapeo directo al enum, usar fallback tradicional
            return PlayerCategory::getTraditionalCategoryForAge($age);
        }

        // Fallback al sistema tradicional
        return PlayerCategory::getTraditionalCategoryForAge($age);
    }

    /**
     * Verifica si una edad es elegible para una categoría específica
     * Mantiene compatibilidad con validaciones existentes
     */
    public function isAgeEligibleForCategory(int $age, PlayerCategory $category, ?League $league = null): bool
    {
        return $category->isAgeEligible($age, $league);
    }

    /**
     * Obtiene el rango de edad para una categoría
     * Wrapper method para mantener compatibilidad
     */
    public function getAgeRangeForCategory(PlayerCategory $category, ?League $league = null): array
    {
        return $category->getAgeRange($league);
    }

    /**
     * Obtiene el texto del rango de edad para una categoría
     * Wrapper method para mantener compatibilidad
     */
    public function getAgeRangeTextForCategory(PlayerCategory $category, ?League $league = null): string
    {
        return $category->getAgeRangeText($league);
    }

    /**
     * Obtiene todas las categorías disponibles para una liga
     * Wrapper method que mantiene la estructura esperada por el código existente
     */
    public function getAvailableCategories(?League $league = null): array
    {
        return PlayerCategory::getAvailableCategories($league);
    }

    /**
     * Obtiene las categorías como opciones para formularios
     * Mantiene compatibilidad con Filament y otros formularios
     */
    public function getCategoryOptions(?League $league = null): array
    {
        $categories = $this->getAvailableCategories($league);
        $options = [];

        foreach ($categories as $category) {
            $options[$category['value']] = $category['label'];
        }

        return $options;
    }

    /**
     * Valida si una categoría es válida para una liga específica
     */
    public function isCategoryValidForLeague(string $categoryValue, ?League $league = null): bool
    {
        if (!$league || !$league->hasCustomCategories()) {
            // Validar contra enum tradicional
            foreach (PlayerCategory::cases() as $case) {
                if ($case->value === $categoryValue) {
                    return true;
                }
            }
            return false;
        }

        // Validar contra categorías dinámicas de la liga
        return $league->categories()
            ->active()
            ->where('code', strtoupper($categoryValue))
            ->exists();
    }

    /**
     * Obtiene la instancia del enum PlayerCategory desde un string
     * Mantiene compatibilidad con código que espera el enum
     */
    public function getCategoryEnum(string $categoryValue): ?PlayerCategory
    {
        foreach (PlayerCategory::cases() as $case) {
            if ($case->value === $categoryValue) {
                return $case;
            }
        }
        return null;
    }

    /**
     * Migra automáticamente las categorías de jugadores cuando cambia la configuración de liga
     * 
     * @param League $league La liga cuya configuración ha cambiado
     * @param User|null $changedBy El usuario que realizó el cambio en la configuración (opcional)
     * @param bool $notifyChanges Si se deben enviar notificaciones de los cambios
     * @return array Resultados de la migración
     */
    public function migratePlayersCategories(League $league, ?User $changedBy = null, bool $notifyChanges = true): array
    {
        $results = [
            'migrated' => 0,
            'errors' => 0,
            'unchanged' => 0,
            'details' => []
        ];

        $players = $league->clubs()
            ->with(['players.user'])
            ->get()
            ->pluck('players')
            ->flatten();
            
        $playerChanges = [];

        foreach ($players as $player) {
            try {
                $currentCategory = $player->category;
                $newCategory = $this->getCategoryForPlayer($player);

                if ($newCategory && $newCategory->value !== $currentCategory) {
                    $player->update(['category' => $newCategory->value]);
                    $results['migrated']++;
                    $results['details'][] = [
                        'player_id' => $player->id,
                        'old_category' => $currentCategory,
                        'new_category' => $newCategory->value,
                        'action' => 'migrated'
                    ];
                    
                    // Preparar datos para notificación
                    if ($notifyChanges) {
                        $playerChanges[] = [
                            'player' => $player,
                            'old_category' => $currentCategory,
                            'new_category' => $newCategory->value,
                            'reason' => 'Cambio en la configuración de categorías de la liga'
                        ];
                    }
                } else {
                    $results['unchanged']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
                $results['details'][] = [
                    'player_id' => $player->id,
                    'error' => $e->getMessage(),
                    'action' => 'error'
                ];
            }
        }
        
        // Enviar notificaciones si hay cambios y se solicita
        if ($notifyChanges && !empty($playerChanges)) {
            app(CategoryNotificationService::class)->notifyBulkPlayerCategoryReassignments($playerChanges);
        }

        return $results;
    }

    /**
     * Obtiene estadísticas de categorías para una liga
     * Mantiene compatibilidad con reportes existentes
     */
    public function getCategoryStats(?League $league = null): array
    {
        if ($league && $league->hasCustomCategories()) {
            return $league->getCategoryStats();
        }

        // Fallback a estadísticas tradicionales
        $stats = [];
        foreach (PlayerCategory::cases() as $category) {
            $stats[$category->getLabel()] = 0; // Placeholder - se llenaría con datos reales
        }

        return $stats;
    }

    /**
     * Verifica si el sistema dinámico está activo para una liga
     */
    public function isDynamicSystemActive(?League $league = null): bool
    {
        return $league && $league->hasCustomCategories();
    }

    /**
     * Obtiene información de compatibilidad para debugging
     */
    public function getCompatibilityInfo(?League $league = null): array
    {
        return [
            'league_id' => $league?->id,
            'has_custom_categories' => $this->isDynamicSystemActive($league),
            'available_categories_count' => count($this->getAvailableCategories($league)),
            'system_mode' => $this->isDynamicSystemActive($league) ? 'dynamic' : 'traditional',
            'enum_categories_count' => count(PlayerCategory::cases()),
        ];
    }
}
