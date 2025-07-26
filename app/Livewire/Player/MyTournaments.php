<?php

namespace App\Livewire\Player;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\VolleyMatch;
use App\Models\Team;
use App\Enums\MatchStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.player')]
class MyTournaments extends Component
{
    use WithPagination;

    public $player;
    public $selectedStatus = 'all'; // all, active, completed, upcoming
    public $searchTerm = '';

    protected $queryString = [
        'selectedStatus' => ['except' => 'all'],
        'searchTerm' => ['except' => '']
    ];

    public function mount()
    {
        $this->player = Auth::user()->player;

        if (!$this->player) {
            return redirect()->route('home');
        }
    }

    public function updatedSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Tournament::with(['teams', 'matches'])
            ->whereHas('teams.players', function ($q) {
                $q->where('player_id', $this->player->id);
            });

        // Apply status filter
        if ($this->selectedStatus !== 'all') {
            // Convertir string a enum
            $statusEnum = match($this->selectedStatus) {
                'in_progress' => \App\Enums\TournamentStatus::InProgress,
                'registration_open' => \App\Enums\TournamentStatus::RegistrationOpen,
                'registration_closed' => \App\Enums\TournamentStatus::RegistrationClosed,
                'finished' => \App\Enums\TournamentStatus::Finished,
                'cancelled' => \App\Enums\TournamentStatus::Cancelled,
                'draft' => \App\Enums\TournamentStatus::Draft,
                default => null
            };

            if ($statusEnum) {
                $query->where('status', $statusEnum);
            }
        }

        // Apply search filter
        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        $tournaments = $query->latest('start_date')->paginate(6);

        // Get upcoming matches for this player
        $upcomingMatches = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::Scheduled)
            ->where('scheduled_at', '>=', now())
            ->where(function ($query) {
                $query->whereHas('homeTeam.players', function ($q) {
                    $q->where('player_id', $this->player->id);
                })->orWhereHas('awayTeam.players', function ($q) {
                    $q->where('player_id', $this->player->id);
                });
            })
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        // Get recent matches for this player
        $recentMatches = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::Finished)
            ->where(function ($query) {
                $query->whereHas('homeTeam.players', function ($q) {
                    $q->where('player_id', $this->player->id);
                })->orWhereHas('awayTeam.players', function ($q) {
                    $q->where('player_id', $this->player->id);
                });
            })
            ->latest('finished_at')
            ->take(5)
            ->get();

        return view('livewire.player.my-tournaments', [
            'tournaments' => $tournaments,
            'upcomingMatches' => $upcomingMatches,
            'recentMatches' => $recentMatches
        ]);
    }

    private function getPlayerTeamInTournament($tournament)
    {
        return $tournament->teams()
            ->whereHas('players', function ($q) {
                $q->where('player_id', $this->player->id);
            })
            ->first();
    }

    private function getStatusLabel($status)
    {
        return match($status) {
            'upcoming' => 'Próximo',
            'active' => 'Activo',
            'completed' => 'Finalizado',
            'cancelled' => 'Cancelado',
            default => 'Desconocido'
        };
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'upcoming' => 'text-blue-600 bg-blue-100',
            'active' => 'text-green-600 bg-green-100',
            'completed' => 'text-gray-600 bg-gray-100',
            'cancelled' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
}
