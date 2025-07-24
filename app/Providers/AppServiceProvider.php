<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Club;
use App\Observers\ClubObserver;

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
        // Model bindings para rutas
        \Illuminate\Support\Facades\Route::model('category', \App\Models\LeagueCategory::class);
        
        // Registrar observers
        Club::observe(ClubObserver::class);
    }
}
