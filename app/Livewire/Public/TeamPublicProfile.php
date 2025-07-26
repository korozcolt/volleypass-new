<?php

namespace App\Livewire\Public;

use App\Models\Team;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class TeamPublicProfile extends Component
{
    public Team $team;
    public $activeTab = 'overview';

    public function mount(Team $team)
    {
        // Verificar que el equipo sea público
        if (!$team->is_public) {
            abort(404);
        }

        $this->team = $team->load([
            'players.user',
            'coach.user',
            'club',
            'matches.homeTeam',
            'matches.awayTeam',
            'tournaments'
        ]);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.public.team-public-profile');
    }
}
