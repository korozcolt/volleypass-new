<?php

namespace App\Events;

use App\Models\VolleyMatch;
use App\Models\MatchSet;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SetCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public VolleyMatch $match;
    public MatchSet $set;
    public string $winner;

    /**
     * Create a new event instance.
     */
    public function __construct(VolleyMatch $match, MatchSet $set, string $winner)
    {
        $this->match = $match;
        $this->set = $set;
        $this->winner = $winner;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('match.' . $this->match->id),
            new Channel('tournament.' . $this->match->tournament_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'match_id' => $this->match->id,
            'set_number' => $this->set->set_number,
            'winner' => $this->winner,
            'home_score' => $this->set->home_score,
            'away_score' => $this->set->away_score,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'set.completed';
    }
}