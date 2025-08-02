<?php

use Illuminate\Support\Facades\Route;

// Solo ruta básica
Route::get('/', function () {
    return redirect('/admin');
});

// Todas las rutas /referee, /club, /player - ELIMINADAS
// Solo mantener rutas de Filament (auto-registradas)
