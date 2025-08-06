<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QrVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Rules\NoAccentsEmail;

// RUTAS PÚBLICAS API (NO REQUIEREN TOKEN)
Route::prefix('v1')->group(function () {
    
    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
            'environment' => app()->environment()
        ]);
    })->name('api.health');

    // Verificación QR (público para verificadores)
    Route::post('/verify-qr', [QrVerificationController::class, 'verify'])->name('api.verify-qr');
    Route::post('/qr-info', [QrVerificationController::class, 'getQrInfo'])->name('api.qr-info');

    // Nuevas rutas de verificación de carnets
    Route::get('/card/verify/{token}', [\App\Http\Controllers\Api\CardVerificationController::class, 'verify'])->name('api.card.verify');
    Route::get('/card/number/{cardNumber}', [\App\Http\Controllers\Api\CardVerificationController::class, 'verifyByNumber'])->name('api.card.verify-number');

    // Autenticación para verificadores
    Route::prefix('auth')->name('api.auth.')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/check-email', function (Request $request) {
            $request->validate(['email' => ['required', new NoAccentsEmail()]]);
            $exists = \App\Models\User::where('email', $request->email)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['Verifier', 'LeagueAdmin', 'SuperAdmin']);
                })
                ->exists();
            return response()->json(['exists' => $exists]);
        })->name('check-email');
    });

    // API Pública
    Route::prefix('public')->group(function () {
        Route::get('/player-status/{card_number}', function ($cardNumber) {
            $card = \App\Models\PlayerCard::where('card_number', $cardNumber)
                ->with('player:id,name,position,category')
                ->first();

            if (!$card) {
                return response()->json(['error' => 'Carnet no encontrado'], 404);
            }

            return response()->json([
                'player_name' => $card->player->name,
                'position' => $card->player->position->getLabel(),
                'category' => $card->player->category->getLabel(),
                'card_status' => $card->status->getLabel(),
                'is_active' => $card->is_active
            ]);
        })->name('api.public.player-status');

        Route::get('/league-stats', function () {
            return response()->json([
                'total_players' => \App\Models\Player::count(),
                'active_cards' => \App\Models\PlayerCard::active()->count(),
                'verifications_today' => \App\Models\QrScanLog::whereDate('scanned_at', today())->count(),
                'clubs_registered' => \App\Models\Club::count()
            ]);
        })->name('api.public.league-stats');

        // Servicios públicos de torneos y partidos
        Route::prefix('tournaments')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\PublicTournamentController::class, 'index'])->name('api.public.tournaments.index');
            Route::get('/{id}', [\App\Http\Controllers\Api\PublicTournamentController::class, 'show'])->name('api.public.tournaments.show');
        });

        Route::prefix('matches')->group(function () {
            Route::get('/scheduled', [\App\Http\Controllers\Api\PublicMatchController::class, 'scheduled'])->name('api.public.matches.scheduled');
            Route::get('/live', [\App\Http\Controllers\Api\PublicMatchController::class, 'live'])->name('api.public.matches.live');
            Route::get('/{id}', [\App\Http\Controllers\Api\PublicMatchController::class, 'show'])->name('api.public.matches.show');
            
            // Listados de jugadores por partido
            Route::get('/{id}/players', [\App\Http\Controllers\Api\PublicMatchPlayersController::class, 'show'])->name('api.public.matches.players');
            Route::get('/{id}/teams/{teamId}/players', [\App\Http\Controllers\Api\PublicMatchPlayersController::class, 'showTeam'])->name('api.public.matches.team-players');
        });
        
        // Tablas de posiciones de torneos
        Route::prefix('tournaments/{id}')->group(function () {
            Route::get('/standings', [\App\Http\Controllers\Api\PublicTournamentStandingsController::class, 'show'])->name('api.public.tournaments.standings');
            Route::get('/groups/{groupId}/standings', [\App\Http\Controllers\Api\PublicTournamentStandingsController::class, 'showGroup'])->name('api.public.tournaments.group-standings');
        });
    });
});

