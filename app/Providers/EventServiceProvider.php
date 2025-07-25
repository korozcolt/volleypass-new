<?php

namespace App\Providers;

use App\Events\DocumentsApproved;
use App\Listeners\TriggerAutomaticCardGeneration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Sistema de Carnetización Automática
        DocumentsApproved::class => [
            TriggerAutomaticCardGeneration::class,
        ],
        
        // Sistema de Categorías Dinámicas - Notificaciones
        \App\Events\CategoryConfigurationChanged::class => [
            \App\Listeners\NotifyCategoryConfigurationChanged::class,
        ],
        
        \App\Events\PlayerCategoryReassigned::class => [
            \App\Listeners\NotifyPlayerCategoryReassigned::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
