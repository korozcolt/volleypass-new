<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Player\Dashboard as PlayerDashboard;
use App\Livewire\Coach\Dashboard as CoachDashboard;
use App\Livewire\Public\TournamentsDashboard as PublicTournamentsDashboard;

// Ruta raíz redirige a torneos públicos como medida de seguridad
Route::get('/', function () {
    return redirect()->route('tournaments.public');
})->name('home');

// Dashboard general - redirige basado en rol
Route::get('dashboard', function () {
    return redirect(\App\Services\RoleRedirectionService::getRedirectUrl());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role.redirect'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth', 'role.redirect'])->group(function () {
    // Player Routes
    Route::prefix('dashboard/player')->group(function () {
        Route::get('/', PlayerDashboard::class)->name('player.dashboard');
        Route::get('/profile', function () {
            return view('player.profile');
        })->name('player.profile');
        Route::get('/card', function () {
            return view('player.card');
        })->name('player.card');
        Route::get('/stats', function () {
            return view('player.stats');
        })->name('player.stats');
        Route::get('/matches', function () {
            return view('player.matches');
        })->name('player.matches');
    });

    // Coach Routes
    Route::prefix('dashboard/coach')->group(function () {
        Route::get('/', CoachDashboard::class)->name('coach.dashboard');
    });

    // Referee Routes
    Route::prefix('dashboard/referee')->group(function () {
        Route::get('/', \App\Livewire\Referee\Dashboard::class)->name('referee.dashboard');
    });

    // Club Director Routes
    Route::prefix('dashboard/club')->group(function () {
        Route::get('/', function () {
            return view('club.dashboard');
        })->name('club.dashboard');
    });

    // League Admin Routes
    Route::prefix('dashboard/league')->group(function () {
        Route::get('/', function () {
            return view('league.dashboard');
        })->name('league.dashboard');
    });

    // Super Admin Routes
    Route::prefix('dashboard/admin')->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});

// Public Routes
Route::get('/tournaments/public', function () {
    return redirect()->route('tournaments.dashboard');
})->name('tournaments.public');
Route::get('/tournaments/dashboard', PublicTournamentsDashboard::class)->name('tournaments.dashboard');

require __DIR__.'/auth.php';
// Rutas de administración
require __DIR__.'/admin.php';
