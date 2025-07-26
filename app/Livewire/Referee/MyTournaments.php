<?php

namespace App\Livewire\Referee;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Mis Torneos - Árbitro')]
class MyTournaments extends Component
{
    public function render()
    {
        return view('livewire.referee.my-tournaments');
    }
}