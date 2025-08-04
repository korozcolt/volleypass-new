<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetupWizardController;

// Ruta principal - página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Rutas del wizard de configuración inicial
Route::middleware(['auth', 'role:superadmin'])->prefix('setup')->name('setup.')->group(function () {
    Route::get('/wizard', [SetupWizardController::class, 'index'])->name('wizard');
    Route::get('/wizard/step/{step}', [SetupWizardController::class, 'showStep'])->name('wizard.step');
    Route::post('/wizard/step/{step}', [SetupWizardController::class, 'processStep'])->name('wizard.process');
    Route::post('/wizard/reset', [SetupWizardController::class, 'reset'])->name('wizard.reset');
});

// Todas las rutas /referee, /club, /player - ELIMINADAS
// Solo mantener rutas de Filament (auto-registradas)
