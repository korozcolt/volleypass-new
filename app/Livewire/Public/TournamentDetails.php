<?php

namespace App\Livewire\Public;

use App\Models\Tournament;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class TournamentDetails extends Component
{
    public Tournament $tournament;
    public $activeTab = 'overview';

    public function mount(Tournament $tournament)
    {
        // Verificar que el torneo sea público
        if (!$tournament->is_public) {
            abort(404);
        }

        $this->tournament = $tournament->load([
            'teams',
            'matches.homeTeam',
            'matches.awayTeam',
            'league',
            'groups'
        ]);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.public.tournament-details');
    }
}
