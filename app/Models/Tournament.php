<?php

namespace App\Models;

use App\Enums\TournamentStatus;

/**
 * @OA\Schema(
 *     schema="Tournament",
 *     type="object",
 *     title="Tournament",
 *     description="Tournament model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Copa Nacional de Voleibol"),
 *     @OA\Property(property="slug", type="string", example="copa-nacional-voleibol"),
 *     @OA\Property(property="description", type="string", example="Torneo nacional de voleibol"),
 *     @OA\Property(property="league_id", type="integer", example=1),
 *     @OA\Property(property="type", type="string", example="championship"),
 *     @OA\Property(property="format", type="string", example="round_robin"),
 *     @OA\Property(property="category", type="string", example="senior"),
 *     @OA\Property(property="gender", type="string", example="mixed"),
 *     @OA\Property(property="registration_start", type="string", format="date-time"),
 *     @OA\Property(property="registration_end", type="string", format="date-time"),
 *     @OA\Property(property="start_date", type="string", format="date-time"),
 *     @OA\Property(property="end_date", type="string", format="date-time"),
 *     @OA\Property(property="season", type="string", example="2024"),
 *     @OA\Property(property="max_teams", type="integer", example=16),
 *     @OA\Property(property="min_teams", type="integer", example=8),
 *     @OA\Property(property="registration_fee", type="number", format="float", example=100.00),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="is_public", type="boolean", example=true),
 *     @OA\Property(property="venue", type="string", example="Polideportivo Municipal"),
 *     @OA\Property(property="total_teams", type="integer", example=12),
 *     @OA\Property(property="total_matches", type="integer", example=66),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
use App\Enums\TournamentType;
use App\Enums\PlayerCategory;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Tournament extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'league_id',
        'type',
        'format',
        'category',
        'gender',
        'registration_start',
        'registration_end',
        'start_date',
        'end_date',
        'season',
        'max_teams',
        'min_teams',
        'registration_fee',
        'currency',
        'status',
        'is_public',
        'requires_approval',
        'rules',
        'prizes',
        'settings',
        'organizer_id',
        'venue',
        'venue_address',
        'notes',
        'total_teams',
        'total_matches',
        'statistics',
        'metadata',
    ];

    protected $casts = [
        'type' => TournamentType::class,
        'category' => PlayerCategory::class,
        'gender' => Gender::class,
        'status' => TournamentStatus::class,
        'registration_start' => 'date',
        'registration_end' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_fee' => 'decimal:2',
        'rules' => 'array',
        'prizes' => 'array',
        'settings' => 'array',
        'statistics' => 'array',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tournament) {
            if (empty($tournament->slug)) {
                $tournament->slug = Str::slug($tournament->name);
            }
        });
    }

    // Relationships
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function tournamentTeams(): HasMany
    {
        return $this->hasMany(TournamentTeam::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'tournament_teams')
            ->withPivot([
                'registration_status',
                'registered_at',
                'approved_at',
                'group_number',
                'seed_position',
                'roster_players',
                'coaching_staff',
                'registration_notes',
                'registration_fee',
                'fee_paid',
                'fee_paid_at',
                'metadata'
            ])
            ->withTimestamps();
    }

    public function matches(): HasMany
    {
        return $this->hasMany(VolleyMatch::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(TournamentGroup::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(TournamentCard::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(TournamentRound::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, TournamentStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, TournamentType $type)
    {
        return $query->where('tournament_type', $type);
    }

    public function scopeByCategory($query, PlayerCategory $category)
    {
        return $query->where('category', $category);
    }

    // Helper methods
    public function canRegisterTeams(): bool
    {
        return $this->status === TournamentStatus::RegistrationOpen &&
               $this->registered_teams < $this->max_teams &&
               now()->isBefore($this->registration_deadline);
    }

    public function hasMinimumTeams(): bool
    {
        return $this->registered_teams >= $this->min_teams;
    }

    public function canStart(): bool
    {
        return $this->status === TournamentStatus::RegistrationClosed &&
               $this->hasMinimumTeams() &&
               now()->isAfter($this->start_date);
    }

    public function getRegistrationProgress(): float
    {
        if ($this->max_teams === 0) return 0;
        return ($this->registered_teams / $this->max_teams) * 100;
    }

    public function getRemainingSlots(): int
    {
        return max(0, $this->max_teams - $this->registered_teams);
    }

    public function updateRegisteredTeamsCount(): void
    {
        $this->update([
            'registered_teams' => $this->tournamentTeams()
                ->where('registration_status', 'approved')
                ->count()
        ]);
    }

    public function getDefaultGameRules(): array
    {
        return [
            'sets_to_win' => 2, // 2 de 3 sets
            'points_per_set' => 25,
            'tiebreak_points' => 15,
            'table_points' => [3, 1, 0], // Victoria-Derrota-Forfeit
            'timeout_per_set' => 2,
            'substitutions_limit' => 6
        ];
    }

    public function getDefaultGroupConfig(): array
    {
        return [
            'auto_distribution' => true,
            'teams_per_group' => 4,
            'seeding_method' => 'random',
            'balance_groups' => true
        ];
    }

    public function getDefaultPrizesConfig(): array
    {
        return [
            'positions_awarded' => 3,
            'prize_types' => ['trophy', 'medal', 'certificate'],
            'mvp_awards' => true,
            'best_setter' => false,
            'best_spiker' => false,
            'fair_play_award' => true
        ];
    }

    // Group management methods
    public function createGroups(int $teamsPerGroup = 4): void
    {
        $approvedTeams = $this->tournamentTeams()->approved()->get();
        $totalTeams = $approvedTeams->count();
        
        if ($totalTeams < 2) {
            throw new \Exception('Se necesitan al menos 2 equipos para crear grupos.');
        }
        
        $numberOfGroups = ceil($totalTeams / $teamsPerGroup);
        
        // Create groups
        for ($i = 1; $i <= $numberOfGroups; $i++) {
            $this->groups()->create([
                'name' => 'Grupo ' . chr(64 + $i), // A, B, C, etc.
                'group_number' => $i,
                'max_teams' => $teamsPerGroup,
                'is_active' => true
            ]);
        }
        
        // Distribute teams
        $this->distributeTeamsToGroups($approvedTeams);
    }
    
    public function distributeTeamsToGroups($teams): void
    {
        $groups = $this->groups()->active()->get();
        $groupIndex = 0;
        
        foreach ($teams as $index => $tournamentTeam) {
            $group = $groups[$groupIndex % $groups->count()];
            
            if ($group->canAddTeam()) {
                $group->addTeam($tournamentTeam->team_id);
            }
            
            $groupIndex++;
        }
    }
    
    public function generateGroupSchedule(): void
    {
        foreach ($this->groups as $group) {
            $group->generateSchedule();
        }
    }
    
    // Disciplinary methods
    public function getDisciplinaryReport(): array
    {
        $cards = $this->cards()->with(['player', 'team', 'match'])->get();
        
        return [
            'total_cards' => $cards->count(),
            'yellow_cards' => $cards->where('card_type', 'Yellow')->count(),
            'red_cards' => $cards->whereIn('card_type', ['Red', 'RedMatch', 'RedTournament'])->count(),
            'teams_with_cards' => $cards->pluck('team_id')->unique()->count(),
            'players_with_cards' => $cards->pluck('player_id')->unique()->count(),
            'cards_by_violation' => $cards->groupBy('violation_type')->map->count(),
            'cards_by_team' => $cards->groupBy('team.name')->map->count(),
        ];
    }
    
    public function getActiveSuspensions(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->cards()
            ->whereIn('card_type', ['RedMatch', 'RedTournament'])
            ->active()
            ->with(['player', 'team'])
            ->get();
    }
    
    public function canPlayerParticipate(int $playerId): bool
    {
        return !$this->cards()
            ->where('player_id', $playerId)
            ->whereIn('card_type', ['RedMatch', 'RedTournament'])
            ->active()
            ->exists();
    }
    
    // Tournament progression methods
    public function canAdvanceToKnockout(): bool
    {
        if (!$this->groups()->exists()) {
            return false;
        }
        
        return $this->groups()->get()->every(function ($group) {
            return $group->matches()->finished()->count() >= $group->getRequiredMatches();
        });
    }
    
    public function getGroupStandings(): array
    {
        return $this->groups()->with(['teams'])->get()->map(function ($group) {
            return [
                'group' => $group,
                'standings' => $group->getStandingsTable()
            ];
        })->toArray();
    }
}
