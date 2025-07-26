<?php

namespace App\Livewire\Public;

use App\Models\Tournament;
use App\Models\VolleyMatch;
use App\Models\Team;
use App\Enums\MatchStatus;
use App\Enums\TournamentStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class PublicTournaments extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $statusFilter = 'all';
    public $categoryFilter = 'all';

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'categoryFilter' => ['except' => 'all']
    ];

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    private function getStats()
    {
        return [
            'active_tournaments' => Tournament::where('status', TournamentStatus::InProgress)->count(),
            'live_matches' => VolleyMatch::where('status', MatchStatus::In_Progress)->count(),
            'registered_teams' => Team::where('status', 'active')->count(),
            'total_players' => \App\Models\Player::whereHas('user', function($q) {
                $q->where('status', 'active');
            })->count()
        ];
    }

    private function getFeaturedTournaments()
    {
        $query = Tournament::with(['teams', 'matches'])
            ->whereIn('status', [TournamentStatus::InProgress, TournamentStatus::RegistrationOpen])
            ->where('is_public', true);

        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        if ($this->statusFilter !== 'all') {
            // Convertir string a enum si es necesario
            $statusEnum = match($this->statusFilter) {
                'in_progress' => TournamentStatus::InProgress,
                'registration_open' => TournamentStatus::RegistrationOpen,
                'registration_closed' => TournamentStatus::RegistrationClosed,
                'finished' => TournamentStatus::Finished,
                'cancelled' => TournamentStatus::Cancelled,
                'draft' => TournamentStatus::Draft,
                default => null
            };

            if ($statusEnum) {
                $query->where('status', $statusEnum);
            }
        }

        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        return $query->latest()->paginate(6);
    }

    private function getLiveMatches()
    {
        return VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::In_Progress)
            ->whereHas('tournament', function($q) {
                $q->where('is_public', true);
            })
            ->latest('updated_at')
            ->take(3)
            ->get();
    }

    private function getUpcomingMatches()
    {
        return VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::Scheduled)
            ->where('scheduled_at', '>=', now())
            ->where('scheduled_at', '<=', now()->addDays(7))
            ->whereHas('tournament', function($q) {
                $q->where('is_public', true);
            })
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.public.public-tournaments', [
            'stats' => $this->getStats(),
            'featuredTournaments' => $this->getFeaturedTournaments(),
            'liveMatches' => $this->getLiveMatches(),
            'upcomingMatches' => $this->getUpcomingMatches(),
        ]);
    }
}
