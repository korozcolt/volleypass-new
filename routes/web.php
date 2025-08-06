<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetupWizardController;
use App\Http\Controllers\ClubSetupController;
use App\Http\Controllers\PlayerCardController;
use App\Http\Controllers\FrontendController;

// Ruta principal - página de bienvenida con React
Route::get('/', function () {
    return view('welcome');
});

// Página de contacto
Route::get('/contacto', [FrontendController::class, 'contact'])->name('contact');

// APIs para el frontend
Route::prefix('api')->group(function () {
    Route::get('/departments', [FrontendController::class, 'getDepartments']);
    Route::get('/cities/{departmentId}', [FrontendController::class, 'getCities']);
    Route::post('/contact', [FrontendController::class, 'submitContact']);
});

// Wizard de configuración inicial del sistema
Route::prefix('setup')->group(function () {
    Route::get('/wizard', [SetupWizardController::class, 'index'])->name('setup.wizard');
    Route::post('/wizard/step', [SetupWizardController::class, 'processStep'])->name('setup.wizard.step');
});

// Wizard de configuración de clubes
Route::prefix('club')->group(function () {
    Route::get('/setup/{clubId?}', [ClubSetupController::class, 'index'])->name('club.setup');
    Route::post('/setup/complete', [ClubSetupController::class, 'complete'])->name('club.setup.complete');
    Route::post('/setup/validate-step', [ClubSetupController::class, 'validateStep'])->name('club.setup.validate');
    Route::get('/setup/data/{step}', [ClubSetupController::class, 'getStepData'])->name('club.setup.data');
});

// Rutas para tarjetas de jugadores
Route::get('/card/{cardNumber}', [PlayerCardController::class, 'show'])->name('player.card.show');
Route::get('/card/{cardNumber}/download', [PlayerCardController::class, 'download'])->name('player.card.download');

// Todas las rutas de administración se manejan a través de Filament
// Las vistas de usuario son React/TypeScript
