<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Mi Equipo - Entrenador')]
class TeamManagement extends Component
{
    public function render()
    {
        return view('livewire.coach.team-management');
    }
}