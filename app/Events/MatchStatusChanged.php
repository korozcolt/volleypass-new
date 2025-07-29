<?php

namespace App\Events;

use App\Models\VolleyMatch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $match;
    public $previousStatus;
    public $newStatus;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(VolleyMatch $match, string $previousStatus, string $newStatus)
    {
        $this->match = $match;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
        $this->timestamp = now();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('matches'),
            new Channel('live-matches'),
            new Channel('match.' . $this->match->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'match_id' => $this->match->id,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'timestamp' => $this->timestamp->toISOString(),
            'match' => [
                'id' => $this->match->id,
                'status' => $this->match->status,
                'started_at' => $this->match->started_at?->toISOString(),
                'finished_at' => $this->match->finished_at?->toISOString(),
                'home_team' => [
                    'id' => $this->match->home_team_id,
                    'name' => $this->match->homeTeam->name ?? 'Equipo Local',
                ],
                'away_team' => [
                    'id' => $this->match->away_team_id,
                    'name' => $this->match->awayTeam->name ?? 'Equipo Visitante',
                ],
                'home_sets' => $this->match->home_sets,
                'away_sets' => $this->match->away_sets,
                'winner_team_id' => $this->match->winner_team_id,
            ],
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'MatchStatusChanged';
    }
}