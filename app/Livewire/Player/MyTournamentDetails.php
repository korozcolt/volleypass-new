<?php

namespace App\Livewire\Player;

use App\Models\Tournament;
use App\Models\Player;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.player')]
class MyTournamentDetails extends Component
{
    public Tournament $tournament;
    public $player;
    public $playerTeam;
    public $teamMatches;
    public $teamStandings;
    public $activeTab = 'overview';

    public function mount(Tournament $tournament)
    {
        $this->player = Auth::user()->player;

        if (!$this->player) {
            return redirect()->route('home');
        }

        // Verificar que la jugadora participe en este torneo
        $this->playerTeam = $tournament->teams()
            ->whereHas('players', function ($query) {
                $query->where('player_id', $this->player->id);
            })
            ->first();

        if (!$this->playerTeam) {
            abort(403, 'No participas en este torneo');
        }

        $this->tournament = $tournament->load(['teams', 'matches']);
        $this->loadTournamentData();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    private function loadTournamentData()
    {
        $this->loadTeamMatches();
        $this->loadTeamStandings();
    }

    private function loadTeamMatches()
    {
        $this->teamMatches = $this->tournament->matches()
            ->with(['homeTeam', 'awayTeam'])
            ->where(function ($query) {
                $query->where('home_team_id', $this->playerTeam->id)
                      ->orWhere('away_team_id', $this->playerTeam->id);
            })
            ->orderBy('scheduled_at')
            ->get();
    }

    private function loadTeamStandings()
    {
        // Calcular posición del equipo en el torneo
        $teams = $this->tournament->teams()->with(['matches'])->get();

        $standings = $teams->map(function ($team) {
            $matches = $team->matches()->where('tournament_id', $this->tournament->id)->get();

            $wins = $matches->where('winner_team_id', $team->id)->count();
            $losses = $matches->where('winner_team_id', '!=', $team->id)->where('winner_team_id', '!=', null)->count();
            $played = $wins + $losses;

            return [
                'team' => $team,
                'played' => $played,
                'wins' => $wins,
                'losses' => $losses,
                'points' => ($wins * 3) + ($losses * 1),
            ];
        })->sortByDesc('points')->values();

        $this->teamStandings = $standings->search(function ($item) {
            return $item['team']->id === $this->playerTeam->id;
        }) + 1; // +1 porque search devuelve índice base 0
    }

    public function render()
    {
        return view('livewire.player.my-tournament-details');
    }
}
