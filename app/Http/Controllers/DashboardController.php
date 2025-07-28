<?php

namespace App\Http\Controllers;

use App\Models\VolleyMatch;
use App\Models\Tournament;
use App\Models\Player;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Dashboard principal basado en el rol del usuario
     */
    public function index(): Response
    {
        $user = Auth::user();
        $userRole = $this->getUserRole($user);
        $dashboardData = $this->getDashboardData($user, $userRole);

        return Inertia::render('Dashboard', [
            'userRole' => $userRole,
            'dashboardData' => $dashboardData,
        ]);
    }

    /**
     * Obtener el rol principal del usuario
     */
    private function getUserRole($user): string
    {
        if ($user->hasRole('admin')) {
            return 'admin';
        }
        
        if ($user->hasRole('referee')) {
            return 'referee';
        }
        
        if ($user->hasRole('coach')) {
            return 'coach';
        }
        
        if ($user->hasRole('player')) {
            return 'player';
        }
        
        return 'player'; // Default
    }

    /**
     * Obtener datos específicos del dashboard según el rol
     */
    private function getDashboardData($user, string $role): array
    {
        switch ($role) {
            case 'admin':
                return [
                    'admin' => [
                        'totalUsers' => \App\Models\User::count(),
                        'totalMatches' => VolleyMatch::count(),
                        'totalTournaments' => Tournament::count(),
                        'recentActivity' => [],
                    ]
                ];

            case 'referee':
                // TODO: Implementar modelo Referee
                return [
                    'referee' => [
                        'referee' => null,
                        'upcomingMatches' => [],
                        'recentMatches' => [],
                        'stats' => [
                            'totalMatches' => 0,
                            'thisMonth' => 0,
                            'thisYear' => 0,
                            'rating' => 4.5,
                        ],
                        'notifications' => [],
                    ]
                ];

            case 'coach':
                $coach = Coach::where('user_id', $user->id)->first();
                return [
                    'coach' => [
                        'coach' => $coach,
                        'teams' => $coach?->teams ?? [],
                        'upcomingMatches' => [],
                        'recentMatches' => [],
                        'teamStats' => [
                            'totalTeams' => $coach?->teams->count() ?? 0,
                            'totalPlayers' => 0,
                            'wins' => 0,
                            'losses' => 0,
                        ],
                        'notifications' => [],
                    ]
                ];

            case 'player':
            default:
                $player = Player::where('user_id', $user->id)->with('team')->first();
                return [
                    'player' => [
                        'player' => $player,
                        'upcomingMatches' => [],
                        'recentMatches' => [],
                        'teamStats' => [
                            'wins' => 0,
                            'losses' => 0,
                            'totalMatches' => 0,
                        ],
                        'notifications' => [],
                    ]
                ];
        }
    }
}