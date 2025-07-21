<?php

namespace App\Services;

use App\Models\Player;
use App\Models\League;
use App\Models\PlayerCard;
use App\Enums\UserStatus;
use App\Enums\MedicalStatus;
use App\Enums\CardStatus;
use Illuminate\Support\Collection;

class CardValidationService
{
    /**
     * Validar si una jugadora puede tener un carnet generado
     */
    public function validateForGeneration(Player $player, League $league): ValidationResult
    {
        $errors = [];
        $warnings = [];

        // Validaciones de documentos
        $documentValidation = $this->validateDocuments($player);
        if (!$documentValidation->isValid()) {
            $errors = array_merge($errors, $documentValidation->getErrors());
        }

        // Validaciones de datos personales
        $personalDataValidation = $this->validatePersonalData($player);
        if (!$personalDataValidation->isValid()) {
            $errors = array_merge($errors, $personalDataValidation->getErrors());
        }

        // Validaciones deportivas
        $sportsDataValidation = $this->validateSportsData($player, $league);
        if (!$sportsDataValidation->isValid()) {
            $errors = array_merge($errors, $sportsDataValidation->getErrors());
        }

        // Validaciones del sistema
        $systemValidation = $this->validateSystemIntegrity($player, $league);
        if (!$systemValidation->isValid()) {
            $errors = array_merge($errors, $systemValidation->getErrors());
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings,
            metadata: [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'validated_at' => now()->toISOString()
            ]
        );
    }

    /**
     * Validar documentos obligatorios
     */
    public function validateDocuments(Player $player): ValidationResult
    {
        $errors = [];
        $user = $player->user;

        // Documento de identidad
        if (empty($user->document_number)) {
            $errors[] = 'Número de documento de identidad requerido';
        }

        // Fecha de nacimiento para verificar edad
        if (!$user->birth_date) {
            $errors[] = 'Fecha de nacimiento requerida';
        }

        // Verificar que tenga foto de perfil
        if (!$user->hasMedia('avatar')) {
            $errors[] = 'Fotografía reciente requerida';
        }

        // Certificado médico vigente
        $medicalCertificate = $this->getValidMedicalCertificate($player);
        if (!$medicalCertificate) {
            $errors[] = 'Certificado médico vigente requerido (no mayor a 6 meses)';
        }

        // Para menores de edad, autorización parental
        if ($user->age && $user->age < 18) {
            if (!$user->hasMedia('documents')) {
                $errors[] = 'Autorización parental requerida para menores de edad';
            }
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors
        );
    }

    /**
     * Validar datos personales
     */
    public function validatePersonalData(Player $player): ValidationResult
    {
        $errors = [];
        $user = $player->user;

        // Nombres completos
        if (empty($user->first_name) || empty($user->last_name)) {
            $errors[] = 'Nombres y apellidos completos requeridos';
        }

        // Validar caracteres especiales en nombres
        if ($user->first_name && preg_match('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/', $user->first_name)) {
            $errors[] = 'El nombre contiene caracteres no válidos';
        }

        if ($user->last_name && preg_match('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/', $user->last_name)) {
            $errors[] = 'Los apellidos contienen caracteres no válidos';
        }

        // Fecha de nacimiento coherente
        if ($user->birth_date) {
            $age = $user->birth_date->diffInYears(now());
            if ($age < 8 || $age > 80) {
                $errors[] = 'Edad fuera del rango válido (8-80 años)';
            }
        }

        // Género corresponde con competencias
        if (!$user->gender) {
            $errors[] = 'Género requerido';
        }

        // Documento único en el sistema
        if ($user->document_number) {
            $duplicateUser = \App\Models\User::where('document_number', $user->document_number)
                ->where('id', '!=', $user->id)
                ->first();

            if ($duplicateUser) {
                $errors[] = 'Número de documento ya registrado en el sistema';
            }
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors
        );
    }

