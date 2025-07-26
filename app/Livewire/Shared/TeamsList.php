<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Equipos')]
class TeamsList extends Component
{
    public function render()
    {
        return view('livewire.shared.teams-list');
    }
}