// RUTAS PROTEGIDAS API (REQUIEREN TOKEN)
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    // Gestión de tokens y usuario
    Route::prefix('auth')->name('api.auth.')->group(function () {
        Route::get('/user', [AuthController::class, 'user'])->name('user');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
        Route::get('/tokens', [AuthController::class, 'listTokens'])->name('tokens.list');
        Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeToken'])->name('tokens.revoke');
    });

    // Gestión de perfiles de usuario
    Route::prefix('users')->name('api.users.')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Api\UserProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [\App\Http\Controllers\Api\UserProfileController::class, 'update'])->name('profile.update');
        Route::get('/{userId}/profile', [\App\Http\Controllers\Api\UserProfileController::class, 'showPublic'])->name('profile.public');
    });
    
    // Verificadores avanzados
    Route::middleware(['api.role:Verifier,LeagueAdmin,SuperAdmin'])->group(function () {
        Route::post('/verify-batch', [QrVerificationController::class, 'verifyBatch'])->name('api.verify-batch');
        Route::get('/stats/dashboard', [QrVerificationController::class, 'getStats'])->name('api.stats.dashboard');
    });

    // Rutas autenticadas de verificación de carnets
    Route::get('/card/details/{token}', [\App\Http\Controllers\Api\CardVerificationController::class, 'details'])->name('api.card.details');
    Route::get('/card/stats', [\App\Http\Controllers\Api\CardVerificationController::class, 'stats'])->name('api.card.stats');

    // Sesiones de verificación
    Route::prefix('scanner')->group(function () {
        Route::post('/start-session', function (Request $request) {
            $request->validate([
                'event_name' => 'required|string|max:200',
                'event_type' => 'required|string|in:match,tournament,training',
                'location' => 'nullable|string|max:200'
            ]);

            $user = $request->user();
            if (!$user->hasAnyRole(['Verifier', 'LeagueAdmin', 'SuperAdmin'])) {
                return response()->json(['error' => 'Sin permisos'], 403);
            }

            $session = \App\Models\MatchVerification::create([
                'event_name' => $request->event_name,
                'event_type' => $request->event_type,
                'venue' => $request->location,
                'event_date' => today(),
                'verifier_id' => $request->user()->id,
                'verification_started_at' => now(),
                'status' => 'in_progress'
            ]);

            return response()->json([
                'session_id' => $session->id,
                'message' => 'Sesión iniciada'
            ]);
        });

        Route::post('/end-session/{sessionId}', function ($sessionId, Request $request) {
            $session = \App\Models\MatchVerification::where('id', $sessionId)
                ->where('verifier_id', $request->user()->id)
                ->where('status', 'in_progress')
                ->first();

            if (!$session) {
                return response()->json(['error' => 'Sesión no encontrada'], 404);
            }

            $session->update([
                'verification_completed_at' => now(),
                'status' => 'completed'
            ]);

            return response()->json([
                'message' => 'Sesión finalizada',
                'duration_minutes' => $session->verification_started_at->diffInMinutes(now())
            ]);
        });
    });

    // Reportes (Admin)
    Route::prefix('reports')->group(function () {
        Route::get('/verifications', function (Request $request) {
            $user = $request->user();
            if (!$user->hasAnyRole(['LeagueAdmin', 'SuperAdmin'])) {
                return response()->json(['error' => 'Sin permisos'], 403);
            }

            $days = $request->query('days', 7);
            $stats = \App\Models\QrScanLog::where('scanned_at', '>=', now()->subDays($days))
                ->selectRaw('
                    DATE(scanned_at) as date,
                    COUNT(*) as total_verifications,
                    COUNT(CASE WHEN scan_result = "success" THEN 1 END) as successful,
                    COUNT(CASE WHEN scan_result = "error" THEN 1 END) as failed,
                    AVG(response_time_ms) as avg_response_time
                ')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();

            return response()->json(['data' => $stats]);
        });

        Route::get('/daily-summary', function (Request $request) {
            $user = $request->user();
            if (!$user->hasAnyRole(['LeagueAdmin', 'SuperAdmin'])) {
                return response()->json(['error' => 'Sin permisos'], 403);
            }

            return response()->json([
                'today' => [
                    'verifications' => \App\Models\QrScanLog::whereDate('scanned_at', today())->count(),
                    'unique_players' => \App\Models\QrScanLog::whereDate('scanned_at', today())->distinct('player_id')->count(),
                    'active_verifiers' => \App\Models\QrScanLog::whereDate('scanned_at', today())->distinct('scanned_by')->count()
                ],
                'yesterday' => [
                    'verifications' => \App\Models\QrScanLog::whereDate('scanned_at', now()->subDay())->count(),
                ]
            ]);
        });
    });

    // Eventos (Admin)
    Route::prefix('events')->group(function () {
        Route::get('/active', function (Request $request) {
            $user = $request->user();
            if (!$user->hasAnyRole(['LeagueAdmin', 'SuperAdmin'])) {
                return response()->json(['error' => 'Sin permisos'], 403);
            }

            $events = \App\Models\MatchVerification::where('status', 'in_progress')
                ->with('verifier:id,name')
                ->get();

            return response()->json(['events' => $events]);
        });
    });

    // Sistema (SuperAdmin)
    Route::prefix('system')->group(function () {
        Route::post('/cache/clear', function (Request $request) {
            $user = $request->user();
            if (!$user->hasRole('SuperAdmin')) {
                return response()->json(['error' => 'Solo SuperAdmin'], 403);
            }

            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');

            return response()->json(['message' => 'Cache limpiado']);
        });

        Route::get('/info', function (Request $request) {
            $user = $request->user();
            if (!$user->hasRole('SuperAdmin')) {
                return response()->json(['error' => 'Solo SuperAdmin'], 403);
            }

            return response()->json([
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'environment' => app()->environment(),
                'debug' => config('app.debug'),
                'timezone' => config('app.timezone'),
                'database' => [
                    'connection' => config('database.default'),
                    'users_count' => \App\Models\User::count(),
                    'players_count' => \App\Models\Player::count(),
                ]
            ]);
        });
    });
});

