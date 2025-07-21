<?php

namespace App\Events;

use App\Models\Player;
use App\Models\League;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentsApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Player $player,
        public League $league,
        public User $approver,
        public array $approvedDocuments = []
    ) {}
}
