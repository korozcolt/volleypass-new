<?php

namespace App\Http\Controllers;

use App\Models\VolleyMatch;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PublicController extends Controller
{
    public function welcome()
    {
        return Inertia::render('HomePage', [
            'systemConfig' => [
                'app_name' => 'VolleyPass',
                'app_description' => 'Sistema de gestión de voleibol',
                'contact_email' => 'info@volleypass.com',
                'contact_phone' => '+57 (5) 282-5555',
            ],
            'featuredMatches' => VolleyMatch::with(['home_team', 'away_team', 'tournament'])
                ->whereIn('status', ['live', 'upcoming'])
                ->orderBy('scheduled_at')
                ->limit(6)
                ->get(),
            'upcomingTournaments' => Tournament::with(['league'])
                ->where('status', 'upcoming')
                ->orderBy('start_date')
                ->limit(3)
                ->get(),
        ]);
    }

    public function liveMatches()
    {
        return Inertia::render('LiveMatches', [
            'matches' => VolleyMatch::with(['home_team', 'away_team', 'tournament', 'referee'])
                ->whereIn('status', ['live', 'upcoming', 'finished'])
                ->orderByRaw("CASE 
                    WHEN status = 'live' THEN 1 
                    WHEN status = 'upcoming' THEN 2 
                    WHEN status = 'finished' THEN 3 
                    ELSE 4 
                END")
                ->orderBy('scheduled_at')
                ->get(),
        ]);
    }

    public function matches()
    {
        return Inertia::render('Matches', [
            'matches' => VolleyMatch::with(['home_team', 'away_team', 'tournament', 'referee'])
                ->orderBy('scheduled_at', 'desc')
                ->get(),
        ]);
    }

    public function tournaments()
    {
        return Inertia::render('Tournaments', [
            'user' => Auth::user(),
            'tournaments' => Tournament::with(['league', 'matches'])
                ->orderBy('start_date', 'desc')
                ->get(),
        ]);
    }

    public function contact()
    {
        return Inertia::render('Contact', [
            'systemConfig' => [
                'app_name' => 'VolleyPass',
                'contact_email' => 'info@volleypass.com',
                'contact_phone' => '+57 (5) 282-5555',
                'contact_address' => 'Sincelejo, Sucre',
            ],
        ]);
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);
        
        return back()->with('success', 'Mensaje enviado correctamente. Te contactaremos pronto.');
    }
}
