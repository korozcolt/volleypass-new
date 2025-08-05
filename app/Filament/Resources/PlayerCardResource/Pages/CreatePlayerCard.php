<?php

namespace App\Filament\Resources\PlayerCardResource\Pages;

use App\Filament\Resources\PlayerCardResource;
use App\Models\Player;
use App\Models\PlayerCard;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePlayerCard extends CreateRecord
{
    protected static string $resource = PlayerCardResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asignar usuario que emite el carnet
        $data['issued_by'] = Auth::id();
        $data['issued_at'] = now();
        
        // Si no se especifica número de carnet, generarlo automáticamente
        if (empty($data['card_number'])) {
            $player = Player::find($data['player_id']);
            if ($player) {
                $data['card_number'] = PlayerCard::generateCardNumber($player);
            }
        }
        
        // Asegurar que expires_at tenga un valor por defecto
        if (empty($data['expires_at'])) {
            $data['expires_at'] = now()->addYear();
        }
        
        // Asegurar que season tenga un valor
        if (empty($data['season'])) {
            $data['season'] = now()->year;
        }
        
        // Asegurar que version tenga un valor
        if (empty($data['version'])) {
            $data['version'] = 1;
        }
        
        // Asegurar que medical_status tenga un valor
        if (empty($data['medical_status'])) {
            $data['medical_status'] = \App\Enums\MedicalStatus::Fit;
        }
        
        // Generar QR code y verification token antes de crear el registro
        $tempCardNumber = $data['card_number'] ?? 'TEMP';
        $playerId = $data['player_id'];
        
        $data['qr_code'] = hash(
            'sha256',
            $tempCardNumber .
                $playerId .
                now()->timestamp .
                config('app.key')
        );
        
        $data['verification_token'] = hash(
            'sha256',
            $data['qr_code'] .
                $playerId .
                'verification_token'
        );

        return $data;
    }
}
