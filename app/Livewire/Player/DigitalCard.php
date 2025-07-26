<?php

namespace App\Livewire\Player;

use App\Models\User;
use App\Models\PlayerCard;
use App\Models\Payment;
use App\Enums\CardStatus;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;

#[Layout('layouts.player-dashboard')]
class DigitalCard extends Component
{
    public $player;
    public $currentCard;
    public $cardHistory;
    public $paymentHistory;
    public $qrCodeData;
    public $cardStatus;
    public $expirationDate;
    public $daysUntilExpiration;
    public $showRenewalForm = false;
    
    public function mount()
    {
        $this->player = Auth::user()->player;
        
        // Verificar si el usuario tiene un perfil de jugador
        if (!$this->player) {
            session()->flash('error', 'No tienes un perfil de jugador asociado a tu cuenta.');
            $this->redirect(route('dashboard'));
            return;
        }
        
        $this->loadCardData();
    }
    
    private function loadCardData()
    {
        // Obtener carnet actual
        $this->currentCard = PlayerCard::where('player_id', $this->player->id)
            ->where('status', '!=', CardStatus::Cancelled)
            ->latest()
            ->first();
            
        if ($this->currentCard) {
            $this->cardStatus = $this->currentCard->status;
            $this->expirationDate = $this->currentCard->expiration_date;
            $this->daysUntilExpiration = $this->expirationDate ? 
                Carbon::now()->diffInDays($this->expirationDate, false) : null;
                
            // Generar datos del QR
            $this->qrCodeData = [
                'card_id' => $this->currentCard->id,
                'player_id' => $this->player->id,
                'card_number' => $this->currentCard->card_number,
                'expiration' => $this->expirationDate?->format('Y-m-d'),
                'status' => $this->cardStatus->value,
                'verification_url' => route('card.verify', $this->currentCard->verification_token)
            ];
        }
        
        // Historial de carnets
        $this->cardHistory = PlayerCard::where('player_id', $this->player->id)
            ->with('payments')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Historial de pagos federativos
        $this->paymentHistory = Payment::where('player_id', $this->player->id)
            ->where('payment_type', 'federation_fee')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }
    
    public function generateQrCode()
    {
        if (!$this->currentCard) {
            return null;
        }
        
        $qrData = json_encode($this->qrCodeData);
        
        return QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($qrData);
    }
    
    public function downloadCardPdf()
    {
        if (!$this->currentCard) {
            $this->dispatch('error', [
                'message' => 'No tienes un carnet activo para descargar'
            ]);
            return;
        }
        
        $qrCodeSvg = $this->generateQrCode();
        
        $data = [
            'player' => $this->player,
            'card' => $this->currentCard,
            'qrCode' => $qrCodeSvg,
            'club' => $this->player->team?->club,
            'category' => $this->player->category,
        ];
        
        $pdf = PDF::loadView('pdf.player-card', $data)
            ->setPaper('a4', 'portrait');
            
        $filename = 'carnet_' . $this->player->user->name . '_' . $this->currentCard->card_number . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
    
    public function verifyCardAuthenticity()
    {
        if (!$this->currentCard) {
            return false;
        }
        
        // Verificar que el carnet no esté vencido
        if ($this->expirationDate && $this->expirationDate->isPast()) {
            return false;
        }
        
        // Verificar que el carnet esté activo
        if ($this->cardStatus !== CardStatus::Active) {
            return false;
        }
        
        // Verificar que el token de verificación sea válido
        return !empty($this->currentCard->verification_token);
    }
    
    public function requestCardRenewal()
    {
        if (!$this->currentCard) {
            $this->dispatch('error', [
                'message' => 'No tienes un carnet para renovar'
            ]);
            return;
        }
        
        // Verificar si ya hay una renovación pendiente
        $pendingRenewal = PlayerCard::where('player_id', $this->player->id)
            ->where('status', CardStatus::Pending_Approval)
            ->exists();
            
        if ($pendingRenewal) {
            $this->dispatch('warning', [
                'message' => 'Ya tienes una solicitud de renovación pendiente'
            ]);
            return;
        }
        
        // Crear nueva solicitud de carnet
        $newCard = PlayerCard::create([
            'player_id' => $this->player->id,
            'card_number' => $this->generateCardNumber(),
            'issue_date' => Carbon::now(),
            'expiration_date' => Carbon::now()->addYear(),
            'status' => CardStatus::Pending_Approval,
            'verification_token' => str()->random(32),
            'previous_card_id' => $this->currentCard->id
        ]);
        
        $this->dispatch('renewal-requested', [
            'message' => 'Solicitud de renovación enviada correctamente',
            'card_id' => $newCard->id
        ]);
        
        $this->loadCardData();
    }
    
    private function generateCardNumber()
    {
        $year = Carbon::now()->year;
        $sequence = PlayerCard::whereYear('created_at', $year)->count() + 1;
        
        return sprintf('%d%04d%04d', $year, $this->player->id, $sequence);
    }
    
    public function getCardStatusLabel()
    {
        return match($this->cardStatus) {
            CardStatus::Active => 'Activo',
            CardStatus::Expired => 'Vencido',
            CardStatus::Suspended => 'Suspendido',
            CardStatus::Pending_Approval => 'Pendiente de Aprobación',
            CardStatus::Cancelled => 'Cancelado',
            default => 'Desconocido'
        };
    }
    
    public function getCardStatusColor()
    {
        return match($this->cardStatus) {
            CardStatus::Active => 'text-green-600 bg-green-100',
            CardStatus::Expired => 'text-red-600 bg-red-100',
            CardStatus::Suspended => 'text-yellow-600 bg-yellow-100',
            CardStatus::Pending_Approval => 'text-blue-600 bg-blue-100',
            CardStatus::Cancelled => 'text-gray-600 bg-gray-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
    
    public function toggleRenewalForm()
    {
        $this->showRenewalForm = !$this->showRenewalForm;
    }
    
    public function refreshCardData()
    {
        $this->loadCardData();
        
        $this->dispatch('card-refreshed', [
            'message' => 'Datos del carnet actualizados'
        ]);
    }
    
    public function render()
    {
        return view('livewire.player.digital-card', [
            'qrCodeSvg' => $this->generateQrCode(),
            'isCardValid' => $this->verifyCardAuthenticity(),
            'statusLabel' => $this->getCardStatusLabel(),
            'statusColor' => $this->getCardStatusColor()
        ]);
    }
}