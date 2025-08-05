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
use App\Enums\TeamType;

class Team extends Model
{
    use SoftDeletes, LogsActivity, HasSearch;

    protected $fillable = [
        'club_id',
        'team_type',
        'league_id',
        'department_id',
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
        'team_type' => TeamType::class,
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

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
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

    // =======================
    // SCOPES Y MÉTODOS AUXILIARES
    // =======================

    public function scopeClubTeams($query)
    {
        return $query->where('team_type', TeamType::CLUB);
    }

    public function scopeDepartmentalSelections($query)
    {
        return $query->where('team_type', TeamType::SELECTION);
    }

    public function scopeByLeague($query, $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function isClubTeam(): bool
    {
        return $this->team_type === TeamType::CLUB;
    }

    public function isDepartmentalSelection(): bool
    {
        return $this->team_type === TeamType::SELECTION;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->isDepartmentalSelection()) {
            return "Selección {$this->department?->name} - {$this->name}";
        }
        
        return $this->name;
    }

    /**
     * Obtiene jugadores elegibles para una selección departamental
     * (jugadores de clubes del mismo departamento y liga)
     */
    public function getEligiblePlayersForSelection()
    {
        if (!$this->isDepartmentalSelection()) {
            return collect();
        }

        return Player::whereHas('currentClub', function ($query) {
            $query->where('league_id', $this->league_id)
                  ->where('department_id', $this->department_id);
        })
        ->where('gender', $this->gender)
        ->whereHas('category', function ($query) {
            $query->where('id', $this->league_category_id);
        })
        ->where('activa', true)
        ->get();
    }
}
