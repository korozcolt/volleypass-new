<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\League;
use App\Models\LeagueConfiguration;
use App\Enums\ConfigurationType;

class LeagueConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las ligas existentes
        $leagues = League::all();

        foreach ($leagues as $league) {
            $this->createConfigurationsForLeague($league->id);
        }
    }

    private function createConfigurationsForLeague(int $league_id): void
    {
        $configurations = [
            // =======================
            // CONFIGURACIONES DE TRASPASOS
            // =======================
            [
                'league_id' => $league_id,
                'key' => 'transfer_approval_required',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'transfers',
                'description' => 'Requiere aprobación de liga para traspasos',
                'is_public' => false,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'transfer_timeout_days',
                'value' => '7',
                'type' => ConfigurationType::NUMBER,
                'group' => 'transfers',
                'description' => 'Días para aprobar/rechazar traspasos',
                'is_public' => false,
                'validation_rules' => 'integer|min:1|max:30',
                'default_value' => '7',
            ],
            [
                'league_id' => $league_id,
                'key' => 'max_transfers_per_season',
                'value' => '2',
                'type' => ConfigurationType::NUMBER,
                'group' => 'transfers',
                'description' => 'Máximo traspasos por jugadora por temporada',
                'is_public' => true,
                'validation_rules' => 'integer|min:1|max:10',
                'default_value' => '2',
            ],
            [
                'league_id' => $league_id,
                'key' => 'transfer_window_start',
                'value' => '2024-01-01',
                'type' => ConfigurationType::DATE,
                'group' => 'transfers',
                'description' => 'Inicio de ventana de traspasos',
                'is_public' => true,
                'validation_rules' => 'date',
                'default_value' => '2024-01-01',
            ],
            [
                'league_id' => $league_id,
                'key' => 'transfer_window_end',
                'value' => '2024-03-31',
                'type' => ConfigurationType::DATE,
                'group' => 'transfers',
                'description' => 'Fin de ventana de traspasos',
                'is_public' => true,
                'validation_rules' => 'date|after:transfer_window_start',
                'default_value' => '2024-03-31',
            ],
            [
                'league_id' => $league_id,
                'key' => 'inter_league_transfers_allowed',
                'value' => '0',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'transfers',
                'description' => 'Permite traspasos entre ligas',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '0',
            ],

            // =======================
            // CONFIGURACIONES DE DOCUMENTACIÓN
            // =======================
            [
                'league_id' => $league_id,
                'key' => 'document_strictness_level',
                'value' => 'high',
                'type' => ConfigurationType::STRING,
                'group' => 'documentation',
                'description' => 'Nivel de exigencia documental (low, medium, high)',
                'is_public' => true,
                'validation_rules' => 'in:low,medium,high',
                'default_value' => 'medium',
            ],
            [
                'league_id' => $league_id,
                'key' => 'medical_certificate_required',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'documentation',
                'description' => 'Certificado médico obligatorio',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'medical_validity_months',
                'value' => '6',
                'type' => ConfigurationType::NUMBER,
                'group' => 'documentation',
                'description' => 'Meses de validez del certificado médico',
                'is_public' => true,
                'validation_rules' => 'integer|min:1|max:24',
                'default_value' => '6',
            ],
            [
                'league_id' => $league_id,
                'key' => 'photo_required',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'documentation',
                'description' => 'Fotografía obligatoria',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'parent_authorization_under_18',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'documentation',
                'description' => 'Autorización de padres para menores de 18',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'insurance_required',
                'value' => '0',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'documentation',
                'description' => 'Seguro deportivo obligatorio',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '0',
            ],

            // =======================
            // CONFIGURACIONES DE CATEGORÍAS
            // =======================
            [
                'league_id' => $league_id,
                'key' => 'age_verification_strict',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'categories',
                'description' => 'Verificación estricta de edad',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'category_mixing_allowed',
                'value' => '0',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'categories',
                'description' => 'Permite jugar en categoría superior',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '0',
            ],
            [
                'league_id' => $league_id,
                'key' => 'guest_players_allowed',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'categories',
                'description' => 'Permite jugadoras invitadas',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'max_guest_players_per_match',
                'value' => '2',
                'type' => ConfigurationType::NUMBER,
                'group' => 'categories',
                'description' => 'Máximo jugadoras invitadas por partido',
                'is_public' => true,
                'validation_rules' => 'integer|min:0|max:6',
                'default_value' => '2',
            ],

            // =======================
            // CONFIGURACIONES DISCIPLINARIAS
            // =======================
            [
                'league_id' => $league_id,
                'key' => 'yellow_card_accumulation_limit',
                'value' => '3',
                'type' => ConfigurationType::NUMBER,
                'group' => 'discipline',
                'description' => 'Límite de tarjetas amarillas acumuladas',
                'is_public' => true,
                'validation_rules' => 'integer|min:1|max:10',
                'default_value' => '3',
            ],
            [
                'league_id' => $league_id,
                'key' => 'suspension_games_per_red_card',
                'value' => '1',
                'type' => ConfigurationType::NUMBER,
                'group' => 'discipline',
                'description' => 'Partidos de suspensión por tarjeta roja',
                'is_public' => true,
                'validation_rules' => 'integer|min:1|max:5',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'appeal_process_enabled',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'discipline',
                'description' => 'Proceso de apelación habilitado',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'appeal_deadline_days',
                'value' => '3',
                'type' => ConfigurationType::NUMBER,
                'group' => 'discipline',
                'description' => 'Días límite para apelar sanciones',
                'is_public' => true,
                'validation_rules' => 'integer|min:1|max:15',
                'default_value' => '3',
            ],

            // =======================
            // CONFIGURACIONES DE FEDERACIÓN
            // =======================
            [
                'league_id' => $league_id,
                'key' => 'federation_required_for_tournaments',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'federation',
                'description' => 'Federación requerida para torneos oficiales',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'federation_grace_period_days',
                'value' => '30',
                'type' => ConfigurationType::NUMBER,
                'group' => 'federation',
                'description' => 'Días de gracia para federación vencida',
                'is_public' => true,
                'validation_rules' => 'integer|min:0|max:90',
                'default_value' => '30',
            ],
            [
                'league_id' => $league_id,
                'key' => 'federation_validity_months',
                'value' => '12',
                'type' => ConfigurationType::NUMBER,
                'group' => 'federation',
                'description' => 'Meses de validez de la federación',
                'is_public' => true,
                'validation_rules' => 'integer|min:1|max:24',
                'default_value' => '12',
            ],
            [
                'league_id' => $league_id,
                'key' => 'manual_approval_process',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'federation',
                'description' => 'Proceso de aprobación manual (sin pagos automáticos)',
                'is_public' => false,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],

            // =======================
            // CONFIGURACIONES PARA INTERFACES CRÍTICAS
            // =======================
            [
                'league_id' => $league_id,
                'key' => 'live_match_updates',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'tournament_interface',
                'description' => 'Actualizaciones en vivo para partidos',
                'is_public' => false,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'real_time_scoring',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'tournament_interface',
                'description' => 'Marcadores en tiempo real',
                'is_public' => false,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'player_eligibility_check',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'tournament_interface',
                'description' => 'Verificación de elegibilidad en partidos',
                'is_public' => false,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'referee_interface_enabled',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'tournament_interface',
                'description' => 'Interface para árbitros habilitada',
                'is_public' => false,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],

            // =======================
            // CONFIGURACIONES PARA VISTA PÚBLICA
            // =======================
            [
                'league_id' => $league_id,
                'key' => 'show_live_scores',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'public_data',
                'description' => 'Mostrar marcadores en vivo públicamente',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'show_team_rosters',
                'value' => '0',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'public_data',
                'description' => 'Mostrar nóminas de equipos públicamente',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '0',
            ],
            [
                'league_id' => $league_id,
                'key' => 'show_player_stats',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'public_data',
                'description' => 'Mostrar estadísticas de jugadoras públicamente',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'show_tournament_brackets',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'public_data',
                'description' => 'Mostrar llaves de torneos públicamente',
                'is_public' => true,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
            [
                'league_id' => $league_id,
                'key' => 'public_api_enabled',
                'value' => '1',
                'type' => ConfigurationType::BOOLEAN,
                'group' => 'public_data',
                'description' => 'API pública habilitada',
                'is_public' => false,
                'validation_rules' => 'boolean',
                'default_value' => '1',
            ],
        ];

        foreach ($configurations as $config) {
            LeagueConfiguration::updateOrCreate(
                [
                    'league_id' => $config['league_id'],
                    'key' => $config['key']
                ],
                $config
            );
        }

        $this->command->info("Configuraciones creadas para Liga ID: {$league_id}");
    }
}
