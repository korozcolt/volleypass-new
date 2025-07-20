<?php

if (!function_exists('system_config')) {
    /**
     * Obtener una configuración del sistema
     */
    function system_config(string $key, $default = null)
    {
        return app('system.config')->get($key, $default);
    }
}

if (!function_exists('set_system_config')) {
    /**
     * Establecer una configuración del sistema
     */
    function set_system_config(string $key, $value): bool
    {
        return app('system.config')->set($key, $value);
    }
}

if (!function_exists('system_config_group')) {
    /**
     * Obtener configuraciones por grupo
     */
    function system_config_group(string $group): array
    {
        return app('system.config')->getByGroup($group);
    }
}

if (!function_exists('app_name')) {
    /**
     * Obtener el nombre de la aplicación desde configuraciones del sistema
     */
    function app_name(): string
    {
        return system_config('app.name', config('app.name', 'VolleyPass'));
    }
}

if (!function_exists('app_description')) {
    /**
     * Obtener la descripción de la aplicación
     */
    function app_description(): string
    {
        return system_config('app.description', 'Sistema de Gestión Deportiva');
    }
}

if (!function_exists('app_version')) {
    /**
     * Obtener la versión de la aplicación
     */
    function app_version(): string
    {
        return system_config('app.version', '1.0.0');
    }
}

if (!function_exists('federation_fee')) {
    /**
     * Obtener la cuota de federación
     */
    function federation_fee(): float
    {
        return (float) system_config('federation.annual_fee', 50000);
    }
}

if (!function_exists('card_validity_months')) {
    /**
     * Obtener los meses de validez del carnet
     */
    function card_validity_months(): int
    {
        return (int) system_config('federation.card_validity_months', 12);
    }
}

if (!function_exists('max_upload_size')) {
    /**
     * Obtener el tamaño máximo de subida en MB
     */
    function max_upload_size(): int
    {
        return (int) system_config('files.max_upload_size', 10);
    }
}

if (!function_exists('allowed_file_extensions')) {
    /**
     * Obtener las extensiones de archivo permitidas
     */
    function allowed_file_extensions(): array
    {
        $extensions = system_config('files.allowed_extensions', ['jpg', 'jpeg', 'png', 'pdf']);
        return is_array($extensions) ? $extensions : json_decode($extensions, true) ?? ['jpg', 'jpeg', 'png', 'pdf'];
    }
}

if (!function_exists('is_maintenance_mode')) {
    /**
     * Verificar si está en modo mantenimiento
     */
    function is_maintenance_mode(): bool
    {
        return (bool) system_config('maintenance.mode', false);
    }
}

if (!function_exists('maintenance_message')) {
    /**
     * Obtener el mensaje de mantenimiento
     */
    function maintenance_message(): string
    {
        return system_config('maintenance.message', 'El sistema está en mantenimiento. Volveremos pronto.');
    }
}
// =======================
// LEAGUE CONFIGURATION HELPERS
// =======================

if (!function_exists('league_config')) {
    /**
     * Obtener una configuración específica de liga
     */
    function league_config(int $league_id, string $key, $default = null)
    {
        return app(\App\Services\LeagueConfigurationService::class)->get($league_id, $key, $default);
    }
}

if (!function_exists('club_is_federated')) {
    /**
     * Verificar si un club está federado
     */
    function club_is_federated(int $club_id): bool
    {
        $club = \App\Models\Club::find($club_id);
        return $club && $club->federation_type === 'federated';
    }
}

if (!function_exists('can_request_transfer')) {
    /**
     * Verificar si una jugadora puede solicitar traspaso
     */
    function can_request_transfer(int $player_id, int $to_club_id): array
    {
        return app(\App\Services\LeagueConfigurationService::class)->canRequestTransfer($player_id, $to_club_id);
    }
}

if (!function_exists('is_player_eligible_for_tournament')) {
    /**
     * Verificar elegibilidad de jugadora para torneo
     */
    function is_player_eligible_for_tournament(int $player_id, int $tournament_id): array
    {
        return app(\App\Services\LeagueConfigurationService::class)->validatePlayerEligibility($player_id, $tournament_id);
    }
}

