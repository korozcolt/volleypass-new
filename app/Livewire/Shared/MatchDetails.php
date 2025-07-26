<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\VolleyMatch;

#[Layout('layouts.app')]
#[Title('Detalles del Partido')]
class MatchDetails extends Component
{
    public VolleyMatch $match;
    
    public function mount(VolleyMatch $match)
    {
        $this->match = $match;
    }
    
    public function render()
    {
        return view('livewire.shared.match-details');
    }
}