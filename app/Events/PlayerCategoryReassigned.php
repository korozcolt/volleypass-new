<?php

namespace App\Events;

use App\Models\Player;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerCategoryReassigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Player $player,
        public string $oldCategory,
        public string $newCategory,
        public string $reason
    ) {}
}