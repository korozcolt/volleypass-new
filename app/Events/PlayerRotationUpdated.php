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

class PlayerRotationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $match;
    public $teamId;
    public $rotationData;
    public $eventType;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(VolleyMatch $match, int $teamId, array $rotationData, string $eventType = 'rotation')
    {
        $this->match = $match;
        $this->teamId = $teamId;
        $this->rotationData = $rotationData;
        $this->eventType = $eventType;
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
            'team_id' => $this->teamId,
            'rotation_data' => $this->rotationData,
            'event_type' => $this->eventType,
            'timestamp' => $this->timestamp->toISOString(),
            'match' => [
                'id' => $this->match->id,
                'status' => $this->match->status,
                'home_team' => [
                    'id' => $this->match->home_team_id,
                    'name' => $this->match->homeTeam->name ?? 'Equipo Local',
                ],
                'away_team' => [
                    'id' => $this->match->away_team_id,
                    'name' => $this->match->awayTeam->name ?? 'Equipo Visitante',
                ],
            ],
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'PlayerRotationUpdated';
    }
}