if (!function_exists('is_transfer_window_open')) {
    /**
     * Verificar si la ventana de traspasos está abierta
     */
    function is_transfer_window_open(int $league_id): bool
    {
        return app(\App\Services\LeagueConfigurationService::class)->isTransferWindowOpen($league_id);
    }
}

if (!function_exists('get_league_transfer_rules')) {
    /**
     * Obtener todas las reglas de traspaso de una liga
     */
    function get_league_transfer_rules(int $league_id): array
    {
        $service = app(\App\Services\LeagueConfigurationService::class);

        return [
            'approval_required' => $service->isTransferApprovalRequired($league_id),
            'timeout_days' => $service->getTransferTimeoutDays($league_id),
            'max_per_season' => $service->getMaxTransfersPerSeason($league_id),
            'window_open' => $service->isTransferWindowOpen($league_id),
        ];
    }
}

if (!function_exists('get_league_document_requirements')) {
    /**
     * Obtener requisitos documentales de una liga
     */
    function get_league_document_requirements(int $league_id): array
    {
        $service = app(\App\Services\LeagueConfigurationService::class);

        return [
            'strictness_level' => $service->getDocumentStrictnessLevel($league_id),
            'medical_required' => $service->isMedicalCertificateRequired($league_id),
            'medical_validity_months' => $service->getMedicalValidityMonths($league_id),
            'parent_authorization_required' => $service->isParentAuthorizationRequired($league_id),
        ];
    }
}

if (!function_exists('get_league_federation_rules')) {
    /**
     * Obtener reglas de federación de una liga
     */
    function get_league_federation_rules(int $league_id): array
    {
        $service = app(\App\Services\LeagueConfigurationService::class);

        return [
            'required_for_tournaments' => $service->isFederationRequiredForTournaments($league_id),
            'grace_period_days' => $service->getFederationGracePeriodDays($league_id),
            'validity_months' => $service->getFederationValidityMonths($league_id),
        ];
    }
}
// =======================
// ADAPTIVE LOGO HELPERS
// =======================

if (!function_exists('adaptive_logo')) {
    /**
     * Obtener HTML de logo adaptativo para modo claro/oscuro
     */
    function adaptive_logo($model, string $classes = 'w-8 h-8', ?string $alt = null): string
    {
        if (!method_exists($model, 'getLogoUrl')) {
            return '';
        }

        $lightLogo = $model->getLogoUrl('light');
        $darkLogo = $model->getLogoUrl('dark');
        $alt = $alt ?: ($model->name ?? 'Logo');

        if ($lightLogo && $darkLogo) {
            return "
                <img src='{$lightLogo}' alt='{$alt}' class='{$classes} block dark:hidden' />
                <img src='{$darkLogo}' alt='{$alt}' class='{$classes} hidden dark:block' />
            ";
        }

        $logo = $lightLogo ?: $darkLogo;
        return $logo ? "<img src='{$logo}' alt='{$alt}' class='{$classes}' />" : '';
    }
}

if (!function_exists('has_adaptive_logos')) {
    /**
     * Verificar si un modelo tiene logos para ambos modos
     */
    function has_adaptive_logos($model): bool
    {
        if (!method_exists($model, 'getLogoUrl')) {
            return false;
        }

        return $model->getLogoUrl('light') && $model->getLogoUrl('dark');
    }
}

if (!function_exists('logo_status')) {
    /**
     * Obtener el estado de los logos de un modelo
     */
    function logo_status($model): array
    {
        if (!method_exists($model, 'getLogoUrl')) {
            return ['status' => 'no_support', 'message' => 'Modelo no soporta logos'];
        }

        $lightLogo = $model->getLogoUrl('light');
        $darkLogo = $model->getLogoUrl('dark');

        if ($lightLogo && $darkLogo) {
            return ['status' => 'complete', 'message' => 'Logos para ambos modos'];
        } elseif ($lightLogo || $darkLogo) {
            return ['status' => 'partial', 'message' => 'Solo un logo disponible'];
        } else {
            return ['status' => 'missing', 'message' => 'Sin logos configurados'];
        }
    }
}
