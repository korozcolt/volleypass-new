<?php

namespace App\Livewire\Medical;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Lista de Jugadoras - Personal Médico')]
class PlayersList extends Component
{
    public function render()
    {
        return view('livewire.medical.players-list');
    }
}