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
        if ($user->hasRole('SuperAdmin') || $user->hasRole('LeagueAdmin')) {
            return 'admin';
        }
        
        if ($user->hasRole('Referee')) {
            return 'referee';
        }
        
        if ($user->hasRole('Coach')) {
            return 'coach';
        }
        
        if ($user->hasRole('Player')) {
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
                $upcomingMatches = VolleyMatch::whereJsonContains('referees', $user->name)
                ->where('status', 'scheduled')
                ->with(['home_team', 'away_team', 'tournament'])
                ->orderBy('scheduled_at')
                ->limit(5)
                ->get();
                
                $recentMatches = VolleyMatch::whereJsonContains('referees', $user->name)
                ->whereIn('status', ['finished', 'in_progress'])
                ->with(['home_team', 'away_team', 'tournament'])
                ->orderBy('scheduled_at', 'desc')
                ->limit(5)
                ->get();
                
                return [
                    'referee' => [
                        'referee' => $user,
                        'upcomingMatches' => $upcomingMatches,
                        'recentMatches' => $recentMatches,
                        'stats' => [
                            'totalMatches' => VolleyMatch::whereJsonContains('referees', $user->name)->count(),
                            'thisMonth' => VolleyMatch::whereJsonContains('referees', $user->name)
                                ->whereMonth('scheduled_at', now()->month)->count(),
                            'thisYear' => VolleyMatch::whereJsonContains('referees', $user->name)
                                ->whereYear('scheduled_at', now()->year)->count(),
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