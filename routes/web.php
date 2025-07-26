<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Public\PublicTournaments;
use App\Livewire\Public\TournamentDetails;
use App\Livewire\Public\TeamPublicProfile;
use App\Livewire\Public\TournamentStandings;
use App\Livewire\Public\TournamentSchedule;
use App\Livewire\Public\TournamentResults;
use App\Livewire\Public\Standings;
use App\Livewire\Player\PlayerDashboard;
use App\Livewire\Player\DigitalCard;
use App\Livewire\Player\PlayerStats;
use App\Livewire\Player\MyTournaments;
use App\Livewire\Player\MyTournamentDetails;
use App\Livewire\Player\PlayerSettings;
use App\Livewire\Player\PlayerNotifications;
use App\Http\Controllers\PlayerController;

// RUTAS PÚBLICAS (NO REQUIEREN AUTENTICACIÓN)
Route::get('/', PublicTournaments::class)->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::get('/teams', \App\Livewire\Public\Teams::class)->name('teams');
Route::get('/standings', Standings::class)->name('standings');
Route::get('/schedule', \App\Livewire\Public\Schedule::class)->name('schedule');

// Demo de componentes UI deportivos
Route::get('/demo-components', \App\Livewire\Public\DemoComponents::class)->name('demo.components');

Route::prefix('public')->name('public.')->group(function () {
    Route::get('/matches', \App\Livewire\Public\LiveMatches::class)->name('matches');
    Route::get('/results', \App\Livewire\Public\RecentResults::class)->name('results');
    Route::get('/teams', \App\Livewire\Public\Teams::class)->name('teams');
    Route::get('/standings', \App\Livewire\Public\Standings::class)->name('standings');
    Route::get('/stats', \App\Livewire\Public\LeagueStats::class)->name('stats');
    Route::get('/tournaments', PublicTournaments::class)->name('tournaments');
    Route::get('/tournament/{tournament}', TournamentDetails::class)->name('tournament.show');
    Route::get('/team/{team}', TeamPublicProfile::class)->name('team.show');
    Route::get('/standings/{tournament}', TournamentStandings::class)->name('tournament.standings');
    Route::get('/schedule/{tournament}', TournamentSchedule::class)->name('tournament.schedule');
    Route::get('/results/{tournament}', TournamentResults::class)->name('tournament.results');
});

// AUTENTICACIÓN WEB (SOLO USUARIOS FINALES)
require __DIR__.'/auth.php';

// DASHBOARD INTELIGENTE - REDIRIGE SEGÚN ROL
Route::get('/dashboard', function () {
    if (!auth('web')->check()) {
        return redirect()->route('login');
    }
    
    $user = auth('web')->user();
    
    // Roles administrativos → Panel admin
    $adminRoles = ['admin', 'super_admin', 'league_director', 'club_director', 'coach', 'referee'];
    
    foreach ($adminRoles as $role) {
        if ($user->hasRole($role)) {
            return redirect('/admin');
        }
    }
    
    // Jugadoras → Dashboard específico
    if ($user->hasRole('player')) {
        return redirect()->route('player.dashboard');
    }
    
    // Sin rol definido → Home
    return redirect()->route('home');
})->middleware(['auth:web', 'verified'])->name('dashboard');

// RUTAS DE JUGADORAS
Route::middleware(['auth:web', 'role:player'])->group(function () {
    Route::prefix('player')->name('player.')->group(function () {
        Route::get('/dashboard', PlayerDashboard::class)->name('dashboard');
        Route::get('/profile', function () {
            return view('player.profile');
        })->name('profile');
        Route::post('/profile/update', [PlayerController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo', [PlayerController::class, 'updatePhoto'])->name('profile.photo');
        Route::get('/card', DigitalCard::class)->name('card');
        Route::get('/card/download', [PlayerController::class, 'downloadCard'])->name('card.download');
        Route::get('/stats', PlayerStats::class)->name('stats');
        Route::get('/tournaments', MyTournaments::class)->name('tournaments');
        Route::get('/tournaments/{tournament}', MyTournamentDetails::class)->name('tournaments.show');
        Route::get('/settings', PlayerSettings::class)->name('settings');
        Route::get('/notifications', PlayerNotifications::class)->name('notifications');
    });
});

// RUTAS DE ENTRENADORES
Route::middleware(['auth:web', 'role:coach'])->group(function () {
    Route::prefix('coach')->name('coach.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Coach\Dashboard::class)->name('dashboard');
        Route::get('/team', \App\Livewire\Coach\TeamManagement::class)->name('team');
        Route::get('/tournaments', \App\Livewire\Coach\MyTournaments::class)->name('tournaments');
        Route::get('/reports', \App\Livewire\Coach\Reports::class)->name('reports');
    });
});

// RUTAS DE ÁRBITROS
Route::middleware(['auth:web', 'role:referee'])->group(function () {
    Route::prefix('referee')->name('referee.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Referee\Dashboard::class)->name('dashboard');
        Route::get('/matches', \App\Livewire\Referee\MyMatches::class)->name('matches');
        Route::get('/tournaments', \App\Livewire\Referee\MyTournaments::class)->name('tournaments');
        Route::get('/match-control/{match}', \App\Livewire\Referee\MatchControl::class)->name('match-control');
    });
});

// RUTAS DE PERSONAL MÉDICO
Route::middleware(['auth:web', 'role:medical'])->group(function () {
    Route::prefix('medical')->name('medical.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Medical\Dashboard::class)->name('dashboard');
        Route::get('/players', \App\Livewire\Medical\PlayersList::class)->name('players');
        Route::get('/player/{player}', \App\Livewire\Medical\PlayerMedicalHistory::class)->name('player');
        Route::get('/reports', \App\Livewire\Medical\MedicalReports::class)->name('reports');
    });
});

// RUTAS COMUNES PARA USUARIOS AUTENTICADOS
Route::middleware(['auth:web'])->group(function () {
    Route::prefix('matches')->name('matches.')->group(function () {
        Route::get('/', \App\Livewire\Shared\MatchesList::class)->name('index');
        Route::get('/{match}', \App\Livewire\Shared\MatchDetails::class)->name('show');
    });
    
    Route::prefix('teams')->name('teams.')->group(function () {
        Route::get('/', \App\Livewire\Shared\TeamsList::class)->name('index');
        Route::get('/{team}', \App\Livewire\Shared\TeamDetails::class)->name('show');
    });
});

// Settings comunes para usuarios autenticados
Route::middleware(['auth:web'])->group(function () {
    Route::get('/settings', function () {
        return redirect()->route('settings.profile');
    })->name('settings');
    Route::get('/profile/edit', function () {
        return view('profile.edit');
    })->name('profile.edit');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});
