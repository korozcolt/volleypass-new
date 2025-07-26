<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.public')]
#[Title('Partidos en Vivo - VolleyPass')]
class Matches extends Component
{
    public function render()
    {
        return view('livewire.public.matches');
    }
}