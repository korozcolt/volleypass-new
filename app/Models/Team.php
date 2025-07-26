<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;
use App\Enums\PlayerCategory;
use App\Enums\Gender;

class Team extends Model
{
    use SoftDeletes, LogsActivity, HasSearch;

    protected $fillable = [
        'club_id',
        'name',
        'category',
        'league_category_id',
        'gender',
        'coach_id',
        'assistant_coach_id',
        'captain_id',
        'colors',
        'founded_date',
        'description',
        'status',
        'notes',
        'settings',
    ];

    protected $casts = [
        'category' => PlayerCategory::class,
        'gender' => Gender::class,
        'founded_date' => 'date',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = ['name', 'club.name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'category', 'gender', 'status'])
            ->logOnlyDirty();
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function assistantCoach(): BelongsTo
    {
        return $this->belongsTo(Coach::class, 'assistant_coach_id');
    }

    public function captain(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'captain_id');
    }

    public function teamPlayers(): HasMany
    {
        return $this->hasMany(TeamPlayer::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'team_players');
    }

    public function leagueCategory(): BelongsTo
    {
        return $this->belongsTo(LeagueCategory::class);
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_teams');
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(VolleyMatch::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(VolleyMatch::class, 'away_team_id');
    }

    public function matches()
    {
        return $this->homeMatches()->union($this->awayMatches());
    }
}
