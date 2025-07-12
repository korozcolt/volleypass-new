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
        'gender',
        'coach_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'category' => PlayerCategory::class,
        'gender' => Gender::class,
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

    public function teamPlayers(): HasMany
    {
        return $this->hasMany(TeamPlayer::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'team_players');
    }
}
