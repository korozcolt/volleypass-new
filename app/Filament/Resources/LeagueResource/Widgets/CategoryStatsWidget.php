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

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        if (!$this->record instanceof League) {
            return $this->getEmptyStats();
        }

        $league = $this->record;
        $hasCustomCategories = $league->hasCustomCategories();

        if (!$hasCustomCategories) {
            return $this->getTraditionalStats($league);
        }

        return $this->getCustomCategoryStats($league);
    }

    private function getEmptyStats(): array
    {
        return [
            Stat::make('Total Jugadoras', 0)
                ->description('Guarda la liga para ver estadísticas')
                ->descriptionIcon('heroicon-m-information-circle')
                ->color('gray'),
        ];
    }

    private function getTraditionalStats(League $league): array
    {
        $totalPlayers = $this->getTotalPlayersCount($league);

        $stats = [
            Stat::make('Total Jugadoras', $totalPlayers)
                ->description('Sistema tradicional activo')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Sistema', 'Tradicional')
                ->description('Configure categorías personalizadas')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('gray'),
        ];

        // Mostrar distribución tradicional resumida
        $distribution = $this->getTraditionalDistribution($league);
        $topCategories = array_slice($distribution, 0, 3, true);

        foreach ($topCategories as $categoryName => $count) {
            if ($count > 0) {
                $stats[] = Stat::make($categoryName, $count)
                    ->description('Jugadoras en categoría')
                    ->descriptionIcon('heroicon-m-user-group')
                    ->color('info');
            }
        }

        return $stats;
    }

    private function getCustomCategoryStats(League $league): array
    {
        $totalPlayers = $this->getTotalPlayersCount($league);
        $impact = $this->calculateQuickImpact($league);

        $stats = [
            // Estadística principal
            Stat::make('Total Jugadoras', $totalPlayers)
                ->description('En todos los clubes')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            // Estado del sistema
            Stat::make('Sistema', 'Personalizado')
                ->description('Categorías configuradas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            // Impacto crítico
            Stat::make('Sin Categoría', $impact['no_category'])
                ->description($impact['no_category'] > 0 ? 'Requieren atención' : 'Todas asignadas')
                ->descriptionIcon($impact['no_category'] > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($impact['no_category'] > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'class' => $impact['no_category'] > 0 ? 'animate-pulse' : '',
                ]),

            // Cambios de categoría
            Stat::make('Cambios', $impact['category_change'])
                ->description($impact['category_change'] > 0 ? 'Jugadoras afectadas' : 'Sin cambios')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($impact['category_change'] > 0 ? 'warning' : 'success'),
        ];

        // Estadísticas por categoría personalizada
        $categories = $league->getActiveCategories()->take(3);
        foreach ($categories as $category) {
            $count = $this->getPlayersInCustomCategory($league, $category);

            $stats[] = Stat::make($category->name, $count)
                ->description("Edad: {$category->min_age}-{$category->max_age} años")
                ->descriptionIcon('heroicon-m-user-group')
                ->color($this->getCategoryColor($category->sort_order ?? 0))
                ->chart($this->generateTrendChart($count)) // Gráfico simple
                ->chartColor($this->getCategoryColor($category->sort_order ?? 0));
        }

        return $stats;
    }

    private function getTotalPlayersCount(League $league): int
    {
        return Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })->count();
    }

    private function calculateQuickImpact(League $league): array
    {
        $players = Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })->with('user')->get();

        $impact = [
            'no_category' => 0,
            'category_change' => 0,
            'no_change' => 0,
        ];

        $customCategories = $league->getActiveCategories();

        foreach ($players as $player) {
            $age = $player->age;
            if (!$age) continue;

            $customCategory = $customCategories->first(function ($category) use ($age) {
                return $age >= $category->min_age && $age <= $category->max_age;
            });

            if (!$customCategory) {
                $impact['no_category']++;
            } else {
                $traditionalCategory = PlayerCategory::getForAge($age, $player->user->gender ?? 'female');
                if ($customCategory->code !== $traditionalCategory->value) {
                    $impact['category_change']++;
                } else {
                    $impact['no_change']++;
                }
            }
        }

        return $impact;
    }

    private function getTraditionalDistribution(League $league): array
    {
        $players = Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })->with('user')->get();

        $distribution = [];
        foreach (PlayerCategory::cases() as $category) {
            $distribution[$category->getLabel()] = 0;
        }

        foreach ($players as $player) {
            $age = $player->age;
            if (!$age) continue;

            $category = PlayerCategory::getForAge($age, $player->user->gender ?? 'female');
            $distribution[$category->getLabel()]++;
        }

        return array_filter($distribution);
    }

    private function getPlayersInCustomCategory(League $league, $category): int
    {
        return Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })
        ->whereHas('user', function ($query) {
            $query->whereNotNull('birth_date');
        })
        ->get()
        ->filter(function ($player) use ($category) {
            $age = $player->age;
            return $age && $age >= $category->min_age && $age <= $category->max_age;
        })
        ->count();
    }

    private function getCategoryColor(int $index): string
    {
        $colors = ['primary', 'success', 'warning', 'info', 'danger', 'gray'];
        return $colors[$index % count($colors)];
    }

    private function generateTrendChart(int $count): array
    {
        // Generar un gráfico simple para visualización
        if ($count === 0) return [0, 0, 0, 0, 0];

        $base = max(1, $count - 2);
        return [
            $base,
            $base + rand(0, 1),
            $base + rand(0, 2),
            $base + rand(0, 1),
            $count
        ];
    }

    protected function getColumns(): int
    {
        return 2; // Layout más compacto
    }

    public static function canView(): bool
    {
        return true;
    }
}
