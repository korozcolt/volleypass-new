<?php

namespace App\Services;

use App\Models\LeagueConfiguration;
use App\Models\Player;
use App\Models\Club;
use App\Models\Tournament;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LeagueConfigurationService
{
    protected int $cacheMinutes = 60;

    // =======================
    // GESTIÓN DE CONFIGURACIONES
    // =======================

    public function get(int $league_id, string $key, $default = null)
    {
        $cacheKey = "league_config_{$league_id}_{$key}";

        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($league_id, $key, $default) {
            return LeagueConfiguration::get($league_id, $key, $default);
        });
    }

    public function set(int $league_id, string $key, $value): bool
    {
        $result = LeagueConfiguration::set($league_id, $key, $value);

        if ($result) {
            $this->clearCache($league_id, $key);
            Log::info("Liga configuración actualizada", [
                'league_id' => $league_id,
                'key' => $key,
                'value' => $value
            ]);
        }

        return $result;
    }

    public function getByGroup(int $league_id, string $group): array
    {
        $cacheKey = "league_config_group_{$league_id}_{$group}";

        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($league_id, $group) {
            return LeagueConfiguration::getByGroup($league_id, $group);
        });
    }

    public function getAllConfigurations(int $league_id): array
    {
        $cacheKey = "league_config_all_{$league_id}";

        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($league_id) {
            return LeagueConfiguration::where('league_id', $league_id)
                ->get()
                ->groupBy('group')
                ->map(function ($configs) {
                    return $configs->mapWithKeys(function ($config) {
                        return [$config->key => $config->typed_value];
                    });
                })
                ->toArray();
        });
    }

    // =======================
    // REGLAS DE TRASPASOS
    // =======================

    public function isTransferApprovalRequired(int $league_id): bool
    {
        return $this->get($league_id, 'transfer_approval_required', true);
    }

    public function getTransferTimeoutDays(int $league_id): int
    {
        return $this->get($league_id, 'transfer_timeout_days', 7);
    }

    public function getMaxTransfersPerSeason(int $league_id): int
    {
        return $this->get($league_id, 'max_transfers_per_season', 2);
    }

    public function isTransferWindowOpen(int $league_id): bool
    {
        $start = $this->get($league_id, 'transfer_window_start');
        $end = $this->get($league_id, 'transfer_window_end');

        if (!$start || !$end) {
            return true; // Si no hay ventana definida, siempre está abierta
        }

        $now = now();
        return $now->between($start, $end);
    }

    public function canRequestTransfer(int $player_id, int $to_club_id): array
    {
        $player = Player::find($player_id);
        $toClub = Club::find($to_club_id);

        if (!$player || !$toClub) {
            return ['can_transfer' => false, 'reason' => 'Jugadora o club no encontrado'];
        }

        $league_id = $toClub->league_id;

        // Verificar ventana de traspasos
        if (!$this->isTransferWindowOpen($league_id)) {
            return ['can_transfer' => false, 'reason' => 'Ventana de traspasos cerrada'];
        }

        // Verificar límite de traspasos por temporada
        $currentSeasonTransfers = $player->transfers()
            ->whereYear('created_at', now()->year)
            ->count();

        $maxTransfers = $this->getMaxTransfersPerSeason($league_id);
        if ($currentSeasonTransfers >= $maxTransfers) {
            return ['can_transfer' => false, 'reason' => "Límite de traspasos excedido ({$maxTransfers} por temporada)"];
        }

        // Verificar si hay traspasos pendientes
        $pendingTransfers = $player->transfers()
            ->whereIn('status', ['requested', 'under_review'])
            ->count();

        if ($pendingTransfers > 0) {
            return ['can_transfer' => false, 'reason' => 'Ya tiene un traspaso pendiente'];
        }

        return ['can_transfer' => true, 'reason' => null];
    }

    // =======================
    // REGLAS DE DOCUMENTACIÓN
    // =======================

    public function getDocumentStrictnessLevel(int $league_id): string
    {
        return $this->get($league_id, 'document_strictness_level', 'medium');
    }

    public function isMedicalCertificateRequired(int $league_id): bool
    {
        return $this->get($league_id, 'medical_certificate_required', true);
    }

    public function getMedicalValidityMonths(int $league_id): int
    {
        return $this->get($league_id, 'medical_validity_months', 6);
    }

    public function isParentAuthorizationRequired(int $league_id): bool
    {
        return $this->get($league_id, 'parent_authorization_under_18', true);
    }

    // =======================
    // REGLAS DE CATEGORÍAS
    // =======================

    public function isAgeVerificationStrict(int $league_id): bool
    {
        return $this->get($league_id, 'age_verification_strict', true);
    }

    public function isCategoryMixingAllowed(int $league_id): bool
    {
        return $this->get($league_id, 'category_mixing_allowed', false);
    }

    public function areGuestPlayersAllowed(int $league_id): bool
    {
        return $this->get($league_id, 'guest_players_allowed', true);
    }

    public function getMaxGuestPlayersPerMatch(int $league_id): int
    {
        return $this->get($league_id, 'max_guest_players_per_match', 2);
    }

    // =======================
    // REGLAS DE FEDERACIÓN
    // =======================

    public function isFederationRequiredForTournaments(int $league_id): bool
    {
        return $this->get($league_id, 'federation_required_for_tournaments', true);
    }

    public function getFederationGracePeriodDays(int $league_id): int
    {
        return $this->get($league_id, 'federation_grace_period_days', 30);
    }

    public function getFederationValidityMonths(int $league_id): int
    {
        return $this->get($league_id, 'federation_validity_months', 12);
    }

    // =======================
    // VALIDACIONES DE ELEGIBILIDAD
    // =======================

    public function validatePlayerEligibility(int $player_id, int $tournament_id): array
    {
        $player = Player::find($player_id);
        $tournament = Tournament::find($tournament_id);

        if (!$player || !$tournament) {
            return ['eligible' => false, 'reasons' => ['Jugadora o torneo no encontrado']];
        }

        $league_id = $tournament->league_id;
        $reasons = [];

        // Verificar federación si es requerida
        if ($this->isFederationRequiredForTournaments($league_id)) {
            if (!$player->club || $player->club->federation_type !== 'federated') {
                $reasons[] = 'El torneo requiere jugadoras de clubes federados';
            }

            if ($player->federation_status !== 'federated_active') {
                $reasons[] = 'La jugadora debe estar federada activa';
            }
        }

        // Verificar documentación médica
        if ($this->isMedicalCertificateRequired($league_id)) {
            $validityMonths = $this->getMedicalValidityMonths($league_id);
            $medicalCert = $player->medicalCertificates()
                ->where('status', 'approved')
                ->where('expires_at', '>', now())
                ->first();

            if (!$medicalCert) {
                $reasons[] = 'Certificado médico requerido y válido';
            }
        }

        // Verificar edad y categoría
        if ($this->isAgeVerificationStrict($league_id)) {
            $playerAge = $player->age;
            $tournamentCategory = $tournament->category;

            if (!$this->isPlayerAgeValidForCategory($playerAge, $tournamentCategory)) {
                if (!$this->isCategoryMixingAllowed($league_id)) {
                    $reasons[] = 'Edad no válida para la categoría del torneo';
                }
            }
        }

        return [
            'eligible' => empty($reasons),
            'reasons' => $reasons
        ];
    }

    // =======================
    // MÉTODOS AUXILIARES
    // =======================

    private function isPlayerAgeValidForCategory(int $age, string $category): bool
    {
        return match ($category) {
            'mini' => $age >= 8 && $age <= 10,
            'pre_mini' => $age >= 11 && $age <= 12,
            'infantil' => $age >= 13 && $age <= 14,
            'cadete' => $age >= 15 && $age <= 16,
            'juvenil' => $age >= 17 && $age <= 18,
            'mayores' => $age >= 19,
            'masters' => $age >= 35,
            default => true,
        };
    }

    public function clearCache(int $league_id, ?string $key = null): void
    {
        if ($key) {
            Cache::forget("league_config_{$league_id}_{$key}");
        } else {
            // Limpiar todo el cache de la liga
            $patterns = [
                "league_config_{$league_id}_*",
                "league_config_group_{$league_id}_*",
                "league_config_all_{$league_id}"
            ];

            foreach ($patterns as $pattern) {
                Cache::forget($pattern);
            }
        }
    }

    public function reload(int $league_id): void
    {
        $this->clearCache($league_id);
        Log::info("Cache de configuraciones de liga limpiado", ['league_id' => $league_id]);
    }

    // =======================
    // ESTADÍSTICAS
    // =======================

    public function getConfigurationStats(int $league_id): array
    {
        $configs = LeagueConfiguration::where('league_id', $league_id)->get();

        return [
            'total_configurations' => $configs->count(),
            'by_group' => $configs->groupBy('group')->map->count(),
            'public_configurations' => $configs->where('is_public', true)->count(),
            'last_updated' => $configs->max('updated_at'),
        ];
    }
}
