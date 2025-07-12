<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Player;
use App\Models\PlayerCard;
use App\Models\MedicalCertificate;

class GenerateSeasonCards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600;

    public function __construct(
        private int $season,
        private bool $onlyEligible = true
    ) {}

    public function handle(): void
    {
        Log::info('Iniciando generación de carnets para temporada', [
            'season' => $this->season
        ]);

        try {
            $query = Player::with(['user', 'currentClub', 'medicalCertificates'])
                ->where('status', 'active')
                ->whereHas('user', function($q) {
                    $q->where('status', 'active');
                });

            if ($this->onlyEligible) {
                $query->where('is_eligible', true);
            }

            $players = $query->get();
            $cardsGenerated = 0;
            $errors = 0;

            foreach ($players as $player) {
                try {
                    // Verificar si ya tiene carnet para esta temporada
                    $existingCard = PlayerCard::where('player_id', $player->id)
                        ->where('season', $this->season)
                        ->whereIn('status', ['active', 'pending_approval'])
                        ->first();

                    if ($existingCard) {
                        continue; // Ya tiene carnet
                    }

                    // Verificar elegibilidad médica
                    if (!$this->hasValidMedicalCertificate($player)) {
                        Log::warning('Jugadora sin certificado médico válido', [
                            'player_id' => $player->id,
                            'player_name' => $player->user->full_name
                        ]);
                        continue;
                    }

                    // Generar carnet directamente
                    $card = $this->generateCardForPlayer($player);
                    $cardsGenerated++;

                    Log::info('Carnet generado', [
                        'player_id' => $player->id,
                        'card_id' => $card->id,
                        'card_number' => $card->card_number
                    ]);

                } catch (\Exception $e) {
                    $errors++;
                    Log::error('Error generando carnet', [
                        'player_id' => $player->id,
                        'player_name' => $player->user->full_name,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Generación de carnets completada', [
                'season' => $this->season,
                'total_players' => $players->count(),
                'cards_generated' => $cardsGenerated,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Error en GenerateSeasonCards', [
                'season' => $this->season,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function hasValidMedicalCertificate(Player $player): bool
    {
        return $player->medicalCertificates()
            ->where('status', 'approved')
            ->where('is_current', true)
            ->where('expires_at', '>', now()->addMonths(3))
            ->exists();
    }

    private function generateCardForPlayer(Player $player): PlayerCard
    {
        // Obtener certificado médico actual
        $medicalCertificate = $player->medicalCertificates()
            ->where('status', 'approved')
            ->where('is_current', true)
            ->orderBy('expires_at', 'desc')
            ->first();

        // Generar número de carnet
        $cardNumber = $this->generateCardNumber($player);

        // Crear carnet
        $card = PlayerCard::create([
            'player_id' => $player->id,
            'card_number' => $cardNumber,
            'status' => 'active',
            'issued_at' => now(),
            'expires_at' => now()->addYear(), // Válido por 1 año
            'season' => $this->season,
            'medical_status' => $medicalCertificate?->medical_status ?? 'fit',
            'medical_check_date' => $medicalCertificate?->issue_date,
            'medical_approved_by' => $medicalCertificate?->reviewed_by,
            'issued_by' => 1, // Sistema automático
            'version' => 1,
        ]);

        // Generar código QR
        $card->qr_code = hash('sha256', $card->card_number . $card->player_id . now()->timestamp);
        $card->verification_token = hash('sha256', $card->qr_code . 'verification');
        $card->save();

        return $card;
    }

    private function generateCardNumber(Player $player): string
    {
        $clubCode = str_pad($player->current_club_id, 3, '0', STR_PAD_LEFT);
        $sequence = PlayerCard::where('season', $this->season)->count() + 1;

        return "VP-{$this->season}-{$clubCode}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
