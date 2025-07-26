<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Public\PublicTournaments;
use App\Livewire\Public\TournamentDetails;
use App\Livewire\Public\TeamPublicProfile;
use App\Livewire\Public\TournamentStandings;
use App\Livewire\Public\TournamentSchedule;
use App\Livewire\Public\TournamentResults;
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

Route::prefix('public')->name('public.')->group(function () {
    Route::get('/tournaments', PublicTournaments::class)->name('tournaments');
    Route::get('/tournament/{tournament}', TournamentDetails::class)->name('tournament.show');
    Route::get('/team/{team}', TeamPublicProfile::class)->name('team.show');
    Route::get('/standings/{tournament}', TournamentStandings::class)->name('standings');
    Route::get('/schedule/{tournament}', TournamentSchedule::class)->name('schedule');
    Route::get('/results/{tournament}', TournamentResults::class)->name('results');
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

// Settings comunes para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});
