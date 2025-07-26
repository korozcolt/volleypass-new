<?php

namespace App\Livewire\Player;

use App\Models\Player;
use App\Models\VolleyMatch;
use App\Models\Tournament;
use App\Enums\MatchStatus;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.player')]
class PlayerDashboard extends Component
{
    public $player;
    public $upcomingMatches;
    public $recentMatches;
    public $activeTournaments;
    public $playerStats;
    public $notifications;

    public function mount()
    {
        $this->player = Auth::user()->player;

        if (!$this->player) {
            // Redirigir si no es jugadora
            return redirect()->route('home');
        }

        $this->loadDashboardData();
    }

    private function loadDashboardData()
    {
        $this->loadUpcomingMatches();
        $this->loadRecentMatches();
        $this->loadActiveTournaments();
        $this->loadPlayerStats();
        $this->loadNotifications();
    }

    private function loadUpcomingMatches()
    {
        $this->upcomingMatches = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
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
            ->take(3)
            ->get();
    }

    private function loadRecentMatches()
    {
        $this->recentMatches = VolleyMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', MatchStatus::Finished)
            ->where(function ($query) {
                $query->whereHas('homeTeam.players', function ($q) {
                    $q->where('player_id', $this->player->id);
                })->orWhereHas('awayTeam.players', function ($q) {
                    $q->where('player_id', $this->player->id);
                });
            })
            ->latest('finished_at')
            ->take(3)
            ->get();
    }

    private function loadActiveTournaments()
    {
        $this->activeTournaments = Tournament::with(['teams'])
            ->whereIn('status', ['active', 'registration_open'])
            ->whereHas('teams.players', function ($query) {
                $query->where('player_id', $this->player->id);
            })
            ->latest()
            ->take(3)
            ->get();
    }

    private function loadPlayerStats()
    {
        // Estadísticas básicas de la jugadora
        $totalMatches = VolleyMatch::where('status', MatchStatus::Finished)
            ->where(function ($query) {
                $query->whereHas('homeTeam.players', function ($q) {
                    $q->where('player_id', $this->player->id);
                })->orWhereHas('awayTeam.players', function ($q) {
                    $q->where('player_id', $this->player->id);
                });
            })
            ->count();

        $wins = VolleyMatch::where('status', MatchStatus::Finished)
            ->where(function ($query) {
                $query->whereHas('homeTeam.players', function ($q) {
                    $q->where('player_id', $this->player->id);
                })->where('home_sets', '>', 'away_sets')
                ->orWhere(function ($subQuery) {
                    $subQuery->whereHas('awayTeam.players', function ($q) {
                        $q->where('player_id', $this->player->id);
                    })->where('away_sets', '>', 'home_sets');
                });
            })
            ->count();

        $this->playerStats = [
            'total_matches' => $totalMatches,
            'wins' => $wins,
            'losses' => $totalMatches - $wins,
            'win_percentage' => $totalMatches > 0 ? round(($wins / $totalMatches) * 100, 1) : 0,
            'active_tournaments' => $this->activeTournaments->count(),
        ];
    }

    private function loadNotifications()
    {
        $this->notifications = collect([
            [
                'type' => 'match',
                'title' => 'Próximo partido',
                'message' => 'Tienes un partido programado para mañana a las 15:00',
                'time' => '2 horas',
                'read' => false
            ],
            [
                'type' => 'tournament',
                'title' => 'Nuevo torneo',
                'message' => 'Se ha abierto la inscripción para el Torneo Regional',
                'time' => '1 día',
                'read' => false
            ]
        ]);
    }

    public function render()
    {
        return view('livewire.player.player-dashboard');
    }
}
