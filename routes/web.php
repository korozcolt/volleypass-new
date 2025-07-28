<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DashboardController;

// AUTENTICACIÓN WEB
require __DIR__.'/auth.php';

// RUTAS PÚBLICAS
Route::get('/', [PublicController::class, 'welcome'])->name('home');
Route::get('/live-matches', [PublicController::class, 'liveMatches'])->name('live-matches');
Route::get('/partidos', [PublicController::class, 'matches'])->name('public.matches');
Route::get('/torneos', [PublicController::class, 'tournaments'])->name('public.tournaments');
Route::get('/contacto', [PublicController::class, 'contact'])->name('public.contact');
Route::post('/contacto', [PublicController::class, 'submitContact'])->name('public.contact.submit');

// DASHBOARD AUTENTICADO
Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Nota: Las rutas /admin son manejadas automáticamente por Filament
