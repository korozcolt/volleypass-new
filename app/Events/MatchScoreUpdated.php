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

class MatchScoreUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $match;
    public $setNumber;
    public $homeScore;
    public $awayScore;
    public $eventType;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(VolleyMatch $match, int $setNumber, int $homeScore, int $awayScore, string $eventType = 'point')
    {
        $this->match = $match;
        $this->setNumber = $setNumber;
        $this->homeScore = $homeScore;
        $this->awayScore = $awayScore;
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
            'set_number' => $this->setNumber,
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore,
            'home_sets' => $this->match->home_sets,
            'away_sets' => $this->match->away_sets,
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
        return 'MatchScoreUpdated';
    }
}