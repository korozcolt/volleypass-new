<?php

namespace App\Livewire\Public;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\VolleyMatch;
use App\Enums\PlayerCategory;
use App\Enums\MatchStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class Teams extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $cityFilter = 'all';
    public $categoryFilter = 'all';
    public $sortBy = 'name'; // name, matches, wins, recent_activity

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'cityFilter' => ['except' => 'all'],
        'categoryFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'name']
    ];

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedCityFilter()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function getTeamsProperty()
    {
        $query = Team::with(['club.city', 'players', 'tournaments'])
            ->where('status', 'active');

        // Filtro de búsqueda
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('club', function ($clubQuery) {
                      $clubQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }

        // Filtro por ciudad
        if ($this->cityFilter !== 'all') {
            $query->whereHas('club', function ($q) {
                $q->where('city_id', $this->cityFilter);
            });
        }

        // Filtro por categoría
        if ($this->categoryFilter !== 'all') {
            $query->whereHas('tournaments', function ($q) {
                $q->where('category', $this->categoryFilter);
            });
        }

        // Ordenamiento
        switch ($this->sortBy) {
            case 'matches':
                $query->withCount('homeMatches as total_matches')
                      ->orderByDesc('total_matches');
                break;
            case 'wins':
                $query->withCount([
                    'homeMatches as home_wins' => function ($q) {
                        $q->where('status', MatchStatus::Finished)
                          ->whereRaw('home_score > away_score');
                    },
                    'awayMatches as away_wins' => function ($q) {
                        $q->where('status', MatchStatus::Finished)
                          ->whereRaw('away_score > home_score');
                    }
                ])->orderByRaw('(home_wins + away_wins) DESC');
                break;
            case 'recent_activity':
                $query->orderBy('updated_at', 'desc');
                break;
            default:
                $query->orderBy('name');
                break;
        }

        return $query->paginate(12);
    }

    public function getCitiesProperty()
    {
        return \App\Models\City::whereHas('clubs.teams', function ($q) {
                $q->where('status', 'active');
            })
            ->orderBy('name')
            ->get()
            ->map(function ($city) {
                return (object) [
                    'id' => $city->id,
                    'name' => $city->name
                ];
            });
    }

    public function getCategoriesProperty()
    {
        return collect(PlayerCategory::cases())->map(function ($category) {
            return (object) [
                'value' => $category->value,
                'label' => $category->getLabel()
            ];
        });
    }

    public function getTeamStats($team)
    {
        $homeMatches = VolleyMatch::where('home_team_id', $team->id)
            ->where('status', MatchStatus::Finished)
            ->get();
        
        $awayMatches = VolleyMatch::where('away_team_id', $team->id)
            ->where('status', MatchStatus::Finished)
            ->get();

        $totalMatches = $homeMatches->count() + $awayMatches->count();
        
        $homeWins = $homeMatches->where('home_score', '>', 'away_score')->count();
        $awayWins = $awayMatches->where('away_score', '>', 'home_score')->count();
        $totalWins = $homeWins + $awayWins;

        $activeTournaments = $team->tournaments()
            ->whereIn('status', ['in_progress', 'registration_closed'])
            ->count();

        return [
            'total_matches' => $totalMatches,
            'total_wins' => $totalWins,
            'win_percentage' => $totalMatches > 0 ? round(($totalWins / $totalMatches) * 100) : 0,
            'active_tournaments' => $activeTournaments,
            'total_players' => $team->players->count()
        ];
    }

    public function render()
    {
        return view('livewire.public.teams', [
            'teams' => $this->teams,
            'cities' => $this->cities,
            'categories' => $this->categories
        ]);
    }
}