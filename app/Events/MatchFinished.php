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

class MatchFinished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public VolleyMatch $match;
    public string $winner;
    public array $finalScore;

    /**
     * Create a new event instance.
     */
    public function __construct(VolleyMatch $match, string $winner, array $finalScore = [])
    {
        $this->match = $match;
        $this->winner = $winner;
        $this->finalScore = $finalScore;
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
            new Channel('matches.live'),
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
            'winner' => $this->winner,
            'final_score' => $this->finalScore,
            'home_team' => $this->match->homeTeam->name,
            'away_team' => $this->match->awayTeam->name,
            'tournament_id' => $this->match->tournament_id,
            'finished_at' => $this->match->finished_at,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'match.finished';
    }
}