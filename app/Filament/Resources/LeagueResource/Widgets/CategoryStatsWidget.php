<?php

namespace App\Filament\Resources\LeagueResource\Widgets;

use App\Models\League;
use App\Models\Player;
use App\Enums\PlayerCategory;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class CategoryStatsWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (!$this->record instanceof League) {
            return [];
        }

        $league = $this->record;
        $stats = [];

        // Estadísticas generales
        $totalPlayers = $league->clubs()->withCount('players')->get()->sum('players_count');
        $totalCategories = $league->categories()->active()->count();
        $hasCustomCategories = $league->hasCustomCategories();

        $stats[] = Stat::make('Total Jugadoras', $totalPlayers)
            ->description('En todos los clubes de la liga')
            ->descriptionIcon('heroicon-m-users')
            ->color('primary');

        $stats[] = Stat::make('Categorías Configuradas', $totalCategories)
            ->description($hasCustomCategories ? 'Sistema personalizado' : 'Sistema tradicional')
            ->descriptionIcon($hasCustomCategories ? 'heroicon-m-cog-6-tooth' : 'heroicon-m-list-bullet')
            ->color($hasCustomCategories ? 'success' : 'gray');

        // Estadísticas por categoría
        if ($hasCustomCategories) {
            $categories = $league->getActiveCategories();
            
            foreach ($categories as $category) {
                $playersInCategory = $this->getPlayersInCategoryCount($league, $category);
                $potentialImpact = $this->calculatePotentialImpact($league, $category);
                
                $stats[] = Stat::make($category->name, $playersInCategory)
                    ->description("Rango: {$category->min_age}-{$category->max_age} años")
                    ->descriptionIcon('heroicon-m-user-group')
                    ->color($this->getCategoryColor($category))
                    ->extraAttributes([
                        'class' => 'category-stat',
                        'data-category-id' => $category->id,
                        'data-potential-impact' => $potentialImpact,
                    ]);
            }
        } else {
            // Mostrar estadísticas tradicionales
            foreach (PlayerCategory::cases() as $category) {
                $playersInCategory = $this->getPlayersInTraditionalCategory($league, $category);
                $ageRange = $category->getAgeRange();
                
                $stats[] = Stat::make($category->getLabel(), $playersInCategory)
                    ->description("Rango: {$ageRange[0]}-{$ageRange[1]} años")
                    ->descriptionIcon('heroicon-m-user-group')
                    ->color('gray');
            }
        }

        return $stats;
    }

    private function getPlayersInCategoryCount(League $league, $category): int
    {
        return Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })
        ->whereHas('user', function ($query) use ($category) {
            $query->whereNotNull('birth_date');
        })
        ->get()
        ->filter(function ($player) use ($category) {
            $age = $player->user->age;
            return $age !== null && $age >= $category->min_age && $age <= $category->max_age;
        })
        ->count();
    }

    private function getPlayersInTraditionalCategory(League $league, PlayerCategory $category): int
    {
        $ageRange = $category->getAgeRange();
        
        return Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })
        ->whereHas('user', function ($query) {
            $query->whereNotNull('birth_date');
        })
        ->get()
        ->filter(function ($player) use ($ageRange) {
            $age = $player->user->age;
            return $age !== null && $age >= $ageRange[0] && $age <= $ageRange[1];
        })
        ->count();
    }

    private function calculatePotentialImpact(League $league, $category): int
    {
        // Calcular cuántas jugadoras cambiarían de categoría si se aplica esta configuración
        $currentPlayers = Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })->get();

        $impactCount = 0;
        foreach ($currentPlayers as $player) {
            $currentAge = $player->age;
            $currentTraditionalCategory = PlayerCategory::getForAge($currentAge, $player->user->gender ?? 'female');
            
            // Verificar si la jugadora estaría en esta categoría con el nuevo sistema
            $wouldBeInNewCategory = ($currentAge >= $category->min_age && $currentAge <= $category->max_age);
            $isInTraditionalEquivalent = ($currentTraditionalCategory->value === $category->code);
            
            if ($wouldBeInNewCategory !== $isInTraditionalEquivalent) {
                $impactCount++;
            }
        }

        return $impactCount;
    }

    private function getCategoryColor($category): string
    {
        $colors = ['primary', 'success', 'warning', 'info', 'danger'];
        return $colors[$category->id % count($colors)];
    }

    protected function getColumns(): int
    {
        return 3;
    }

    public static function canView(): bool
    {
        return true;
    }
}