<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QrVerificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ✅ API Authentication independiente de Filament
Route::prefix('v1')->group(function () {

    // Public API (sin autenticación)
    Route::middleware(['throttle:qr_verify'])->group(function () {
        Route::post('/verify-qr', [QrVerificationController::class, 'verify']);
        Route::post('/qr-info', [QrVerificationController::class, 'getQrInfo']);
    });

    // API Authentication
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected API (requiere token Sanctum)
    Route::middleware(['auth:sanctum'])->group(function () {

        // Verificador Authenticated
        Route::middleware(['role:Verifier|LeagueAdmin'])->group(function () {
            Route::post('/verify-batch', [QrVerificationController::class, 'verifyBatch']);
            Route::get('/stats/dashboard', [QrVerificationController::class, 'getStats']);
            //Route::post('/scanner/start-session', [ScannerController::class, 'startSession']);
        });

        // Admin API
        Route::middleware(['role:LeagueAdmin|SuperAdmin'])->group(function () {
            //Route::get('/reports/verifications', [ReportsController::class, 'verifications']);
            //Route::post('/cache/invalidate', [SystemController::class, 'invalidateCache']);
        });

        // Auth management
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);
    });
});
