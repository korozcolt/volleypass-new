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

    public function getViewData(): array
    {
        if (!$this->record instanceof League) {
            return ['impact' => []];
        }

        $league = $this->record;
        $impact = $this->calculateCategoryImpact($league);

        return [
            'league' => $league,
            'impact' => $impact,
            'hasCustomCategories' => $league->hasCustomCategories(),
        ];
    }

    private function calculateCategoryImpact(League $league): array
    {
        $players = Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })->with('currentClub', 'user')->get();

        $impact = [
            'total_players' => $players->count(),
            'affected_players' => [],
            'category_changes' => [],
            'summary' => [
                'no_change' => 0,
                'category_change' => 0,
                'new_category' => 0,
                'no_category' => 0,
            ],
        ];

        if (!$league->hasCustomCategories()) {
            return $impact;
        }

        $customCategories = $league->getActiveCategories();

        foreach ($players as $player) {
            $currentAge = $player->age;
            $gender = $player->user->gender ?? 'female';
            
            // Categoría tradicional actual
            $traditionalCategory = PlayerCategory::getForAge($currentAge, $gender);
            
            // Buscar categoría personalizada que corresponde
            $customCategory = $customCategories->first(function ($category) use ($currentAge) {
                return $currentAge >= $category->min_age && $currentAge <= $category->max_age;
            });

            $changeType = 'no_change';
            $changeDescription = '';

            if (!$customCategory) {
                $changeType = 'no_category';
                $changeDescription = 'No tiene categoría en el sistema personalizado';
            } elseif ($customCategory->code !== $traditionalCategory->value) {
                $changeType = 'category_change';
                $changeDescription = "Cambia de {$traditionalCategory->getLabel()} a {$customCategory->name}";
            } elseif ($customCategory->code === $traditionalCategory->value) {
                $changeType = 'no_change';
                $changeDescription = "Permanece en {$customCategory->name}";
            }

            $impact['summary'][$changeType]++;

            if ($changeType !== 'no_change') {
                $impact['affected_players'][] = [
                    'player' => $player,
                    'current_age' => $currentAge,
                    'traditional_category' => $traditionalCategory,
                    'custom_category' => $customCategory,
                    'change_type' => $changeType,
                    'change_description' => $changeDescription,
                ];
            }

            // Agrupar cambios por categoría
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
            if (!isset($impact['category_changes'][$categoryKey]['from_traditional'][$traditionalKey])) {
                $impact['category_changes'][$categoryKey]['from_traditional'][$traditionalKey] = 0;
            }
            $impact['category_changes'][$categoryKey]['from_traditional'][$traditionalKey]++;
        }

        return $impact;
    }

    public static function canView(): bool
    {
        return true;
    }
}