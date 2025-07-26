<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Mis Torneos - Entrenador')]
class MyTournaments extends Component
{
    public function render()
    {
        return view('livewire.coach.my-tournaments');
    }
}