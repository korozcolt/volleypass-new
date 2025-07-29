<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LiveMatchController;
use App\Http\Controllers\RefereeController;

// AUTENTICACIÓN WEB
require __DIR__.'/auth.php';

// RUTAS DE AUTENTICACIÓN INERTIA (para sobrescribir las de Volt)
Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return inertia('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    })->name('login');

    Route::post('login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// RUTAS PÚBLICAS
Route::get('/', [PublicController::class, 'welcome'])->name('home');
Route::get('/live-matches', [PublicController::class, 'liveMatches'])->name('live-matches');
Route::get('/live-matches-realtime', [LiveMatchController::class, 'index'])->name('live-matches-realtime');
Route::get('/partidos', [PublicController::class, 'matches'])->name('public.matches');
Route::get('/torneos', [PublicController::class, 'tournaments'])->name('public.tournaments');
Route::get('/contacto', [PublicController::class, 'contact'])->name('public.contact');
Route::post('/contacto', [PublicController::class, 'submitContact'])->name('public.contact.submit');

// DASHBOARD AUTENTICADO
Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas para árbitros
    Route::prefix('referee')->name('referee.')->group(function () {
        Route::get('/dashboard', [RefereeController::class, 'dashboard'])->name('dashboard');
        Route::get('/schedule', [RefereeController::class, 'schedule'])->name('schedule');
        Route::get('/profile', [RefereeController::class, 'profile'])->name('profile');
        Route::get('/match/{match}/control', [RefereeController::class, 'matchControl'])->name('match.control');
        Route::get('/matches', [RefereeController::class, 'matches'])->name('matches');
        Route::get('/api/matches', [RefereeController::class, 'getAssignedMatches'])->name('api.matches');
        Route::get('/stats', [RefereeController::class, 'getStats'])->name('stats');
        Route::get('/match/{match}/details', [RefereeController::class, 'matchDetails'])->name('match.details');
        Route::get('/api/match/{match}/details', [RefereeController::class, 'getMatchDetails'])->name('api.match.details');
        
        // Rutas para control de partidos
        Route::post('/match/{match}/score', [RefereeController::class, 'updateScore'])->name('match.update-score');
        Route::post('/match/{match}/status', [RefereeController::class, 'updateStatus'])->name('match.update-status');
        Route::post('/match/{match}/new-set', [RefereeController::class, 'startNewSet'])->name('match.new-set');
        Route::post('/match/{match}/end-set', [RefereeController::class, 'endSet'])->name('match.end-set');
    });
});

// Nota: Las rutas /admin son manejadas automáticamente por Filament
