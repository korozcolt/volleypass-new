<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamPlayer extends Model
{
    protected $fillable = [
        'team_id',
        'player_id',
        'jersey_number',
        'position',
        'is_captain',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'is_captain' => 'boolean',
        'joined_at' => 'date',
        'left_at' => 'date',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}

