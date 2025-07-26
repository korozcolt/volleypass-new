<?php

namespace App\Livewire\Medical;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;

#[Layout('layouts.app')]
#[Title('Historial Médico - Personal Médico')]
class PlayerMedicalHistory extends Component
{
    public User $player;
    
    public function mount(User $player)
    {
        $this->player = $player;
    }
    
    public function render()
    {
        return view('livewire.medical.player-medical-history');
    }
}