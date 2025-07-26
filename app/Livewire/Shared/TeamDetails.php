<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Team;

#[Layout('layouts.app')]
#[Title('Detalles del Equipo')]
class TeamDetails extends Component
{
    public Team $team;
    
    public function mount(Team $team)
    {
        $this->team = $team;
    }
    
    public function render()
    {
        return view('livewire.shared.team-details');
    }
}