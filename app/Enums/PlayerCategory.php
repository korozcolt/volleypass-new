<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
use App\Models\League;
use App\Models\LeagueCategory;

enum PlayerCategory: string implements HasLabel, HasColor, HasIcon {
    case Mini = 'mini';
    case Pre_Mini = 'pre_mini';
    case Infantil = 'infantil';
    case Cadete = 'cadete';
    case Juvenil = 'juvenil';
    case Mayores = 'mayores';
    case Masters = 'masters';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Mini => 'Mini (8-10 años)',
            self::Pre_Mini => 'Pre-Mini (11-12 años)',
            self::Infantil => 'Infantil (13-14 años)',
            self::Cadete => 'Cadete (15-16 años)',
            self::Juvenil => 'Juvenil (17-18 años)',
            self::Mayores => 'Mayores (19+ años)',
            self::Masters => 'Masters (35+ años)',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Mini => 'pink',
            self::Pre_Mini => 'purple',
            self::Infantil => 'info',
            self::Cadete => 'warning',
            self::Juvenil => 'success',
            self::Mayores => 'primary',
            self::Masters => 'gray',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::Mini => 'heroicon-o-heart',
            self::Pre_Mini => 'heroicon-o-star',
            self::Infantil => 'heroicon-o-sparkles',
            self::Cadete => 'heroicon-o-fire',
            self::Juvenil => 'heroicon-o-bolt',
            self::Mayores => 'heroicon-o-trophy',
            self::Masters => 'heroicon-o-academic-cap',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::Mini => 'bg-pink-100 text-pink-800',
            self::Pre_Mini => 'bg-purple-100 text-purple-800',
            self::Infantil => 'bg-blue-100 text-blue-800',
            self::Cadete => 'bg-yellow-100 text-yellow-800',
            self::Juvenil => 'bg-green-100 text-green-800',
            self::Mayores => 'bg-indigo-100 text-indigo-800',
            self::Masters => 'bg-gray-100 text-gray-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function getAgeRange(?League $league = null): array
    {
        // Si hay una liga y tiene configuración personalizada, usar esa configuración
        if ($league && $league->hasCustomCategories()) {
            $category = $league->categories()
                ->active()
                ->where('code', strtoupper($this->value))
                ->first();

            if ($category) {
                return [$category->min_age, $category->max_age];
            }
        }

        // Fallback a rangos tradicionales
        return $this->getDefaultAgeRange();
    }

    /**
     * Obtiene los rangos de edad por defecto (tradicionales)
     */
    public function getDefaultAgeRange(): array
    {
        return match ($this) {
            self::Mini => [8, 10],
            self::Pre_Mini => [11, 12],
            self::Infantil => [13, 14],
            self::Cadete => [15, 16],
            self::Juvenil => [17, 18],
            self::Mayores => [19, 34],
            self::Masters => [35, 100],
        };
    }

    /**
     * Obtiene la categoría apropiada para una edad y género específicos
     * Considera la configuración dinámica de la liga si está disponible
     */
    public static function getForAge(int $age, string $gender, ?League $league = null): ?self
    {
        // Si hay una liga y tiene configuración personalizada, usar esa configuración
        if ($league && $league->hasCustomCategories()) {
            $category = LeagueCategory::findBestCategoryForPlayer($league->id, $age, $gender);
            if ($category) {
                // Intentar mapear el código de la categoría dinámica al enum
                $enumValue = strtolower($category->code);
                foreach (self::cases() as $case) {
                    if ($case->value === $enumValue) {
                        return $case;
                    }
                }
                // Si no hay mapeo directo, devolver null para indicar que se debe usar la categoría dinámica
                return null;
            }
        }

        // Fallback a lógica tradicional
        return self::getTraditionalCategoryForAge($age);
    }

    /**
     * Lógica tradicional de asignación de categorías por edad
     */
    public static function getTraditionalCategoryForAge(int $age): self
    {
        return match (true) {
            $age >= 8 && $age <= 10 => self::Mini,
            $age >= 11 && $age <= 12 => self::Pre_Mini,
            $age >= 13 && $age <= 14 => self::Infantil,
            $age >= 15 && $age <= 16 => self::Cadete,
            $age >= 17 && $age <= 18 => self::Juvenil,
            $age >= 35 => self::Masters,
            default => self::Mayores,
        };
    }

    /**
     * Verifica si una edad es elegible para esta categoría
     * Considera la configuración dinámica de la liga si está disponible
     */
    public function isAgeEligible(int $age, ?League $league = null): bool
    {
        [$minAge, $maxAge] = $this->getAgeRange($league);
        return $age >= $minAge && $age <= $maxAge;
    }

    /**
     * Obtiene el texto del rango de edad
     * Considera la configuración dinámica de la liga si está disponible
     */
    public function getAgeRangeText(?League $league = null): string
    {
        [$minAge, $maxAge] = $this->getAgeRange($league);
        return "{$minAge}-{$maxAge} años";
    }

    /**
     * Obtiene el label dinámico considerando la configuración de la liga
     */
    public function getDynamicLabel(?League $league = null): string
    {
        if ($league && $league->hasCustomCategories()) {
            $category = $league->categories()
                ->active()
                ->where('code', strtoupper($this->value))
                ->first();

            if ($category) {
                return "{$category->name} ({$category->getAgeRangeText()})";
            }
        }

        // Fallback al label tradicional
        return $this->getLabel();
    }

    /**
     * Verifica si la liga tiene configuración personalizada para esta categoría
     */
    public function hasCustomConfiguration(?League $league = null): bool
    {
        if (!$league || !$league->hasCustomCategories()) {
            return false;
        }

        return $league->categories()
            ->active()
            ->where('code', strtoupper($this->value))
            ->exists();
    }

    /**
     * Obtiene todas las categorías disponibles para una liga
     * Combina enum tradicional con configuración dinámica
     */
    public static function getAvailableCategories(?League $league = null): array
    {
        $categories = [];

        if ($league && $league->hasCustomCategories()) {
            // Usar categorías dinámicas de la liga
            $leagueCategories = $league->getActiveCategories();
            foreach ($leagueCategories as $category) {
                $categories[] = [
                    'value' => $category->code,
                    'label' => $category->full_name,
                    'age_range' => [$category->min_age, $category->max_age],
                    'gender' => $category->gender,
                    'is_dynamic' => true,
                ];
            }
        } else {
            // Usar categorías tradicionales del enum
            foreach (self::cases() as $case) {
                $categories[] = [
                    'value' => $case->value,
                    'label' => $case->getLabel(),
                    'age_range' => $case->getDefaultAgeRange(),
                    'gender' => 'mixed',
                    'is_dynamic' => false,
                ];
            }
        }

        return $categories;
    }
}
