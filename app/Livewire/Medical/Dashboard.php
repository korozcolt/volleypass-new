<?php

namespace App\Livewire\Medical;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Dashboard - Personal Médico')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.medical.dashboard');
    }
}