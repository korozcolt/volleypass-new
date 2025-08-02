<?php

use Illuminate\Support\Facades\Route;

// Filament maneja su propia autenticación
// Solo mantenemos logout para compatibilidad
Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');
