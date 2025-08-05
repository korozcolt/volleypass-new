<?php

namespace App\Observers;

use App\Models\Player;
use App\Models\PlayerCard;
use App\Models\League;
use App\Enums\CardStatus;
use App\Enums\MedicalStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlayerObserver
{
    /**
     * Handle the Player "created" event.
     */
    public function created(Player $player): void
    {
        try {
            // Obtener la liga del club actual del jugador o la primera liga disponible
            $league = $player->currentClub?->league ?? League::first();
            
            if (!$league) {
                Log::warning('No se pudo crear carnet para el jugador ID: ' . $player->id . ' - No hay liga disponible');
                return;
            }

            // Generar número de carnet
            $cardNumber = PlayerCard::generateCardNumber($player, $league);
            
            // Generar QR code y verification token
            $qrCode = hash(
                'sha256',
                $cardNumber .
                    $player->id .
                    now()->timestamp .
                    config('app.key')
            );
            
            $verificationToken = hash(
                'sha256',
                $qrCode .
                    $player->id .
                    'verification_token'
            );
            
            // Crear el carnet
            $card = PlayerCard::create([
                'player_id' => $player->id,
                'league_id' => $league->id,
                'card_number' => $cardNumber,
                'qr_code' => $qrCode,
                'verification_token' => $verificationToken,
                'status' => CardStatus::Active,
                'medical_status' => MedicalStatus::Fit,
                'issued_at' => now(),
                'expires_at' => now()->addYear(), // Válido por 1 año
                'season' => now()->year,
                'version' => 1,
                'issued_by' => Auth::id() ?? 1,
            ]);
            
            Log::info('Carnet creado automáticamente para jugador ID: ' . $player->id . ' con número: ' . $cardNumber);
            
        } catch (\Exception $e) {
            Log::error('Error al crear carnet para jugador ID: ' . $player->id . ' - ' . $e->getMessage());
        }
    }

    /**
     * Handle the Player "updated" event.
     */
    public function updated(Player $player): void
    {
        // Si el jugador cambia de club, podríamos necesitar actualizar el carnet
        if ($player->isDirty('current_club_id') && $player->current_card) {
            $newClub = $player->currentClub;
            if ($newClub && $newClub->league_id !== $player->current_card->league_id) {
                // El jugador cambió a una liga diferente
                // Podrías implementar lógica para crear un nuevo carnet o actualizar el existente
                Log::info('Jugador ID: ' . $player->id . ' cambió de liga. Revisar carnet.');
            }
        }
    }

    /**
     * Handle the Player "deleted" event.
     */
    public function deleted(Player $player): void
    {
        // Cuando se elimina un jugador, marcar sus carnets como cancelados
        $player->playerCards()->update([
            'status' => CardStatus::Cancelled
        ]);
        
        Log::info('Carnets desactivados para jugador eliminado ID: ' . $player->id);
    }

    /**
     * Handle the Player "restored" event.
     */
    public function restored(Player $player): void
    {
        // Si se restaura un jugador, reactivar su carnet más reciente
        $latestCard = $player->playerCards()->latest()->first();
        if ($latestCard) {
            $latestCard->update(['status' => CardStatus::Active]);
            Log::info('Carnet reactivado para jugador restaurado ID: ' . $player->id);
        }
    }
}