    /**
     * Validar información deportiva
     */
    public function validateSportsData(Player $player, League $league): ValidationResult
    {
        $errors = [];
        $user = $player->user;

        // Categoría por edad automática
        if ($user->birth_date) {
            $expectedCategory = $this->calculateCategoryByAge($user->birth_date);
            if ($player->category !== $expectedCategory) {
                $errors[] = "Categoría incorrecta. Debería ser: {$expectedCategory->getLabel()}";
            }
        }

        // Posición de juego válida
        if (!$player->position) {
            $errors[] = 'Posición de juego requerida';
        }

        // Club activo y en buena situación
        if (!$player->currentClub) {
            $errors[] = 'Club actual requerido';
        } elseif (!$player->currentClub->is_active) {
            $errors[] = 'El club actual no está activo';
        }

        // Liga corresponde al club registrado
        if ($player->currentClub && $player->currentClub->league_id !== $league->id) {
            $errors[] = 'La liga no corresponde al club de la jugadora';
        }

        // Estado de la jugadora
        if ($user->status !== UserStatus::Active) {
            $errors[] = 'La jugadora debe estar en estado activo';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors
        );
    }

    /**
     * Validar integridad del sistema
     */
    public function validateSystemIntegrity(Player $player, League $league): ValidationResult
    {
        $errors = [];

        // No duplicación - verificar que no exista carnet activo previo
        $existingCard = PlayerCard::where('player_id', $player->id)
            ->where('league_id', $league->id)
            ->whereIn('status', [CardStatus::Active, CardStatus::Medical_Restriction])
            ->first();

        if ($existingCard) {
            $errors[] = "Ya existe un carnet activo para esta jugadora en la liga (#{$existingCard->card_number})";
        }

        // Verificar que no tenga sanciones vigentes
        // TODO: Implementar cuando se tenga el sistema de sanciones
        // $activeSanctions = $this->getActiveSanctions($player);
        // if ($activeSanctions->isNotEmpty()) {
        //     $errors[] = 'La jugadora tiene sanciones vigentes que impiden la generación del carnet';
        // }

        // Verificar que la liga esté activa
        if (!$league->is_active) {
            $errors[] = 'La liga no está activa';
        }

        // Verificar que el club pueda registrar jugadoras
        if ($player->currentClub && !$player->currentClub->canRegisterPlayers()) {
            $errors[] = 'El club no puede registrar jugadoras en este momento';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors
        );
    }

    /**
     * Obtener certificado médico válido
     */
    private function getValidMedicalCertificate(Player $player)
    {
        // TODO: Implementar cuando se tenga el sistema de certificados médicos
        // Por ahora, verificar que tenga estado médico apto
        return $player->medical_status === MedicalStatus::Fit;
    }

    /**
     * Calcular categoría por edad
     */
    private function calculateCategoryByAge($birthDate)
    {
        $age = $birthDate->diffInYears(now());

        return match (true) {
            $age >= 8 && $age <= 10 => \App\Enums\PlayerCategory::Mini,
            $age >= 11 && $age <= 12 => \App\Enums\PlayerCategory::Pre_Mini,
            $age >= 13 && $age <= 14 => \App\Enums\PlayerCategory::Infantil,
            $age >= 15 && $age <= 16 => \App\Enums\PlayerCategory::Cadete,
            $age >= 17 && $age <= 18 => \App\Enums\PlayerCategory::Juvenil,
            $age >= 19 && $age <= 34 => \App\Enums\PlayerCategory::Mayores,
            $age >= 35 => \App\Enums\PlayerCategory::Masters,
            default => \App\Enums\PlayerCategory::Mayores
        };
    }
}

/**
 * Clase para encapsular resultados de validación
 */
class ValidationResult
{
    public function __construct(
        private bool $valid,
        private array $errors = [],
        private array $warnings = [],
        private array $metadata = []
    ) {}

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getErrorMessage(): string
    {
        return implode('; ', $this->errors);
    }

    public function toArray(): array
    {
        return [
            'valid' => $this->valid,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'metadata' => $this->metadata
        ];
    }
}
