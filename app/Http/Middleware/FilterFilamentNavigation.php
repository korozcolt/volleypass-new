<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilterFilamentNavigation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $role = $user->getRoleNames()->first();

        // Filtrar recursos según rol
        $this->filterNavigationByRole($role);

        return $next($request);
    }

    private function filterNavigationByRole(string $role): void
    {
        $allowedResources = $this->getAllowedResourcesByRole($role);
        
        // En Filament 3, la navegación se filtra a través de los recursos individuales
        // usando el método shouldRegisterNavigation() o canAccess()
        // Este middleware se mantiene para compatibilidad pero la lógica real
        // debe implementarse en cada Resource usando canAccess() method
        
        // No es necesario filtrar aquí ya que cada Resource debe implementar
        // su propia lógica de acceso usando canAccess() method
    }

    private function getAllowedResourcesByRole(string $role): array
    {
        return match($role) {
            'SuperAdmin' => [
                'UserResource', 'PlayerResource', 'ClubResource', 
                'LeagueResource', 'TournamentResource', 'TeamResource',
                'PaymentResource', 'RoleResource', 'SystemConfigurationResource',
                'MedicalCertificateResource', 'NotificationResource',
                'TransferResource', 'PlayerCardResource', 'InjuryResource',
                'AwardResource', 'ActivityResource', 'DataBackupResource',
                'InvoiceResource', 'QrScanLogResource'
            ],
            'LeagueAdmin' => [
                'PlayerResource', 'ClubResource', 'LeagueResource', 
                'TournamentResource', 'TeamResource', 'PaymentResource',
                'MedicalCertificateResource', 'NotificationResource',
                'UserResource', 'TransferResource', 'PlayerCardResource',
                'InjuryResource', 'AwardResource'
            ],
            'ClubDirector' => [
                'ClubResource', 'TeamResource', 'PlayerResource',
                'PaymentResource', 'NotificationResource',
                'TransferResource', 'PlayerCardResource'
            ],
            'Coach' => [
                'TeamResource', 'PlayerResource', 'NotificationResource'
            ],
            'SportsDoctor' => [
                'PlayerResource', 'MedicalCertificateResource',
                'InjuryResource', 'NotificationResource'
            ],
            'Referee' => [], // Sin acceso al admin
            'Player' => [], // Sin acceso al admin
            'Verifier' => [
                'PlayerCardResource', 'NotificationResource'
            ],
            default => [],
        };
    }
}