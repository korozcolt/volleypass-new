<?php

namespace App\Livewire\Public;

use App\Models\VolleyMatch;
use App\Models\Tournament;
use App\Models\Team;

use App\Enums\MatchStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.public')]
class Schedule extends Component
{
    use WithPagination;

    public $selectedTournament = 'all';
    public $selectedDate = null;
    public $selectedStatus = 'all';
    public $selectedVenue = 'all';
    public $viewMode = 'list'; // list, calendar

    protected $queryString = [
        'selectedTournament' => ['except' => 'all'],
        'selectedDate' => ['except' => null],
        'selectedStatus' => ['except' => 'all'],
        'selectedVenue' => ['except' => 'all'],
        'viewMode' => ['except' => 'list']
    ];

    public function mount()
    {
        // Set default date to today if not specified
        if (!$this->selectedDate) {
            $this->selectedDate = now()->format('Y-m-d');
        }
    }

    public function render()
    {
        $matches = $this->getMatches();
        $tournaments = $this->getTournaments();
        $venues = $this->getVenues();
        $upcomingMatches = $this->getUpcomingMatches();
        $todayMatches = $this->getTodayMatches();

        return view('livewire.public.schedule', [
            'matches' => $matches,
            'tournaments' => $tournaments,
            'venues' => $venues,
            'upcomingMatches' => $upcomingMatches,
            'todayMatches' => $todayMatches,
            'matchStatuses' => MatchStatus::cases()
        ]);
    }

    private function getMatches()
    {
        $query = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->orderBy('match_date', 'asc')
            ->orderBy('match_time', 'asc');

        // Filter by tournament
        if ($this->selectedTournament !== 'all') {
            $query->where('tournament_id', $this->selectedTournament);
        }

        // Filter by date
        if ($this->selectedDate) {
            $query->whereDate('match_date', $this->selectedDate);
        } else {
            // Show matches from today onwards
            $query->whereDate('match_date', '>=', now()->toDateString());
        }

        // Filter by status
        if ($this->selectedStatus !== 'all') {
            $query->where('status', $this->selectedStatus);
        }

        // Filter by venue
        if ($this->selectedVenue !== 'all') {
            $query->where('venue', $this->selectedVenue);
        }

        return $query->paginate(20);
    }

    private function getTournaments()
    {
        return Tournament::whereHas('matches')
            ->orderBy('name')
            ->get();
    }

    private function getVenues()
    {
        return VolleyMatch::whereNotNull('venue')
            ->distinct()
            ->pluck('venue')
            ->filter()
            ->sort()
            ->map(function($venue) {
                return (object) ['id' => $venue, 'name' => $venue];
            });
    }

    private function getUpcomingMatches()
    {
        return VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::Scheduled)
            ->whereDate('match_date', '>=', now()->toDateString())
            ->orderBy('match_date', 'asc')
            ->orderBy('match_time', 'asc')
            ->limit(5)
            ->get();
    }

    private function getTodayMatches()
    {
        return VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->whereDate('match_date', now()->toDateString())
            ->orderBy('match_time', 'asc')
            ->get();
    }

    public function setDate($date)
    {
        $this->selectedDate = $date;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->selectedTournament = 'all';
        $this->selectedDate = now()->format('Y-m-d');
        $this->selectedStatus = 'all';
        $this->selectedVenue = 'all';
        $this->resetPage();
    }

    public function refreshData()
    {
        // This method can be called to refresh the component data
        $this->render();
    }

    // Computed properties for quick access
    public function getMatchesCountProperty()
    {
        $query = VolleyMatch::query();
        
        if ($this->selectedDate) {
            $query->whereDate('match_date', $this->selectedDate);
        }
        
        return $query->count();
    }

    public function getTodayMatchesCountProperty()
    {
        return VolleyMatch::whereDate('match_date', now()->toDateString())->count();
    }

    public function getUpcomingMatchesCountProperty()
    {
        return VolleyMatch::where('status', MatchStatus::Scheduled)
            ->whereDate('match_date', '>=', now()->toDateString())
            ->count();
    }
}