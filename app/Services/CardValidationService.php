<?php

namespace App\Services;

use App\Models\Player;
use App\Models\League;
use App\Models\PlayerCard;
use App\Enums\UserStatus;
use App\Enums\MedicalStatus;
use App\Enums\CardStatus;
use App\Enums\PlayerCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CardValidationService
{
    public function __construct(
        protected CategoryAssignmentService $categoryAssignmentService
    ) {}

    /**
     * Helper para obtener la categoría como string
     */
    private function getCategoryAsString($category): ?string
    {
        if ($category === null) {
            return null;
        }

        return is_object($category) ? $category->value : (string) $category;
    }

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
        $warnings = [];
        $user = $player->user;

        // Validar categoría usando sistema dinámico
        $categoryValidation = $this->validatePlayerCategory($player, $league);
        if (!$categoryValidation->isValid()) {
            $errors = array_merge($errors, $categoryValidation->getErrors());
        }
        $warnings = array_merge($warnings, $categoryValidation->getWarnings());

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

        // Validar que la categoría esté activa en la liga
        if ($player->category) {
            $categoryActiveValidation = $this->validateCategoryIsActive($player, $league);
            if (!$categoryActiveValidation->isValid()) {
                $errors = array_merge($errors, $categoryActiveValidation->getErrors());
            }
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
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
     * Validar categoría de jugadora usando sistema dinámico
     */
    private function validatePlayerCategory(Player $player, League $league): ValidationResult
    {
        $errors = [];
        $warnings = [];
        $user = $player->user;

        try {
            // Verificar que tenga fecha de nacimiento para calcular edad
            if (!$user->birth_date) {
                $errors[] = 'Fecha de nacimiento requerida para validar categoría';
                return new ValidationResult(false, $errors, $warnings);
            }

            // Obtener categoría actual de la jugadora
            $currentCategory = $player->category;
            if (!$currentCategory) {
                $errors[] = 'La jugadora debe tener una categoría asignada';
                return new ValidationResult(false, $errors, $warnings);
            }

            // Obtener categoría sugerida por el sistema de asignación automática
            $suggestedCategory = $this->categoryAssignmentService->assignAutomaticCategory($player);

            if (!$suggestedCategory) {
                $errors[] = 'No se pudo determinar una categoría válida para la jugadora';
                return new ValidationResult(false, $errors, $warnings);
            }

            // Comparar categoría actual con la sugerida
            $currentCategoryValue = is_object($currentCategory) ? $currentCategory->value : $currentCategory;

            if ($currentCategoryValue !== $suggestedCategory) {
                // Validar si el cambio es crítico o solo una advertencia
                $changeValidation = $this->categoryAssignmentService->validateCategoryChange($player, $suggestedCategory);

                if (!empty($changeValidation['errors'])) {
                    $errors[] = "Categoría incorrecta. Debería ser: {$suggestedCategory}";
                    $errors = array_merge($errors, $changeValidation['errors']);
                } else {
                    $warnings[] = "Categoría sugerida: {$suggestedCategory} (actual: {$currentCategoryValue})";
                    $warnings = array_merge($warnings, $changeValidation['warnings'] ?? []);
                }
            }

            // Validar específicamente contra configuración de liga si existe
            if ($league->hasCustomCategories()) {
                $dynamicValidation = $this->validateAgainstLeagueConfiguration($player, $league);
                if (!$dynamicValidation->isValid()) {
                    $errors = array_merge($errors, $dynamicValidation->getErrors());
                }
                $warnings = array_merge($warnings, $dynamicValidation->getWarnings());
            }

            Log::info('Validación de categoría completada', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'current_category' => $currentCategoryValue,
                'suggested_category' => $suggestedCategory,
                'errors_count' => count($errors),
                'warnings_count' => count($warnings)
            ]);

        } catch (\Exception $e) {
            Log::error('Error validando categoría de jugadora', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $errors[] = 'Error interno validando categoría de la jugadora';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
        );
    }

    /**
     * Validar contra configuración específica de la liga
     */
    private function validateAgainstLeagueConfiguration(Player $player, League $league): ValidationResult
    {
        $errors = [];
        $warnings = [];
        $user = $player->user;

        try {
            $age = $user->age ?? 0;
            $gender = $user->gender ? $user->gender->value : 'unknown';
            $currentCategory = $this->getCategoryAsString($player->category);

            // Buscar la categoría en la configuración de la liga
            $leagueCategory = $league->categories()
                ->where('name', $currentCategory)
                ->orWhere('code', $currentCategory)
                ->first();

            if (!$leagueCategory) {
                $errors[] = "La categoría '{$currentCategory}' no está configurada en esta liga";

                // Sugerir categorías disponibles
                $availableCategories = $league->getActiveCategories()->pluck('name')->toArray();
                if (!empty($availableCategories)) {
                    $warnings[] = 'Categorías disponibles en esta liga: ' . implode(', ', $availableCategories);
                }

                return new ValidationResult(false, $errors, $warnings);
            }

            // Validar que la categoría esté activa
            if (!$leagueCategory->is_active) {
                $errors[] = "La categoría '{$currentCategory}' no está activa en esta liga";
                return new ValidationResult(false, $errors, $warnings);
            }

            // Validar elegibilidad por edad
            if (!$leagueCategory->isAgeEligible($age)) {
                $errors[] = "La edad de la jugadora ({$age} años) no corresponde al rango de la categoría {$leagueCategory->name} ({$leagueCategory->getAgeRangeText()})";
            }

            // Validar elegibilidad por género
            $leagueCategoryGender = $leagueCategory->gender ? $leagueCategory->gender->value : 'mixed';
            if ($leagueCategoryGender !== 'mixed' && $leagueCategoryGender !== $gender) {
                $errors[] = "El género de la jugadora no coincide con las restricciones de la categoría {$leagueCategory->name}";
            }

            // Validar reglas especiales si existen
            if ($leagueCategory->hasSpecialRules()) {
                $specialRulesValidation = $this->validateSpecialRules($player, $leagueCategory);
                if (!$specialRulesValidation->isValid()) {
                    $errors = array_merge($errors, $specialRulesValidation->getErrors());
                }
                $warnings = array_merge($warnings, $specialRulesValidation->getWarnings());
            }

        } catch (\Exception $e) {
            Log::error('Error validando contra configuración de liga', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);
            $errors[] = 'Error validando configuración específica de la liga';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
        );
    }

    /**
     * Validar que la categoría esté activa en la liga
     */
    private function validateCategoryIsActive(Player $player, League $league): ValidationResult
    {
        $errors = [];
        $currentCategory = $this->getCategoryAsString($player->category);

        try {
            // Si la liga tiene configuración dinámica, verificar que la categoría esté activa
            if ($league->hasCustomCategories()) {
                $leagueCategory = $league->categories()
                    ->where('name', $currentCategory)
                    ->orWhere('code', $currentCategory)
                    ->where('is_active', true)
                    ->first();

                if (!$leagueCategory) {
                    $errors[] = "La categoría '{$currentCategory}' no está disponible para nuevos carnets en esta liga";
                }
            } else {
                // Para sistema tradicional, verificar que sea una categoría válida del enum
                $playerCategory = PlayerCategory::tryFrom($currentCategory);
                if (!$playerCategory) {
                    $errors[] = "La categoría '{$currentCategory}' no es válida";
                }
            }

        } catch (\Exception $e) {
            Log::error('Error validando estado activo de categoría', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'category' => $currentCategory,
                'error' => $e->getMessage()
            ]);
            $errors[] = 'Error validando disponibilidad de la categoría';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors
        );
    }

    /**
     * Validar reglas especiales de una categoría
     */
    private function validateSpecialRules(Player $player, $leagueCategory): ValidationResult
    {
        $errors = [];
        $warnings = [];

        try {
            $specialRules = $leagueCategory->special_rules ?? [];

            if (empty($specialRules)) {
                return new ValidationResult(true, [], []);
            }

            foreach ($specialRules as $rule) {
                $ruleValidation = $this->validateIndividualSpecialRule($player, $rule);
                if (!$ruleValidation->isValid()) {
                    $errors = array_merge($errors, $ruleValidation->getErrors());
                }
                $warnings = array_merge($warnings, $ruleValidation->getWarnings());
            }

        } catch (\Exception $e) {
            Log::error('Error validando reglas especiales', [
                'player_id' => $player->id,
                'category' => $leagueCategory->name,
                'error' => $e->getMessage()
            ]);
            $errors[] = 'Error validando reglas especiales de la categoría';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
        );
    }

    /**
     * Validar una regla especial individual
     */
    private function validateIndividualSpecialRule(Player $player, array $rule): ValidationResult
    {
        $errors = [];
        $warnings = [];
        $user = $player->user;

        $ruleType = $rule['type'] ?? '';
        $age = $user->age ?? 0;
        $gender = $user->gender ? $user->gender->value : 'unknown';

        switch ($ruleType) {
            case 'requires_medical_clearance':
                if ($rule['value'] === true && $player->medical_status !== MedicalStatus::Fit) {
                    $errors[] = 'Esta categoría requiere certificado médico especial vigente';
                }
                break;

            case 'minimum_experience_months':
                $requiredMonths = $rule['value'] ?? 0;
                // TODO: Implementar cuando se tenga sistema de experiencia
                $warnings[] = "Esta categoría requiere mínimo {$requiredMonths} meses de experiencia";
                break;

            case 'max_players_per_team':
                $maxPlayers = $rule['value'] ?? 0;
                if ($maxPlayers > 0) {
                    $currentPlayers = $player->currentClub->players()
                        ->where('category', $player->category)
                        ->count();

                    if ($currentPlayers >= $maxPlayers) {
                        $errors[] = "Esta categoría tiene un límite de {$maxPlayers} jugadoras por equipo";
                    }
                }
                break;

            case 'tournament_eligibility':
                $eligibleTournaments = $rule['value'] ?? [];
                if (!empty($eligibleTournaments)) {
                    $warnings[] = 'Esta categoría solo es elegible para torneos: ' . implode(', ', $eligibleTournaments);
                }
                break;

            case 'age_override':
                if (isset($rule['min_age_override']) && $age < $rule['min_age_override']) {
                    $errors[] = "Edad mínima requerida para esta categoría: {$rule['min_age_override']} años";
                }
                if (isset($rule['max_age_override']) && $age > $rule['max_age_override']) {
                    $errors[] = "Edad máxima permitida para esta categoría: {$rule['max_age_override']} años";
                }
                break;

            case 'gender_restriction':
                $allowedGenders = $rule['allowed_genders'] ?? ['male', 'female'];
                if (!in_array($gender, $allowedGenders)) {
                    $errors[] = 'Esta categoría tiene restricciones de género específicas';
                }
                break;

            default:
                $warnings[] = "Regla especial no reconocida: {$ruleType}";
                break;
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
        );
    }

    /**
     * Validar elegibilidad completa para generación de carnet con categorías dinámicas
     */
    public function validateCategoryEligibility(Player $player, League $league): ValidationResult
    {
        $errors = [];
        $warnings = [];
        $user = $player->user;

        try {
            // Validación básica de datos requeridos
            if (!$user->birth_date) {
                $errors[] = 'Fecha de nacimiento requerida para validar elegibilidad de categoría';
                return new ValidationResult(false, $errors, $warnings);
            }

            if (!$user->gender) {
                $errors[] = 'Género requerido para validar elegibilidad de categoría';
                return new ValidationResult(false, $errors, $warnings);
            }

            $age = $user->age ?? 0;
            $gender = $user->gender->value;
            $currentCategory = $this->getCategoryAsString($player->category);

            // Si la liga tiene configuración dinámica, usar esa validación
            if ($league->hasCustomCategories()) {
                $dynamicValidation = $this->validateAgainstLeagueConfiguration($player, $league);
                $errors = array_merge($errors, $dynamicValidation->getErrors());
                $warnings = array_merge($warnings, $dynamicValidation->getWarnings());

                // Verificar si hay una categoría más apropiada
                $suggestedCategory = $this->categoryAssignmentService->assignAutomaticCategory($player);
                if ($suggestedCategory && $suggestedCategory !== $currentCategory) {
                    $warnings[] = "Categoría sugerida por el sistema: {$suggestedCategory}";
                }
            } else {
                // Validación tradicional con enum
                $enumValidation = $this->validateTraditionalCategory($player);
                $errors = array_merge($errors, $enumValidation->getErrors());
                $warnings = array_merge($warnings, $enumValidation->getWarnings());
            }

            Log::info('Validación de elegibilidad de categoría completada', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'current_category' => $currentCategory,
                'age' => $age,
                'gender' => $gender,
                'has_dynamic_categories' => $league->hasCustomCategories(),
                'errors_count' => count($errors),
                'warnings_count' => count($warnings)
            ]);

        } catch (\Exception $e) {
            Log::error('Error en validación de elegibilidad de categoría', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $errors[] = 'Error interno validando elegibilidad de categoría';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings,
            metadata: [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'category_validated' => $currentCategory,
                'validation_type' => $league->hasCustomCategories() ? 'dynamic' : 'traditional'
            ]
        );
    }

    /**
     * Validar categoría usando sistema tradicional (enum)
     */
    private function validateTraditionalCategory(Player $player): ValidationResult
    {
        $errors = [];
        $warnings = [];
        $user = $player->user;

        try {
            $currentCategory = $this->getCategoryAsString($player->category);

            // Verificar que la categoría sea válida en el enum
            $playerCategory = PlayerCategory::tryFrom($currentCategory);
            if (!$playerCategory) {
                $errors[] = "La categoría '{$currentCategory}' no es válida en el sistema";
                return new ValidationResult(false, $errors, $warnings);
            }

            // Obtener categoría sugerida por edad
            $suggestedCategory = $this->categoryAssignmentService->assignAutomaticCategory($player);
            if ($suggestedCategory && $suggestedCategory !== $currentCategory) {
                $warnings[] = "Categoría sugerida por edad: {$suggestedCategory} (actual: {$currentCategory})";
            }

        } catch (\Exception $e) {
            Log::error('Error validando categoría tradicional', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);
            $errors[] = 'Error validando categoría con sistema tradicional';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
        );
    }

    /**
     * Obtener información detallada de validación para debugging
     */
    public function getValidationDetails(Player $player, League $league): array
    {
        $user = $player->user;
        $currentCategory = $this->getCategoryAsString($player->category);

        $details = [
            'player_info' => [
                'id' => $player->id,
                'name' => $user->full_name,
                'age' => $user->age ?? 0,
                'gender' => $user->gender ? $user->gender->value : 'unknown',
                'birth_date' => $user->birth_date?->format('Y-m-d'),
                'current_category' => $currentCategory
            ],
            'league_info' => [
                'id' => $league->id,
                'name' => $league->name,
                'has_custom_categories' => $league->hasCustomCategories()
            ],
            'validation_results' => [],
            'category_suggestions' => []
        ];

        try {
            // Ejecutar validación completa
            $validationResult = $this->validateCategoryEligibility($player, $league);
            $details['validation_results'] = $validationResult->toArray();

            // Obtener sugerencia de categoría
            $suggestedCategory = $this->categoryAssignmentService->assignAutomaticCategory($player);
            if ($suggestedCategory) {
                $details['category_suggestions']['automatic'] = $suggestedCategory;
            }

            // Si hay configuración dinámica, obtener categorías disponibles
            if ($league->hasCustomCategories()) {
                $availableCategories = $league->getActiveCategories();
                $details['available_categories'] = $availableCategories->map(function ($category) use ($user) {
                    return [
                        'name' => $category->name,
                        'key' => $category->code,
                        'age_range' => $category->getAgeRangeText(),
                        'gender' => $category->gender ? $category->gender->value : 'mixed',
                        'is_eligible' => $category->isAgeEligible($user->age ?? 0),
                        'has_special_rules' => $category->hasSpecialRules()
                    ];
                })->toArray();
            }

        } catch (\Exception $e) {
            $details['error'] = $e->getMessage();
            Log::error('Error obteniendo detalles de validación', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);
        }

        return $details;
    }

    /**
     * Validar específicamente para generación de carnets con categorías dinámicas
     */
    public function validateForCardGeneration(Player $player, League $league): ValidationResult
    {
        $errors = [];
        $warnings = [];
        $user = $player->user;

        try {
            // Validación básica de datos requeridos para carnet
            if (!$user->birth_date) {
                $errors[] = 'Fecha de nacimiento requerida para generar carnet';
                return new ValidationResult(false, $errors, $warnings);
            }

            if (!$user->gender) {
                $errors[] = 'Género requerido para generar carnet';
                return new ValidationResult(false, $errors, $warnings);
            }

            if (!$player->category) {
                $errors[] = 'La jugadora debe tener una categoría asignada para generar carnet';
                return new ValidationResult(false, $errors, $warnings);
            }

            $age = $user->age ?? 0;
            $currentCategory = $this->getCategoryAsString($player->category);

            // Validar usando configuración dinámica si está disponible
            if ($league->hasCustomCategories()) {
                $dynamicValidation = $this->validateCategoryForCardGeneration($player, $league);
                $errors = array_merge($errors, $dynamicValidation->getErrors());
                $warnings = array_merge($warnings, $dynamicValidation->getWarnings());
            } else {
                // Validación tradicional
                $traditionalValidation = $this->validateTraditionalCategoryForCard($player);
                $errors = array_merge($errors, $traditionalValidation->getErrors());
                $warnings = array_merge($warnings, $traditionalValidation->getWarnings());
            }

            // Validar que la categoría esté disponible para nuevos carnets
            $availabilityValidation = $this->validateCategoryAvailabilityForCards($player, $league);
            if (!$availabilityValidation->isValid()) {
                $errors = array_merge($errors, $availabilityValidation->getErrors());
            }

            Log::info('Validación para generación de carnet completada', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'category' => $currentCategory,
                'age' => $age,
                'has_dynamic_categories' => $league->hasCustomCategories(),
                'errors_count' => count($errors),
                'warnings_count' => count($warnings)
            ]);

        } catch (\Exception $e) {
            Log::error('Error en validación para generación de carnet', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $errors[] = 'Error interno validando elegibilidad para carnet';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings,
            metadata: [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'category_validated' => $currentCategory,
                'validation_type' => $league->hasCustomCategories() ? 'dynamic' : 'traditional',
                'validated_for' => 'card_generation'
            ]
        );
    }

    /**
     * Validar categoría específicamente para generación de carnets con configuración dinámica
     */
    private function validateCategoryForCardGeneration(Player $player, League $league): ValidationResult
    {
        $errors = [];
        $warnings = [];
        $user = $player->user;

        try {
            $age = $user->age ?? 0;
            $gender = $user->gender->value;
            $currentCategory = $this->getCategoryAsString($player->category);

            // Buscar la categoría en la configuración de la liga
            $leagueCategory = $league->categories()
                ->where('name', $currentCategory)
                ->orWhere('code', $currentCategory)
                ->where('is_active', true)
                ->first();

            if (!$leagueCategory) {
                $errors[] = "No se puede generar carnet: la categoría '{$currentCategory}' no está disponible en esta liga";

                // Sugerir categoría correcta
                $suggestedCategory = $this->categoryAssignmentService->assignAutomaticCategory($player);
                if ($suggestedCategory) {
                    $warnings[] = "Categoría sugerida: {$suggestedCategory}";
                }

                return new ValidationResult(false, $errors, $warnings);
            }

            // Validar elegibilidad por edad
            if (!$leagueCategory->isAgeEligible($age)) {
                $errors[] = "No se puede generar carnet: la edad de la jugadora ({$age} años) no es elegible para la categoría {$leagueCategory->name} (rango: {$leagueCategory->getAgeRangeText()})";
            }

            // Validar elegibilidad por género
            $leagueCategoryGender = $leagueCategory->gender ? $leagueCategory->gender->value : 'mixed';
            if ($leagueCategoryGender !== 'mixed' && $leagueCategoryGender !== $gender) {
                $errors[] = "No se puede generar carnet: el género de la jugadora no es elegible para la categoría {$leagueCategory->name}";
            }

            // Validar reglas especiales para generación de carnets
            if ($leagueCategory->hasSpecialRules()) {
                $specialRulesValidation = $this->validateSpecialRulesForCardGeneration($player, $leagueCategory);
                if (!$specialRulesValidation->isValid()) {
                    $errors = array_merge($errors, $specialRulesValidation->getErrors());
                }
                $warnings = array_merge($warnings, $specialRulesValidation->getWarnings());
            }

            // Verificar si hay una categoría más apropiada
            $suggestedCategory = $this->categoryAssignmentService->assignAutomaticCategory($player);
            if ($suggestedCategory && $suggestedCategory !== $currentCategory) {
                $warnings[] = "El sistema sugiere la categoría: {$suggestedCategory} (actual: {$currentCategory})";
            }

        } catch (\Exception $e) {
            Log::error('Error validando categoría para generación de carnet', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);
            $errors[] = 'Error validando configuración de categoría para carnet';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
        );
    }

    /**
     * Validar reglas especiales específicamente para generación de carnets
     */
    private function validateSpecialRulesForCardGeneration(Player $player, $leagueCategory): ValidationResult
    {
        $errors = [];
        $warnings = [];

        try {
            $specialRules = $leagueCategory->special_rules ?? [];

            if (empty($specialRules)) {
                return new ValidationResult(true, [], []);
            }

            foreach ($specialRules as $rule) {
                $ruleValidation = $this->validateSpecialRuleForCard($player, $rule);
                if (!$ruleValidation->isValid()) {
                    $errors = array_merge($errors, $ruleValidation->getErrors());
                }
                $warnings = array_merge($warnings, $ruleValidation->getWarnings());
            }

        } catch (\Exception $e) {
            Log::error('Error validando reglas especiales para carnet', [
                'player_id' => $player->id,
                'category' => $leagueCategory->name,
                'error' => $e->getMessage()
            ]);
            $errors[] = 'Error validando reglas especiales de la categoría';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
        );
    }

    /**
     * Validar una regla especial individual para generación de carnets
     */
    private function validateSpecialRuleForCard(Player $player, array $rule): ValidationResult
    {
        $errors = [];
        $warnings = [];
        $user = $player->user;

        $ruleType = $rule['type'] ?? '';

        switch ($ruleType) {
            case 'requires_medical_clearance':
                if ($rule['value'] === true && $player->medical_status !== MedicalStatus::Fit) {
                    $errors[] = 'Esta categoría requiere certificado médico especial vigente para generar carnet';
                }
                break;

            case 'blocks_card_generation':
                if ($rule['value'] === true) {
                    $errors[] = 'Esta categoría no permite generación automática de carnets';
                }
                break;

            case 'requires_manual_approval':
                if ($rule['value'] === true) {
                    $warnings[] = 'Esta categoría requiere aprobación manual para generar carnets';
                }
                break;

            case 'minimum_experience_months':
                $requiredMonths = $rule['value'] ?? 0;
                $warnings[] = "Esta categoría requiere mínimo {$requiredMonths} meses de experiencia para carnets";
                break;

            case 'max_cards_per_season':
                $maxCards = $rule['value'] ?? 0;
                if ($maxCards > 0) {
                    $warnings[] = "Esta categoría tiene límite de {$maxCards} carnets por temporada";
                }
                break;

            case 'tournament_eligibility':
                $eligibleTournaments = $rule['value'] ?? [];
                if (!empty($eligibleTournaments)) {
                    $warnings[] = 'Carnet válido solo para torneos: ' . implode(', ', $eligibleTournaments);
                }
                break;

            default:
                $warnings[] = "Regla especial no reconocida para carnets: {$ruleType}";
                break;
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
        );
    }

    /**
     * Validar categoría tradicional específicamente para carnets
     */
    private function validateTraditionalCategoryForCard(Player $player): ValidationResult
    {
        $errors = [];
        $warnings = [];
        $user = $player->user;

        try {
            $currentCategory = $this->getCategoryAsString($player->category);

            // Verificar que la categoría sea válida en el enum
            $playerCategory = PlayerCategory::tryFrom($currentCategory);
            if (!$playerCategory) {
                $errors[] = "No se puede generar carnet: la categoría '{$currentCategory}' no es válida";
                return new ValidationResult(false, $errors, $warnings);
            }

            // Obtener categoría sugerida por edad y comparar
            $suggestedCategory = $this->categoryAssignmentService->assignAutomaticCategory($player);
            if ($suggestedCategory && $suggestedCategory !== $currentCategory) {
                $warnings[] = "Categoría sugerida por edad: {$suggestedCategory} (actual: {$currentCategory})";

                // Si la diferencia es significativa, convertir en error
                $age = $user->age ?? 0;
                if ($this->isCriticalCategoryMismatch($currentCategory, $suggestedCategory, $age)) {
                    $errors[] = "No se puede generar carnet: la categoría actual ({$currentCategory}) no corresponde a la edad de la jugadora. Categoría correcta: {$suggestedCategory}";
                }
            }

        } catch (\Exception $e) {
            Log::error('Error validando categoría tradicional para carnet', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);
            $errors[] = 'Error validando categoría tradicional para carnet';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
        );
    }

    /**
     * Validar disponibilidad de categoría para nuevos carnets
     */
    private function validateCategoryAvailabilityForCards(Player $player, League $league): ValidationResult
    {
        $errors = [];
        $currentCategory = $this->getCategoryAsString($player->category);

        try {
            if ($league->hasCustomCategories()) {
                // Verificar que la categoría esté activa para nuevos carnets
                $leagueCategory = $league->categories()
                    ->where('name', $currentCategory)
                    ->orWhere('code', $currentCategory)
                    ->where('is_active', true)
                    ->first();

                if (!$leagueCategory) {
                    $errors[] = "La categoría '{$currentCategory}' no está disponible para generar nuevos carnets en esta liga";
                }
            }

        } catch (\Exception $e) {
            Log::error('Error validando disponibilidad de categoría para carnets', [
                'player_id' => $player->id,
                'league_id' => $league->id,
                'category' => $currentCategory,
                'error' => $e->getMessage()
            ]);
            $errors[] = 'Error validando disponibilidad de categoría para carnets';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors
        );
    }

    /**
     * Validar reglas especiales específicamente para generación de carnets
     */
    private function validateSpecialRulesForCard(Player $player, $leagueCategory): ValidationResult
    {
        $errors = [];
        $warnings = [];

        try {
            $specialRules = $leagueCategory->special_rules ?? [];

            if (empty($specialRules)) {
                return new ValidationResult(true, [], []);
            }

            foreach ($specialRules as $rule) {
                $ruleValidation = $this->validateSpecialRuleForCard($player, $rule);
                if (!$ruleValidation->isValid()) {
                    $errors = array_merge($errors, $ruleValidation->getErrors());
                }
                $warnings = array_merge($warnings, $ruleValidation->getWarnings());
            }

        } catch (\Exception $e) {
            Log::error('Error validando reglas especiales para carnet', [
                'player_id' => $player->id,
                'category' => $leagueCategory->name,
                'error' => $e->getMessage()
            ]);
            $errors[] = 'Error validando reglas especiales para generación de carnet';
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings
        );
    }



    /**
     * Determinar si una diferencia de categoría es crítica para carnets
     */
    private function isCriticalCategoryMismatch(string $current, string $suggested, int $age): bool
    {
        // Para carnets, somos más estrictos con las diferencias de categoría
        $criticalMismatches = [
            'Mini' => ['Cadete', 'Juvenil', 'Mayores', 'Masters'],
            'Pre_Mini' => ['Juvenil', 'Mayores', 'Masters'],
            'Infantil' => ['Mayores', 'Masters'],
            'Cadete' => ['Mini', 'Masters'],
            'Juvenil' => ['Mini', 'Pre_Mini'],
            'Mayores' => ['Mini', 'Pre_Mini', 'Infantil'],
            'Masters' => ['Mini', 'Pre_Mini', 'Infantil', 'Cadete']
        ];

        return isset($criticalMismatches[$current]) && in_array($suggested, $criticalMismatches[$current]);
    }

    /**
     * Calcular categoría por edad (método legacy mantenido para compatibilidad)
     * @deprecated Usar CategoryAssignmentService::assignAutomaticCategory() en su lugar
     */
    private function calculateCategoryByAge($birthDate)
    {
        $age = $birthDate->diffInYears(now());

        return match (true) {
            $age >= 8 && $age <= 10 => PlayerCategory::Mini,
            $age >= 11 && $age <= 12 => PlayerCategory::Pre_Mini,
            $age >= 13 && $age <= 14 => PlayerCategory::Infantil,
            $age >= 15 && $age <= 16 => PlayerCategory::Cadete,
            $age >= 17 && $age <= 18 => PlayerCategory::Juvenil,
            $age >= 19 && $age <= 34 => PlayerCategory::Mayores,
            $age >= 35 => PlayerCategory::Masters,
            default => PlayerCategory::Mayores
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

    public function getWarningMessage(): string
    {
        return implode('; ', $this->warnings);
    }

    public function hasWarnings(): bool
    {
        return !empty($this->warnings);
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

    /**
     * Crear resultado exitoso con posibles advertencias
     */
    public static function success(array $warnings = [], array $metadata = []): self
    {
        return new self(true, [], $warnings, $metadata);
    }

    /**
     * Crear resultado fallido con errores
     */
    public static function failure(array $errors, array $warnings = [], array $metadata = []): self
    {
        return new self(false, $errors, $warnings, $metadata);
    }
}
