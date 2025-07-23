<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\CategoryCompatibilityService;

/**
 * Facade para el servicio de compatibilidad de categorías
 * Facilita el acceso desde cualquier parte del código
 *
 * @method static \App\Enums\PlayerCategory|null getCategoryForAge(int $age, string $gender, ?\App\Models\League $league = null)
 * @method static \App\Enums\PlayerCategory|null getCategoryForPlayer(\App\Models\Player $player)
 * @method static bool isAgeEligibleForCategory(int $age, \App\Enums\PlayerCategory $category, ?\App\Models\League $league = null)
 * @method static array getAgeRangeForCategory(\App\Enums\PlayerCategory $category, ?\App\Models\League $league = null)
 * @method static string getAgeRangeTextForCategory(\App\Enums\PlayerCategory $category, ?\App\Models\League $league = null)
 * @method static array getAvailableCategories(?\App\Models\League $league = null)
 * @method static array getCategoryOptions(?\App\Models\League $league = null)
 * @method static bool isCategoryValidForLeague(string $categoryValue, ?\App\Models\League $league = null)
 * @method static \App\Enums\PlayerCategory|null getCategoryEnum(string $categoryValue)
 * @method static array migratePlayersCategories(\App\Models\League $league)
 * @method static array getCategoryStats(?\App\Models\League $league = null)
 * @method static bool isDynamicSystemActive(?\App\Models\League $league = null)
 * @method static array getCompatibilityInfo(?\App\Models\League $league = null)
 */
class CategoryCompatibility extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return CategoryCompatibilityService::class;
    }
}
