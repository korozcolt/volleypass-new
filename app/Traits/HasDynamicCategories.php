<?php

namespace App\Traits;

use App\Enums\PlayerCategory;
use App\Models\League;
use App\Services\CategoryCompatibilityService;

/**
 * Trait para agregar funcionalidad de categorías dinámicas a modelos existentes
 * Facilita la migración del código legacy al nuevo sistema
 */
trait HasDynamicCategories
{
    /**
     * Instancia del servicio de compatibilidad
     */
    protected ?CategoryCompatibilityService $categoryCompatibilityService = null;

    /**
     * Obtiene el servicio de compatibilidad
     */
    protected function getCategoryCompatibilityService(): CategoryCompatibilityService
    {
        if (!$this->categoryCompatibilityService) {
            $this->categoryCompatibilityService = app(CategoryCompatibilityService::class);
        }

        return $this->categoryCompatibilityService;
    }

    /**
     * Obtiene la categoría apropiada para una edad y género
     * Método helper que mantiene compatibilidad con código existente
     */
    public function getCategoryForAge(int $age, string $gender, ?League $league = null): ?PlayerCategory
    {
        return $this->getCategoryCompatibilityService()->getCategoryForAge($age, $gender, $league);
    }

    /**
     * Verifica si una edad es elegible para una categoría
     * Método helper que considera configuración dinámica
     */
    public function isAgeEligibleForCategory(int $age, PlayerCategory $category, ?League $league = null): bool
    {
        return $this->getCategoryCompatibilityService()->isAgeEligibleForCategory($age, $category, $league);
    }

    /**
     * Obtiene el rango de edad para una categoría
     * Método helper que considera configuración dinámica
     */
    public function getAgeRangeForCategory(PlayerCategory $category, ?League $league = null): array
    {
        return $this->getCategoryCompatibilityService()->getAgeRangeForCategory($category, $league);
    }

    /**
     * Obtiene las categorías disponibles como opciones para formularios
     * Método helper para Filament y otros formularios
     */
    public function getCategoryOptions(?League $league = null): array
    {
        return $this->getCategoryCompatibilityService()->getCategoryOptions($league);
    }

    /**
     * Verifica si una categoría es válida para una liga
     * Método helper para validaciones
     */
    public function isCategoryValidForLeague(string $categoryValue, ?League $league = null): bool
    {
        return $this->getCategoryCompatibilityService()->isCategoryValidForLeague($categoryValue, $league);
    }

    /**
     * Obtiene información sobre el sistema de categorías activo
     * Útil para debugging y logging
     */
    public function getCategorySystemInfo(?League $league = null): array
    {
        return $this->getCategoryCompatibilityService()->getCompatibilityInfo($league);
    }

    /**
     * Verifica si el sistema dinámico está activo
     * Método helper para condicionales en el código
     */
    public function isDynamicCategorySystemActive(?League $league = null): bool
    {
        return $this->getCategoryCompatibilityService()->isDynamicSystemActive($league);
    }
}
