<?php

namespace App\Filament\Widgets;

use App\Models\Player;
use App\Models\LeagueCategory;
use App\Models\MedicalCertificate;
use App\Models\League;
use App\Models\Club;
use App\Enums\PlayerCategory;
use App\Enums\MedicalStatus;
use App\Enums\FederationStatus;
use App\Enums\Gender;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoryDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución por Categorías y Estados';
    
    protected static ?int $sort = 2;
    
    protected static ?string $pollingInterval = '60s';
    
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = 'categories';
    
    public string $chartType = 'categories';
    public array $insights = [];
    
    protected function getFilters(): ?array
    {
        return [
            'categories' => 'Distribución por Categorías',
            'medical' => 'Estados Médicos',
            'federation_trend' => 'Evolución Mensual Federaciones',
            'league_performance' => 'Performance por Liga',
        ];
    }

    protected function getData(): array
    {
        return match ($this->filter) {
            'categories' => $this->getCategoryDistributionData(),
            'medical' => $this->getMedicalStatusData(),
            'federation_trend' => $this->getFederationTrendData(),
            'league_performance' => $this->getLeaguePerformanceData(),
            default => $this->getCategoryDistributionData(),
        };
    }

    protected function getType(): string
    {
        return match ($this->filter) {
            'categories' => 'doughnut',
            'medical' => 'pie',
            'federation_trend' => 'line',
            'league_performance' => 'bar',
            default => 'doughnut',
        };
    }

    protected function getCategoryDistributionData(): array
    {
        $cacheKey = 'chart.category_distribution';
        
        $chartData = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            $categories = [];
            $leagueCategories = LeagueCategory::with('players')->get();
            
            foreach ($leagueCategories as $category) {
                $name = $category->gender . ' - ' . $category->name;
                $count = $category->players->count();
                
                if ($count > 0) {
                    $categories[$name] = $count;
                }
            }
            
            return [
                'labels' => array_keys($categories),
                'data' => array_values($categories),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jugadoras por Categoría',
                    'data' => $chartData['data'],
                    'backgroundColor' => [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF',
                        '#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56',
                    ],
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $chartData['labels'],
        ];
    }

    protected function getMedicalStatusData(): array
    {
        $cacheKey = 'chart.medical_status';
        
        $chartData = Cache::remember($cacheKey, now()->addMinutes(20), function () {
            $statuses = MedicalCertificate::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            
            $labels = [];
            $counts = [];
            
            foreach (MedicalStatus::cases() as $status) {
                $labels[] = $status->getLabel();
                $counts[] = $statuses[$status->value] ?? 0;
            }
            
            return [
                'labels' => $labels,
                'data' => $counts,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Certificados Médicos',
                    'data' => $chartData['data'],
                    'backgroundColor' => [
                        '#10B981', // Valid - Green
                        '#F59E0B', // Pending - Yellow
                        '#EF4444', // Expired - Red
                        '#6B7280', // Rejected - Gray
                        '#8B5CF6', // Under Review - Purple
                    ],
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $chartData['labels'],
        ];
    }

    protected function getFederationTrendData(): array
    {
        $cacheKey = 'chart.federation_trend';
        
        $chartData = Cache::remember($cacheKey, now()->addHours(1), function () {
            $months = [];
            $federatedCounts = [];
            $totalCounts = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months[] = $date->format('M Y');
                
                $federatedCount = Player::where('federation_status', FederationStatus::Federated)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                
                $totalCount = Player::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                
                $federatedCounts[] = $federatedCount;
                $totalCounts[] = $totalCount;
            }
            
            return [
                'labels' => $months,
                'federated' => $federatedCounts,
                'total' => $totalCounts,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jugadoras Federadas',
                    'data' => $chartData['federated'],
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Total Registradas',
                    'data' => $chartData['total'],
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $chartData['labels'],
        ];
    }

    protected function getLeaguePerformanceData(): array
    {
        $cacheKey = 'chart.league_performance';
        
        $chartData = Cache::remember($cacheKey, now()->addMinutes(45), function () {
            $leagues = DB::table('leagues')
                ->leftJoin('clubs', 'leagues.id', '=', 'clubs.league_id')
                ->leftJoin('players', 'clubs.id', '=', 'players.current_club_id')
                ->select(
                    'leagues.name as league_name',
                    DB::raw('COUNT(DISTINCT clubs.id) as clubs_count'),
                    DB::raw('COUNT(DISTINCT players.id) as players_count'),
                    DB::raw('COUNT(DISTINCT CASE WHEN players.federation_status = "federated" THEN players.id END) as federated_count')
                )
                ->groupBy('leagues.id', 'leagues.name')
                ->having('clubs_count', '>', 0)
                ->get()
                ->toArray();
            
            $labels = array_column($leagues, 'league_name');
            $clubsCounts = array_column($leagues, 'clubs_count');
            $playersCounts = array_column($leagues, 'players_count');
            $federatedCounts = array_column($leagues, 'federated_count');
            
            return [
                'labels' => $labels,
                'clubs' => $clubsCounts,
                'players' => $playersCounts,
                'federated' => $federatedCounts,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Clubes Activos',
                    'data' => $chartData['clubs'],
                    'backgroundColor' => '#8B5CF6',
                    'borderColor' => '#7C3AED',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Total Jugadoras',
                    'data' => $chartData['players'],
                    'backgroundColor' => '#3B82F6',
                    'borderColor' => '#2563EB',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Jugadoras Federadas',
                    'data' => $chartData['federated'],
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#059669',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $chartData['labels'],
        ];
    }

    protected function getOptions(): array
    {
        $baseOptions = [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];

        return match ($this->filter) {
            'federation_trend' => array_merge($baseOptions, [
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => [
                            'display' => true,
                        ],
                    ],
                    'x' => [
                        'grid' => [
                            'display' => false,
                        ],
                    ],
                ],
                'elements' => [
                    'point' => [
                        'radius' => 4,
                        'hoverRadius' => 6,
                    ],
                ],
            ]),
            'league_performance' => array_merge($baseOptions, [
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => [
                            'display' => true,
                        ],
                    ],
                    'x' => [
                        'grid' => [
                            'display' => false,
                        ],
                    ],
                ],
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                    ],
                ],
            ]),
            default => $baseOptions,
        };
    }

    public static function canView(): bool
    {
        return true; // Visible para todos los usuarios autenticados
    }
    
    public function setChartType(string $type): void
    {
        $this->chartType = $type;
        $this->refreshChart();
    }
    
    public function refreshChart(): void
    {
        // Clear cache for current chart type
        Cache::forget("chart.{$this->chartType}_distribution");
        $this->insights = $this->getInsights();
    }
    
    public function getChartTitle(): string
    {
        return match($this->chartType) {
            'categories' => 'Distribución por Categorías',
            'medical' => 'Estados Médicos',
            'federation' => 'Tendencia de Federación (6 meses)',
            'league' => 'Performance por Liga',
            default => 'Distribución'
        };
    }
    
    public function getChartData(): array
    {
        return match($this->chartType) {
            'categories' => $this->getCategoryDistributionData(),
            'medical' => $this->getMedicalStatusData(),
            'federation' => $this->getFederationTrendData(),
            'league' => $this->getLeaguePerformanceData(),
            default => []
        };
    }
    
    public function getChartStats(): array
    {
        return match($this->chartType) {
            'categories' => $this->getCategoryStats(),
            'medical' => $this->getMedicalStats(),
            'federation' => $this->getFederationStats(),
            'league' => $this->getLeagueStats(),
            default => []
        };
    }
    
    protected function getInsights(): array
    {
        if ($this->chartType === 'categories') {
            return $this->getCategoryInsights();
        }
        
        return [];
    }
    
    protected function getCategoryStats(): array
    {
        $total = Player::count();
        $withCategory = Player::whereNotNull('category')->count();
        $mostPopular = Player::select('category')
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByRaw('COUNT(*) DESC')
            ->first();
            
        return [
            ['label' => 'Total Jugadoras', 'value' => number_format($total)],
            ['label' => 'Con Categoría', 'value' => number_format($withCategory)],
            ['label' => 'Más Popular', 'value' => $mostPopular->category ?? 'N/A'],
            ['label' => 'Cobertura', 'value' => $total > 0 ? round(($withCategory / $total) * 100) . '%' : '0%']
        ];
    }
    
    protected function getMedicalStats(): array
    {
        $total = Player::count();
        $fit = Player::where('medical_status', MedicalStatus::Fit)->count();
        $expired = Player::where('medical_expires_at', '<', now())->count();
        
        return [
            ['label' => 'Total', 'value' => number_format($total)],
            ['label' => 'Aptas', 'value' => number_format($fit)],
            ['label' => 'Vencidos', 'value' => number_format($expired)],
            ['label' => '% Aptas', 'value' => $total > 0 ? round(($fit / $total) * 100) . '%' : '0%']
        ];
    }
    
    protected function getFederationStats(): array
    {
        $total = Player::count();
        $federated = Player::where('federation_status', FederationStatus::Federated)->count();
        $pending = Player::where('federation_status', FederationStatus::PendingPayment)->count();
        
        return [
            ['label' => 'Total', 'value' => number_format($total)],
            ['label' => 'Federadas', 'value' => number_format($federated)],
            ['label' => 'Pendientes', 'value' => number_format($pending)],
            ['label' => '% Federadas', 'value' => $total > 0 ? round(($federated / $total) * 100) . '%' : '0%']
        ];
    }
    
    protected function getLeagueStats(): array
    {
        $leagues = League::count();
        $activeClubs = Club::where('is_active', true)->count();
        $totalPlayers = Player::count();
        $avgPerLeague = $leagues > 0 ? round($totalPlayers / $leagues) : 0;
        
        return [
            ['label' => 'Ligas', 'value' => number_format($leagues)],
            ['label' => 'Clubes Activos', 'value' => number_format($activeClubs)],
            ['label' => 'Total Jugadoras', 'value' => number_format($totalPlayers)],
            ['label' => 'Promedio/Liga', 'value' => number_format($avgPerLeague)]
        ];
    }
    
    protected function getCategoryInsights(): array
    {
        $categoryStats = Player::select('category')
            ->whereNotNull('category')
            ->groupBy('category')
            ->selectRaw('category, COUNT(*) as count')
            ->orderBy('count', 'desc')
            ->get();
            
        $dominant = $categoryStats->first();
        $growth = $this->getGrowthCategory();
        $attention = $this->getAttentionCategory();
        
        return [
            'dominant_category' => $dominant ? $dominant->category : 'N/A',
            'growth_category' => $growth,
            'attention_category' => $attention
        ];
    }
    
    protected function getGrowthCategory(): string
    {
        // Categoría con mayor crecimiento en el último mes
        $lastMonth = Player::where('created_at', '>=', now()->subMonth())
            ->whereNotNull('category')
            ->groupBy('category')
            ->selectRaw('category, COUNT(*) as count')
            ->orderBy('count', 'desc')
            ->first();
            
        return $lastMonth ? $lastMonth->category : 'N/A';
    }
    
    protected function getAttentionCategory(): string
    {
        // Categoría con menos jugadoras activas
        $attention = Player::whereNotNull('category')
            ->groupBy('category')
            ->selectRaw('category, COUNT(*) as count')
            ->orderBy('count', 'asc')
            ->first();
            
        return $attention ? $attention->category : 'N/A';
    }
}