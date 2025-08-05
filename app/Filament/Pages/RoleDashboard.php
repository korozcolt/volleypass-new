<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\League;
use App\Models\Payment;
use App\Models\MedicalCertificate;

class RoleDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.role-dashboard';
    protected static ?string $slug = 'dashboard';

    public function getTitle(): string
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();

        return match($role) {
            'SuperAdmin' => 'Dashboard - Super Administrador',
            'LeagueAdmin' => 'Dashboard - Administrador de Liga',
            'ClubDirector' => 'Dashboard - Director de Club',
            'Coach' => 'Dashboard - Entrenador',
            'SportsDoctor' => 'Dashboard - Médico Deportivo',
            'Verifier' => 'Dashboard - Verificador',
            default => 'Dashboard'
        };
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    protected function getHeaderWidgets(): array
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();

        return match($role) {
            'SuperAdmin' => [
                \App\Filament\Widgets\SystemStatsWidget::class,
                \App\Filament\Widgets\UserStatsOverviewWidget::class,
                \App\Filament\Widgets\ClubStatsWidget::class,
            ],
            'LeagueAdmin' => [
                \App\Filament\Widgets\ClubStatsWidget::class,
                \App\Filament\Widgets\UserStatsOverviewWidget::class,
            ],
            'ClubDirector' => [
                \App\Filament\Widgets\ClubStatsWidget::class,
            ],
            'Coach' => [
                \App\Filament\Widgets\ClubStatsWidget::class,
            ],
            'SportsDoctor' => [
                \App\Filament\Widgets\SystemStatsWidget::class,
            ],
            'Verifier' => [
                \App\Filament\Widgets\SystemStatsWidget::class,
            ],
            default => []
        };
    }

    public function getRoleSpecificData(): array
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();

        return match($role) {
            'SuperAdmin' => [
                'totalUsers' => User::count(),
                'totalPlayers' => Player::count(),
                'totalClubs' => Club::count(),
                'totalTeams' => Team::count(),
                'totalTournaments' => Tournament::count(),
                'totalLeagues' => League::count(),
                'recentPayments' => Payment::latest()->limit(5)->get(),
                'systemHealth' => 'healthy'
            ],
            'LeagueAdmin' => [
                'myLeagues' => $user->leagues ?? collect(),
                'totalTournaments' => Tournament::count(),
                'activeTournaments' => Tournament::where('status', 'active')->count(),
                'totalClubs' => Club::count(),
                'totalTeams' => Team::count(),
                'recentPayments' => Payment::latest()->limit(5)->get()
            ],
            'ClubDirector' => [
                'myClub' => $user->club,
                'myTeams' => $user->club?->teams ?? collect(),
                'totalPlayers' => $user->club?->players()->count() ?? 0,
                'pendingPayments' => Payment::where('club_id', $user->club?->id)
                    ->where('status', 'pending')
                    ->count(),
                'activeTeams' => $user->club?->teams()->where('status', 'active')->count() ?? 0
            ],
            'Coach' => [
                'myTeams' => $user->coachedTeams ?? collect(),
                'totalPlayers' => $user->coachedTeams?->sum(fn($team) => $team->players->count()) ?? 0,
                'upcomingMatches' => collect(), // TODO: Implementar cuando exista modelo Match
                'trainingSchedule' => collect() // TODO: Implementar cuando exista modelo Training
            ],
            'SportsDoctor' => [
                'totalPatients' => Player::count(),
                'pendingCertificates' => MedicalCertificate::where('status', 'pending')->count(),
                'expiringCertificates' => MedicalCertificate::where('expires_at', '<=', now()->addDays(30))->count(),
                'recentCertificates' => MedicalCertificate::latest()->limit(5)->get()
            ],
            'Verifier' => [
                'pendingVerifications' => Player::where('verification_status', 'pending')->count(),
                'verifiedToday' => Player::where('verified_at', '>=', now()->startOfDay())->count(),
                'totalVerified' => Player::where('verification_status', 'verified')->count(),
                'rejectedDocuments' => Player::where('verification_status', 'rejected')->count()
            ],
            default => []
        };
    }

    public static function canAccess(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $allowedRoles = [
            'SuperAdmin',
            'LeagueAdmin',
            'ClubDirector',
            'Coach',
            'SportsDoctor',
            'Verifier'
        ];

        return $user->hasAnyRole($allowedRoles);
    }
}