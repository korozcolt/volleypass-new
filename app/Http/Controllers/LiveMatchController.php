<?php

namespace App\Http\Controllers;

use App\Models\VolleyMatch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class LiveMatchController extends Controller
{
    /**
     * Display the live matches page with real-time updates
     */
    public function index()
    {
        $matches = VolleyMatch::with([
            'homeTeam',
            'awayTeam',
            'tournament',
            'referee',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ])
        ->whereIn('status', ['in_progress', 'scheduled', 'finished'])
        ->orderByRaw("CASE 
            WHEN status = 'in_progress' THEN 1 
            WHEN status = 'scheduled' THEN 2 
            WHEN status = 'finished' THEN 3 
            ELSE 4 
        END")
        ->orderBy('scheduled_at', 'desc')
        ->get();

        $stats = [
            'total_matches' => $matches->count(),
            'live_matches' => $matches->where('status', 'in_progress')->count(),
            'upcoming_matches' => $matches->where('status', 'scheduled')->count(),
            'finished_matches' => $matches->where('status', 'finished')->count(),
        ];

        return Inertia::render('LiveMatchesRealTime', [
            'matches' => $matches,
            'stats' => $stats,
        ]);
    }

    /**
     * Get live matches data for API consumption
     */
    public function getLiveMatches()
    {
        $matches = VolleyMatch::with([
            'homeTeam',
            'awayTeam',
            'tournament',
            'referee',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ])
        ->whereIn('status', ['in_progress', 'scheduled', 'finished'])
        ->orderByRaw("CASE 
            WHEN status = 'in_progress' THEN 1 
            WHEN status = 'scheduled' THEN 2 
            WHEN status = 'finished' THEN 3 
            ELSE 4 
        END")
        ->orderBy('scheduled_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $matches,
            'stats' => [
                'total_matches' => $matches->count(),
                'live_matches' => $matches->where('status', 'in_progress')->count(),
                'upcoming_matches' => $matches->where('status', 'scheduled')->count(),
                'finished_matches' => $matches->where('status', 'finished')->count(),
            ]
        ]);
    }

    /**
     * Get only matches that are currently in progress
     */
    public function getInProgressMatches()
    {
        $matches = VolleyMatch::with([
            'homeTeam',
            'awayTeam',
            'tournament',
            'referee',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ])
        ->where('status', 'in_progress')
        ->orderBy('started_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $matches,
            'count' => $matches->count()
        ]);
    }

    /**
     * Get upcoming matches (scheduled)
     */
    public function getUpcomingMatches()
    {
        $matches = VolleyMatch::with([
            'homeTeam',
            'awayTeam',
            'tournament',
            'referee'
        ])
        ->where('status', 'scheduled')
        ->orderBy('scheduled_at', 'asc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $matches,
            'count' => $matches->count()
        ]);
    }

    /**
     * Get recently finished matches
     */
    public function getFinishedMatches(Request $request)
    {
        $limit = $request->get('limit', 20);
        
        $matches = VolleyMatch::with([
            'homeTeam',
            'awayTeam',
            'tournament',
            'referee',
            'sets' => function ($query) {
                $query->orderBy('set_number');
            }
        ])
        ->where('status', 'finished')
        ->orderBy('finished_at', 'desc')
        ->limit($limit)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $matches,
            'count' => $matches->count()
        ]);
    }

    /**
     * Get match statistics for dashboard
     */
    public function getMatchStats()
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'today' => [
                'total' => VolleyMatch::whereDate('scheduled_at', $today)->count(),
                'live' => VolleyMatch::where('status', 'in_progress')
                    ->whereDate('scheduled_at', $today)->count(),
                'finished' => VolleyMatch::where('status', 'finished')
                    ->whereDate('finished_at', $today)->count(),
            ],
            'this_week' => [
                'total' => VolleyMatch::where('scheduled_at', '>=', $thisWeek)->count(),
                'live' => VolleyMatch::where('status', 'in_progress')
                    ->where('scheduled_at', '>=', $thisWeek)->count(),
                'finished' => VolleyMatch::where('status', 'finished')
                    ->where('finished_at', '>=', $thisWeek)->count(),
            ],
            'this_month' => [
                'total' => VolleyMatch::where('scheduled_at', '>=', $thisMonth)->count(),
                'live' => VolleyMatch::where('status', 'in_progress')
                    ->where('scheduled_at', '>=', $thisMonth)->count(),
                'finished' => VolleyMatch::where('status', 'finished')
                    ->where('finished_at', '>=', $thisMonth)->count(),
            ],
            'all_time' => [
                'total' => VolleyMatch::count(),
                'live' => VolleyMatch::where('status', 'in_progress')->count(),
                'finished' => VolleyMatch::where('status', 'finished')->count(),
                'scheduled' => VolleyMatch::where('status', 'scheduled')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}