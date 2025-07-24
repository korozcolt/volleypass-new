<?php

namespace App\Events;

use App\Models\League;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryConfigurationChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public League $league,
        public array $oldConfiguration,
        public array $newConfiguration,
        public User $changedBy
    ) {}
}
