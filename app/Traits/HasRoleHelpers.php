<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait HasRoleHelpers
{
    /**
     * Verificar si el usuario tiene acceso al panel de administración
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->hasAnyRole([
            'SuperAdmin',
            'LeagueAdmin', 
            'ClubDirector',
            'Coach',
            'SportsDoctor',
            'Verifier'
        ]);
    }

    /**
     * Verificar si el usuario es administrador del sistema
     */
    public function isSystemAdmin(): bool
    {
        return $this->hasRole('SuperAdmin');
    }

    /**
     * Verificar si el usuario es administrador de liga
     */
    public function isLeagueAdmin(): bool
    {
        return $this->hasRole('LeagueAdmin');
    }

    /**
     * Verificar si el usuario es director de club
     */
    public function isClubDirector(): bool
    {
        return $this->hasRole('ClubDirector');
    }

    /**
     * Verificar si el usuario es entrenador
     */
    public function isCoach(): bool
    {
        return $this->hasRole('Coach');
    }

    /**
     * Verificar si el usuario es médico deportivo
     */
    public function isSportsDoctor(): bool
    {
        return $this->hasRole('SportsDoctor');
    }

    /**
     * Verificar si el usuario es árbitro
     */
    public function isReferee(): bool
    {
        return $this->hasRole('Referee');
    }

    /**
     * Verificar si el usuario es jugador
     */
    public function isPlayer(): bool
    {
        return $this->hasRole('Player');
    }

    /**
     * Verificar si el usuario es verificador
     */
    public function isVerifier(): bool
    {
        return $this->hasRole('Verifier');
    }

    /**
     * Obtener el rol principal del usuario (el de mayor jerarquía)
     */
    public function getPrimaryRole(): ?string
    {
        $roleHierarchy = [
            'SuperAdmin' => 8,
            'LeagueAdmin' => 7,
            'ClubDirector' => 6,
            'SportsDoctor' => 5,
            'Coach' => 4,
            'Verifier' => 3,
            'Referee' => 2,
            'Player' => 1,
        ];

        $userRoles = $this->roles->pluck('name')->toArray();
        $highestRole = null;
        $highestPriority = 0;

        foreach ($userRoles as $role) {
            if (isset($roleHierarchy[$role]) && $roleHierarchy[$role] > $highestPriority) {
                $highestPriority = $roleHierarchy[$role];
                $highestRole = $role;
            }
        }

        return $highestRole;
    }

    /**
     * Obtener los grupos de navegación permitidos para el usuario
     */
    public function getAllowedNavigationGroups(): array
    {
        $primaryRole = $this->getPrimaryRole();

        return match ($primaryRole) {
            'SuperAdmin' => [
                'Gestión Deportiva',
                'Gestión Médica y Documentos',
                'Finanzas y Pagos',
                'Comunicación',
                'Administración del Sistema'
            ],
            'LeagueAdmin' => [
                'Gestión Deportiva',
                'Gestión Médica y Documentos',
                'Finanzas y Pagos',
                'Comunicación'
            ],
            'ClubDirector' => [
                'Gestión Deportiva',
                'Finanzas y Pagos'
            ],
            'SportsDoctor' => [
                'Gestión Médica y Documentos'
            ],
            'Coach' => [
                'Gestión Deportiva'
            ],
            'Verifier' => [
                'Gestión Médica y Documentos'
            ],
            default => []
        };
    }

    /**
     * Verificar si el usuario puede acceder a un recurso específico
     */
    public function canAccessResource(string $resource): bool
    {
        $resourcePermissions = [
            'UserResource' => 'users.view',
            'PlayerResource' => 'players.view',
            'ClubResource' => 'clubs.view',
            'TeamResource' => 'clubs.view',
            'LeagueResource' => 'leagues.view',
            'TournamentResource' => 'tournaments.view',
            'MedicalCertificateResource' => 'medical.view',
            'PlayerCardResource' => 'players.view',
            'PaymentResource' => 'system.access_admin',
            'NotificationResource' => 'system.access_admin',
            'RoleResource' => 'system.access_admin',
            'SystemConfigurationResource' => 'system.access_admin',
        ];

        if (!isset($resourcePermissions[$resource])) {
            return false;
        }

        return $this->can($resourcePermissions[$resource]);
    }

    /**
     * Obtener la URL de redirección después del login
     */
    public function getPostLoginRedirectUrl(): string
    {
        if ($this->canAccessAdminPanel()) {
            return '/admin';
        }

        if ($this->isPlayer()) {
            return route('dashboard');
        }

        return route('home');
    }

    /**
     * Verificar si el usuario puede gestionar otro usuario
     */
    public function canManageUser($targetUser): bool
    {
        // SuperAdmin puede gestionar a todos
        if ($this->isSystemAdmin()) {
            return true;
        }

        // LeagueAdmin puede gestionar usuarios de menor jerarquía
        if ($this->isLeagueAdmin()) {
            return !$targetUser->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
        }

        // ClubDirector puede gestionar solo jugadores y entrenadores de su club
        if ($this->isClubDirector()) {
            return $targetUser->hasAnyRole(['Player', 'Coach']) && 
                   $this->club_id === $targetUser->club_id;
        }

        return false;
    }
}