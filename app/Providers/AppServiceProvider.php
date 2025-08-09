<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Club;
use App\Models\Player;
use App\Observers\ClubObserver;
use App\Observers\PlayerObserver;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
        // Model bindings para rutas
        \Illuminate\Support\Facades\Route::model('category', \App\Models\LeagueCategory::class);

        // Registrar observers
        Club::observe(ClubObserver::class);
        Player::observe(PlayerObserver::class);
    }
}
