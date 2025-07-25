<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PlayerCard extends Component
{
    public $player;
    public $cardStatus;
    public $qrCode;
    public $medicalInfo;

    public function mount()
    {
        $this->player = Auth::user()->player;
        $this->loadCardData();
    }

    public function loadCardData()
    {
        $this->cardStatus = [
            'status' => 'active', // active, expired, restricted
            'expiry_date' => '2024-12-31',
            'federation_number' => 'VS-2024-' . str_pad($this->player->id ?? 1, 4, '0', STR_PAD_LEFT),
            'last_updated' => now()->format('Y-m-d')
        ];

        $this->qrCode = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . 
                       urlencode(json_encode([
                           'player_id' => $this->player->id ?? 1,
                           'federation_number' => $this->cardStatus['federation_number'],
                           'status' => $this->cardStatus['status']
                       ]));

        $this->medicalInfo = [
            'blood_type' => 'O+',
            'allergies' => 'Ninguna',
            'emergency_contact' => '+57 300 123 4567',
            'last_medical_check' => '2024-01-15'
        ];
    }

    public function downloadCard()
    {
        // Lógica para descargar/imprimir carnet
        $this->dispatch('show-notification', [
            'type' => 'success',
            'message' => 'Carnet descargado exitosamente'
        ]);
    }

    public function render()
    {
        return view('livewire.player.player-card');
    }
}
