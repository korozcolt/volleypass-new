<?php

namespace App\Http\Controllers;

use App\Models\VolleyMatch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class RefereeController extends Controller
{
    /**
     * Display the referee dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get matches assigned to this referee
        $assignedMatches = VolleyMatch::with([
            'homeTeam',
            'awayTeam',
            'tournament',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ])
        ->whereJsonContains('referees', $user->name)
        ->orderBy('scheduled_at', 'desc')
        ->get();

        // Get upcoming matches
        $upcomingMatches = $assignedMatches->where('status', 'scheduled')
            ->sortBy('scheduled_at')
            ->take(5);

        // Get live matches
        $liveMatches = $assignedMatches->where('status', 'in_progress');

        // Get recent matches
        $recentMatches = $assignedMatches->where('status', 'finished')
            ->sortByDesc('finished_at')
            ->take(5);

        $stats = [
            'total_matches' => $assignedMatches->count(),
            'live_matches' => $liveMatches->count(),
            'upcoming_matches' => $upcomingMatches->count(),
            'completed_matches' => $recentMatches->count(),
        ];

        return Inertia::render('Referee/Dashboard', [
            'assignedMatches' => $assignedMatches,
            'upcomingMatches' => $upcomingMatches->values(),
            'liveMatches' => $liveMatches->values(),
            'recentMatches' => $recentMatches->values(),
            'stats' => $stats,
        ]);
    }

    /**
     * Show match control panel
     */
    public function matchControl(VolleyMatch $match)
    {
        $user = Auth::user();
        
        // Check if user can control this match
        $canControl = $this->canControlMatch($match, $user);
        
        $match->load([
            'homeTeam',
            'awayTeam',
            'tournament',
            'referee',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ]);

        return Inertia::render('Referee/MatchControl', [
            'match' => $match,
            'canControl' => $canControl,
        ]);
    }

    /**
     * Show matches page for referee
     */
    public function matches(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'all');
        
        $query = VolleyMatch::with([
            'homeTeam',
            'awayTeam',
            'tournament',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ])
        ->whereJsonContains('referees', $user->name);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $matches = $query->orderBy('scheduled_at', 'desc')->get();

        return Inertia::render('Referee/Matches', [
            'matches' => $matches,
            'currentStatus' => $status,
            'stats' => [
                'total' => $matches->count(),
                'scheduled' => $matches->where('status', 'scheduled')->count(),
                'in_progress' => $matches->where('status', 'in_progress')->count(),
                'finished' => $matches->where('status', 'finished')->count(),
            ]
        ]);
    }

    /**
     * Get matches assigned to the current referee (API endpoint)
     */
    public function getAssignedMatches(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'all');
        
        $query = VolleyMatch::with([
            'homeTeam',
            'awayTeam',
            'tournament',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ])
        ->whereJsonContains('referees', $user->name);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $matches = $query->orderBy('scheduled_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $matches,
            'count' => $matches->count()
        ]);
    }

    /**
     * Get referee statistics
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $totalMatches = VolleyMatch::whereJsonContains('referees', $user->name)->count();

        $liveMatches = VolleyMatch::where('status', 'in_progress')
            ->whereJsonContains('referees', $user->name)->count();

        $upcomingMatches = VolleyMatch::where('status', 'scheduled')
            ->whereJsonContains('referees', $user->name)->count();

        $completedMatches = VolleyMatch::where('status', 'finished')
            ->whereJsonContains('referees', $user->name)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_matches' => $totalMatches,
                'live_matches' => $liveMatches,
                'upcoming_matches' => $upcomingMatches,
                'completed_matches' => $completedMatches,
            ]
        ]);
    }

    /**
     * Check if user can control a specific match
     */
    private function canControlMatch(VolleyMatch $match, $user): bool
    {
        // Check if user is assigned as referee in the referees JSON column
        $referees = $match->referees ?? [];
        if (in_array($user->name, $referees)) {
            return true;
        }

        // Check if user has admin role
        if ($user->hasRole('SuperAdmin') || $user->hasRole('LeagueAdmin')) {
            return true;
        }

        return false;
    }

    /**
     * Show match details page for referee
     */
    public function matchDetails(VolleyMatch $match)
    {
        $user = Auth::user();
        
        // Check if user can access this match
        if (!$this->canControlMatch($match, $user)) {
            abort(403, 'No tienes permisos para acceder a este partido.');
        }

        $match->load([
            'homeTeam',
            'awayTeam',
            'tournament',
            'referee',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ]);

        return Inertia::render('Referee/MatchDetails', [
            'match' => $match,
            'canControl' => $this->canControlMatch($match, $user),
            'currentSet' => $match->sets->where('status', 'in_progress')->first(),
            'completedSets' => $match->sets->where('status', 'completed'),
        ]);
    }

    /**
     * Get match details for referee (API endpoint)
     */
    public function getMatchDetails(VolleyMatch $match)
    {
        $user = Auth::user();
        
        // Check if user can access this match
        if (!$this->canControlMatch($match, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder a este partido.'
            ], 403);
        }

        $match->load([
            'homeTeam',
            'awayTeam',
            'tournament',
            'referee',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'match' => $match,
                'can_control' => $this->canControlMatch($match, $user),
                'current_set' => $match->sets->where('status', 'in_progress')->first(),
                'completed_sets' => $match->sets->where('status', 'completed'),
            ]
        ]);
    }

    /**
     * Show referee schedule page
     */
    public function schedule()
    {
        $user = Auth::user();
        
        // Get all matches assigned to this referee
        $matches = VolleyMatch::with([
            'homeTeam',
            'awayTeam',
            'tournament',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ])
        ->whereJsonContains('referees', $user->name)
        ->orderBy('scheduled_at', 'asc')
        ->get();

        return Inertia::render('Referee/Schedule', [
            'matches' => $matches,
        ]);
    }

    /**
     * Show referee profile page
     */
    public function profile()
    {
        $user = Auth::user();
        
        // Get referee profile data
        $referee = $user->referee;
        
        // Get match statistics
        $totalMatches = VolleyMatch::whereJsonContains('referees', $user->name)->count();
        $completedMatches = VolleyMatch::whereJsonContains('referees', $user->name)
            ->where('status', 'finished')->count();
        $upcomingMatches = VolleyMatch::whereJsonContains('referees', $user->name)
            ->where('status', 'scheduled')->count();

        return Inertia::render('Referee/Profile', [
            'referee' => $referee,
            'user' => $user,
            'stats' => [
                'total_matches' => $totalMatches,
                'completed_matches' => $completedMatches,
                'upcoming_matches' => $upcomingMatches,
            ]
        ]);
    }
}