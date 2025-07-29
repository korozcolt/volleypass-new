<?php

namespace App\Events;

use App\Models\MatchSet;
use App\Models\VolleyMatch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SetUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $matchSet;
    public $match;
    public $eventType;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(MatchSet $matchSet, string $eventType = 'updated')
    {
        $this->matchSet = $matchSet;
        $this->match = $matchSet->match;
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
            'set_id' => $this->matchSet->id,
            'set_number' => $this->matchSet->set_number,
            'home_score' => $this->matchSet->home_score,
            'away_score' => $this->matchSet->away_score,
            'status' => $this->matchSet->status,
            'event_type' => $this->eventType,
            'timestamp' => $this->timestamp->toISOString(),
            'set' => [
                'id' => $this->matchSet->id,
                'set_number' => $this->matchSet->set_number,
                'home_score' => $this->matchSet->home_score,
                'away_score' => $this->matchSet->away_score,
                'status' => $this->matchSet->status,
                'started_at' => $this->matchSet->started_at?->toISOString(),
                'ended_at' => $this->matchSet->ended_at?->toISOString(),
                'duration_minutes' => $this->matchSet->duration_minutes,
            ],
            'match' => [
                'id' => $this->match->id,
                'status' => $this->match->status,
                'home_sets' => $this->match->home_sets,
                'away_sets' => $this->match->away_sets,
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
        return 'SetUpdated';
    }
}