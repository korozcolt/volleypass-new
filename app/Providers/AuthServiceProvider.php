<?php

namespace App\Providers;

use App\Models\Club;
use App\Policies\ClubPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Club::class => ClubPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir gates adicionales si es necesario
        Gate::define('view_federation_stats', function ($user) {
            return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
        });

        // Definir gates específicos para ClubResource
        Gate::define('view_any_club', function ($user) {
            return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
        });

        Gate::define('view_club', function ($user, $club) {
            return $user->can('view', $club);
        });

        Gate::define('create_club', function ($user) {
            return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
        });

        Gate::define('update_club', function ($user, $club) {
            return $user->can('update', $club);
        });

        Gate::define('delete_club', function ($user, $club) {
            return $user->can('delete', $club);
        });
    }
}