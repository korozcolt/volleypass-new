<?php

use Illuminate\Support\Facades\Route;

// AUTENTICACIÓN WEB
require __DIR__.'/auth.php';

// DASHBOARD - REDIRIGE A ADMIN
Route::get('/dashboard', function () {
    if (!auth('web')->check()) {
        return redirect()->route('login');
    }
    
    return redirect('/admin');
})->middleware(['auth:web', 'verified'])->name('dashboard');

// RUTA HOME - REDIRIGE A LOGIN
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');
