<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Player\Dashboard as PlayerDashboard;
use App\Livewire\Coach\Dashboard as CoachDashboard;
use App\Livewire\Public\Tournaments as PublicTournaments;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth'])->group(function () {
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
});

// Public Routes
Route::get('/tournaments/public', PublicTournaments::class)->name('tournaments.public');

require __DIR__.'/auth.php';
// Rutas de administración
require __DIR__.'/admin.php';
