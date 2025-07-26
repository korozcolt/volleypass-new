<?php

namespace App\Livewire\Medical;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Reportes Médicos - Personal Médico')]
class MedicalReports extends Component
{
    public function render()
    {
        return view('livewire.medical.medical-reports');
    }
}