// Broadcasting Authentication
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/broadcasting/auth', function (Request $request) {
        return \Illuminate\Support\Facades\Broadcast::auth($request);
    });
});

// Real-time match updates
Route::prefix('v1/matches')->group(function () {
    // Rutas públicas para obtener datos en tiempo real
    Route::get('/live', [\App\Http\Controllers\Api\MatchRealTimeController::class, 'getLiveMatches']);
    Route::get('/{matchId}/realtime', [\App\Http\Controllers\Api\MatchRealTimeController::class, 'getMatchData']);
    
    // Rutas protegidas para actualizar datos (solo árbitros/administradores)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{matchId}/score', [\App\Http\Controllers\Api\MatchRealTimeController::class, 'updateSetScore']);
        Route::post('/{matchId}/status', [\App\Http\Controllers\Api\MatchRealTimeController::class, 'changeStatus']);
        Route::post('/{matchId}/rotation', [\App\Http\Controllers\Api\MatchRealTimeController::class, 'updateRotation']);
        Route::post('/{matchId}/new-set', [\App\Http\Controllers\Api\MatchRealTimeController::class, 'startNewSet']);
        Route::post('/{matchId}/events', [\App\Http\Controllers\Api\MatchRealTimeController::class, 'addMatchEvent']);
    });
});

// Match Live Management (Solo para árbitros)
Route::prefix('v1/matches')->middleware(['auth:sanctum', 'api.role:Referee,LeagueAdmin,SuperAdmin'])->group(function () {
    Route::post('/{id}/start', [\App\Http\Controllers\Api\MatchLiveController::class, 'startMatch']);
    Route::post('/{id}/finish', [\App\Http\Controllers\Api\MatchLiveController::class, 'finishMatch']);
    Route::post('/{id}/sets/start', [\App\Http\Controllers\Api\MatchLiveController::class, 'startNewSet']);
    Route::post('/{id}/sets/finish', [\App\Http\Controllers\Api\MatchLiveController::class, 'finishSet']);
    Route::post('/{id}/score', [\App\Http\Controllers\Api\MatchLiveController::class, 'updateScore']);
    Route::post('/{id}/rotation', [\App\Http\Controllers\Api\MatchLiveController::class, 'updateRotation']);
    Route::post('/{id}/events', [\App\Http\Controllers\Api\MatchLiveController::class, 'addEvent']);
    Route::get('/{id}/events', [\App\Http\Controllers\Api\MatchLiveController::class, 'getEvents']);
    Route::get('/{id}/status', [\App\Http\Controllers\Api\MatchLiveController::class, 'getMatchStatus']);
});

// Rotation Tracking (Solo para árbitros)
Route::prefix('v1/matches')->middleware(['auth:sanctum', 'api.role:Referee,LeagueAdmin,SuperAdmin'])->group(function () {
    Route::post('/{id}/rotation/update', [\App\Http\Controllers\Api\RotationTrackingController::class, 'updateRotation']);
    Route::post('/{id}/rotation/rotate', [\App\Http\Controllers\Api\RotationTrackingController::class, 'rotatePositions']);
    Route::get('/{id}/rotation/current', [\App\Http\Controllers\Api\RotationTrackingController::class, 'getCurrentRotation']);
    Route::get('/{id}/rotation/history', [\App\Http\Controllers\Api\RotationTrackingController::class, 'getRotationHistory']);
    Route::post('/{id}/rotation/substitute', [\App\Http\Controllers\Api\RotationTrackingController::class, 'substitutePlayer']);
    Route::post('/{id}/rotation/validate', [\App\Http\Controllers\Api\RotationTrackingController::class, 'validateRotation']);
    Route::get('/{id}/rotation/positions', [\App\Http\Controllers\Api\RotationTrackingController::class, 'getAvailablePositions']);
});

// Sanctions Management (Solo para árbitros)
Route::prefix('v1/sanctions')->middleware(['auth:sanctum', 'api.role:Referee,LeagueAdmin,SuperAdmin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\SanctionController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\Api\SanctionController::class, 'store']);
    Route::get('/{id}', [\App\Http\Controllers\Api\SanctionController::class, 'show']);
    Route::post('/{id}/revoke', [\App\Http\Controllers\Api\SanctionController::class, 'revoke']);
    Route::post('/{id}/appeal', [\App\Http\Controllers\Api\SanctionController::class, 'appeal']);
    Route::get('/player/{playerId}/active', [\App\Http\Controllers\Api\SanctionController::class, 'getActiveSanctionsForPlayer']);
    Route::get('/match/{matchId}', [\App\Http\Controllers\Api\SanctionController::class, 'getSanctionsForMatch']);
});

// Webhooks
Route::prefix('v1/webhooks')->group(function () {
    Route::post('/card-status-changed', function (Request $request) {
        return response()->json(['received' => true]);
    });

    Route::post('/medical-alert', function (Request $request) {
        return response()->json(['received' => true]);
    });
});
