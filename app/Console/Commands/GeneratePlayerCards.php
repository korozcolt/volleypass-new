<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Player;
use App\Models\PlayerCard;
use App\Models\League;
use App\Enums\CardStatus;
use App\Enums\MedicalStatus;
use Illuminate\Support\Facades\Log;

class GeneratePlayerCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'players:generate-cards 
                            {--force : Regenerar carnets incluso si ya existen}
                            {--player= : ID específico de jugador}
                            {--league= : ID específica de liga}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera carnets automáticamente para jugadores que no tienen carnet activo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🆔 Iniciando generación de carnets de jugadores...');
        
        $force = $this->option('force');
        $playerId = $this->option('player');
        $leagueId = $this->option('league');
        
        // Construir query base
        $query = Player::with(['user', 'currentClub.league', 'current_card']);
        
        // Filtrar por jugador específico si se proporciona
        if ($playerId) {
            $query->where('id', $playerId);
        }
        
        // Filtrar por liga específica si se proporciona
        if ($leagueId) {
            $query->whereHas('currentClub', function ($q) use ($leagueId) {
                $q->where('league_id', $leagueId);
            });
        }
        
        $players = $query->get();
        
        if ($players->isEmpty()) {
            $this->warn('No se encontraron jugadores que cumplan los criterios.');
            return 0;
        }
        
        $this->info("Procesando {$players->count()} jugadores...");
        
        $created = 0;
        $skipped = 0;
        $errors = 0;
        
        $progressBar = $this->output->createProgressBar($players->count());
        $progressBar->start();
        
        foreach ($players as $player) {
            try {
                // Verificar si ya tiene carnet activo
                if (!$force && $player->current_card) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }
                
                // Obtener la liga
                $league = $player->currentClub?->league ?? League::first();
                
                if (!$league) {
                    $this->newLine();
                    $this->warn("Jugador {$player->user->full_name} (ID: {$player->id}) no tiene liga asignada. Saltando...");
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }
                
                // Si es force, desactivar carnet actual
                if ($force && $player->current_card) {
                    $player->current_card->update(['status' => CardStatus::Replaced]);
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
                    'expires_at' => now()->addYear(),
                    'season' => now()->year,
                    'version' => $force && $player->current_card ? $player->current_card->version + 1 : 1,
                    'issued_by' => 1, // Asumiendo que el superadmin tiene ID 1
                ]);
                
                $created++;
                
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error al crear carnet para {$player->user->full_name} (ID: {$player->id}): {$e->getMessage()}");
                Log::error("Error al crear carnet para jugador ID: {$player->id} - {$e->getMessage()}");
                $errors++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Mostrar resumen
        $this->info('✅ Proceso completado:');
        $this->table(
            ['Resultado', 'Cantidad'],
            [
                ['Carnets creados', $created],
                ['Jugadores saltados', $skipped],
                ['Errores', $errors],
                ['Total procesados', $players->count()]
            ]
        );
        
        if ($created > 0) {
            $this->info("🎉 Se crearon {$created} carnets exitosamente.");
        }
        
        if ($errors > 0) {
            $this->warn("⚠️  Se encontraron {$errors} errores. Revisa los logs para más detalles.");
            return 1;
        }
        
        return 0;
    }
}