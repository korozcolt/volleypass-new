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
// use App\Livewire\Player\ProfileManagement; // No existe aún
use App\Livewire\Player\DigitalCard;
use App\Livewire\Player\PlayerStats;
use App\Livewire\Player\MyTournaments;
use App\Livewire\Player\MyTournamentDetails;
use App\Livewire\Player\PlayerSettings;
use App\Livewire\Player\PlayerNotifications;
use App\Http\Controllers\PlayerController;

// PANTALLA INICIAL (PÚBLICA)
Route::get('/', PublicTournaments::class)->name('home');

// Página de información
Route::view('/about', 'pages.about')->name('about');

// Dashboard general - redirige basado en rol
Route::get('dashboard', function () {
    return redirect(\App\Services\RoleRedirectionService::getRedirectUrl());
})->middleware(['auth', 'verified'])->name('dashboard');

// RUTAS PÚBLICAS (Sin Autenticación)
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/tournament/{tournament}', TournamentDetails::class)->name('tournament.show');
    Route::get('/team/{team}', TeamPublicProfile::class)->name('team.show');
    Route::get('/standings/{tournament}', TournamentStandings::class)->name('standings');
    Route::get('/schedule/{tournament}', TournamentSchedule::class)->name('schedule');
    Route::get('/results/{tournament}', TournamentResults::class)->name('results');
});

// RUTAS DE JUGADORAS (Usuario Final)
Route::middleware(['auth'])->group(function () {
    Route::prefix('player')->name('player.')->group(function () {

        // Dashboard principal - primera pantalla después del login
        Route::get('/dashboard', PlayerDashboard::class)->name('dashboard');

        // Gestión de perfil personal
        Route::get('/profile', function () {
            return view('player.profile');
        })->name('profile');
        Route::post('/profile/update', [PlayerController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo', [PlayerController::class, 'updatePhoto'])->name('profile.photo');

        // Mi carnet digital
        Route::get('/card', DigitalCard::class)->name('card');
        Route::get('/card/download', [PlayerController::class, 'downloadCard'])->name('card.download');

        // Mis estadísticas personales (solo lectura)
        Route::get('/stats', PlayerStats::class)->name('stats');

        // Mis torneos (solo donde participo)
        Route::get('/tournaments', MyTournaments::class)->name('tournaments');
        Route::get('/tournaments/{tournament}', MyTournamentDetails::class)->name('tournaments.show');

        // Configuraciones básicas
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

require __DIR__.'/auth.php';
