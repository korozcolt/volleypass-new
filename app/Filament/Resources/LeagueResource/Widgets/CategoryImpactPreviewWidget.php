<?php

namespace App\Filament\Resources\LeagueResource\Widgets;

use App\Models\League;
use App\Models\Player;
use App\Enums\PlayerCategory;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class CategoryImpactPreviewWidget extends Widget
{
    protected static string $view = 'filament.widgets.category-impact-preview';

    public ?Model $record = null;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getViewData(): array
    {
        if (!$this->record instanceof League) {
            return [
                'impact' => $this->getEmptyImpact(),
                'hasData' => false,
                'league' => null,
                'hasCustomCategories' => false,
            ];
        }

        $league = $this->record;
        $impact = $this->calculateCategoryImpact($league);

        return [
            'league' => $league,
            'impact' => $impact,
            'hasCustomCategories' => $league->hasCustomCategories(),
            'hasData' => true,
            'criticalCount' => $impact['summary']['no_category'],
            'totalAffected' => count($impact['affected_players']),
            'needsAttention' => $impact['summary']['no_category'] > 0,
        ];
    }

    private function getEmptyImpact(): array
    {
        return [
            'total_players' => 0,
            'affected_players' => [],
            'category_changes' => [],
            'summary' => [
                'no_change' => 0,
                'category_change' => 0,
                'new_category' => 0,
                'no_category' => 0,
            ],
        ];
    }

    private function calculateCategoryImpact(League $league): array
    {
        $players = Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })->with(['currentClub', 'user'])->get();

        $impact = $this->getEmptyImpact();
        $impact['total_players'] = $players->count();

        if (!$league->hasCustomCategories()) {
            return $impact;
        }

        $customCategories = $league->getActiveCategories();

        foreach ($players as $player) {
            $currentAge = $player->age;
            if (!$currentAge) continue;

            $gender = $player->user->gender ?? 'female';

            // Categoría tradicional actual
            $traditionalCategory = PlayerCategory::getForAge($currentAge, $gender);

            // Buscar categoría personalizada que corresponde
            $customCategory = $customCategories->first(function ($category) use ($currentAge) {
                return $currentAge >= $category->min_age && $currentAge <= $category->max_age;
            });

            $changeType = $this->determineChangeType($traditionalCategory, $customCategory);
            $impact['summary'][$changeType]++;

            if ($changeType !== 'no_change') {
                $impact['affected_players'][] = [
                    'player' => $player,
                    'current_age' => $currentAge,
                    'traditional_category' => $traditionalCategory,
                    'custom_category' => $customCategory,
                    'change_type' => $changeType,
                    'change_description' => $this->getChangeDescription($traditionalCategory, $customCategory, $changeType),
                    'priority' => $this->getChangePriority($changeType),
                ];
            }

            // Agrupar cambios por categoría para estadísticas
            $this->groupCategoryChanges($impact, $customCategory, $traditionalCategory);
        }

        // Ordenar jugadoras afectadas por prioridad
        usort($impact['affected_players'], function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        return $impact;
    }

    private function determineChangeType($traditionalCategory, $customCategory): string
    {
        if (!$customCategory) {
            return 'no_category';
        }

        if ($customCategory->code !== $traditionalCategory->value) {
            return 'category_change';
        }

        return 'no_change';
    }

    private function getChangeDescription($traditionalCategory, $customCategory, $changeType): string
    {
        return match($changeType) {
            'no_category' => 'Sin categoría en sistema personalizado',
            'category_change' => "Cambia de {$traditionalCategory->getLabel()} a {$customCategory->name}",
            'new_category' => "Asignada a {$customCategory->name}",
            default => "Permanece en {$customCategory->name}"
        };
    }

    private function getChangePriority($changeType): int
    {
        return match($changeType) {
            'no_category' => 3, // Máxima prioridad
            'category_change' => 2,
            'new_category' => 1,
            default => 0
        };
    }

    private function groupCategoryChanges(&$impact, $customCategory, $traditionalCategory): void
    {
        $categoryKey = $customCategory ? $customCategory->name : 'Sin categoría';

        if (!isset($impact['category_changes'][$categoryKey])) {
            $impact['category_changes'][$categoryKey] = [
                'category' => $customCategory,
                'players_count' => 0,
                'from_traditional' => [],
            ];
        }

        $impact['category_changes'][$categoryKey]['players_count']++;

        $traditionalKey = $traditionalCategory->getLabel();
        $impact['category_changes'][$categoryKey]['from_traditional'][$traditionalKey] =
            ($impact['category_changes'][$categoryKey]['from_traditional'][$traditionalKey] ?? 0) + 1;
    }

    public static function canView(): bool
    {
        return true;
    }
}
