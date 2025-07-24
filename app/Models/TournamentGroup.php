<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class TournamentGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tournament_id',
        'name',
        'slug',
        'description',
        'group_number',
        'max_teams',
        'current_teams',
        'standings',
        'schedule',
        'rules',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'standings' => 'array',
        'schedule' => 'array',
        'rules' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($group) {
            if (empty($group->slug)) {
                $group->slug = Str::slug($group->name);
            }
        });
    }

    // Relationships
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(TournamentTeam::class, 'group_number', 'group_number')
            ->where('tournament_id', $this->tournament_id);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(VolleyMatch::class, 'group_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTournament($query, $tournamentId)
    {
        return $query->where('tournament_id', $tournamentId);
    }

    // Helper methods
    public function canAddTeam(): bool
    {
        return $this->current_teams < $this->max_teams && $this->is_active;
    }

    public function isFull(): bool
    {
        return $this->current_teams >= $this->max_teams;
    }

    public function addTeam(TournamentTeam $tournamentTeam): bool
    {
        if (!$this->canAddTeam()) {
            return false;
        }

        $tournamentTeam->update([
            'group_number' => $this->group_number
        ]);

        $this->increment('current_teams');
        $this->updateStandings();

        return true;
    }

    public function removeTeam(TournamentTeam $tournamentTeam): bool
    {
        if ($tournamentTeam->group_number !== $this->group_number) {
            return false;
        }

        $tournamentTeam->update([
            'group_number' => null
        ]);

        $this->decrement('current_teams');
        $this->updateStandings();

        return true;
    }

    public function updateStandings(): void
    {
        $teams = $this->teams()->with('team')->get();
        $standings = [];

        foreach ($teams as $tournamentTeam) {
            $teamStats = $this->calculateTeamStats($tournamentTeam);
            $standings[] = [
                'tournament_team_id' => $tournamentTeam->id,
                'team_id' => $tournamentTeam->team_id,
                'team_name' => $tournamentTeam->team->name,
                'matches_played' => $teamStats['matches_played'],
                'wins' => $teamStats['wins'],
                'losses' => $teamStats['losses'],
                'sets_won' => $teamStats['sets_won'],
                'sets_lost' => $teamStats['sets_lost'],
                'points_for' => $teamStats['points_for'],
                'points_against' => $teamStats['points_against'],
                'table_points' => $teamStats['table_points'],
                'set_ratio' => $teamStats['set_ratio'],
                'point_ratio' => $teamStats['point_ratio'],
            ];
        }

        // Sort by table points, then by set ratio, then by point ratio
        usort($standings, function ($a, $b) {
            if ($a['table_points'] !== $b['table_points']) {
                return $b['table_points'] <=> $a['table_points'];
            }
            if ($a['set_ratio'] !== $b['set_ratio']) {
                return $b['set_ratio'] <=> $a['set_ratio'];
            }
            return $b['point_ratio'] <=> $a['point_ratio'];
        });

        // Add position
        foreach ($standings as $index => &$standing) {
            $standing['position'] = $index + 1;
        }

        $this->update(['standings' => $standings]);
    }

    private function calculateTeamStats(TournamentTeam $tournamentTeam): array
    {
        $matches = VolleyMatch::where('tournament_id', $this->tournament_id)
            ->where('group_id', $this->id)
            ->where(function ($query) use ($tournamentTeam) {
                $query->where('home_team_id', $tournamentTeam->team_id)
                      ->orWhere('away_team_id', $tournamentTeam->team_id);
            })
            ->where('status', 'finished')
            ->get();

        $stats = [
            'matches_played' => $matches->count(),
            'wins' => 0,
            'losses' => 0,
            'sets_won' => 0,
            'sets_lost' => 0,
            'points_for' => 0,
            'points_against' => 0,
            'table_points' => 0,
        ];

        foreach ($matches as $match) {
            $isHome = $match->home_team_id === $tournamentTeam->team_id;
            $teamSets = $isHome ? $match->home_sets : $match->away_sets;
            $opponentSets = $isHome ? $match->away_sets : $match->home_sets;
            $teamPoints = $isHome ? $match->home_points : $match->away_points;
            $opponentPoints = $isHome ? $match->away_points : $match->home_points;

            $stats['sets_won'] += $teamSets;
            $stats['sets_lost'] += $opponentSets;
            $stats['points_for'] += $teamPoints;
            $stats['points_against'] += $opponentPoints;

            if ($teamSets > $opponentSets) {
                $stats['wins']++;
                $stats['table_points'] += 3; // Victoria
            } else {
                $stats['losses']++;
                $stats['table_points'] += 1; // Derrota
            }
        }

        $stats['set_ratio'] = $stats['sets_lost'] > 0 ? $stats['sets_won'] / $stats['sets_lost'] : $stats['sets_won'];
        $stats['point_ratio'] = $stats['points_against'] > 0 ? $stats['points_for'] / $stats['points_against'] : $stats['points_for'];

        return $stats;
    }

    public function generateSchedule(): array
    {
        $teams = $this->teams()->get();
        $schedule = [];
        
        if ($teams->count() < 2) {
            return $schedule;
        }

        // Round-robin schedule generation
        $teamIds = $teams->pluck('team_id')->toArray();
        $rounds = [];
        
        for ($round = 0; $round < count($teamIds) - 1; $round++) {
            $roundMatches = [];
            for ($i = 0; $i < count($teamIds) / 2; $i++) {
                $home = $teamIds[$i];
                $away = $teamIds[count($teamIds) - 1 - $i];
                
                if ($home !== $away) {
                    $roundMatches[] = [
                        'round' => $round + 1,
                        'home_team_id' => $home,
                        'away_team_id' => $away,
                        'match_date' => null, // To be set later
                    ];
                }
            }
            
            $rounds[] = $roundMatches;
            
            // Rotate teams (keep first team fixed)
            $last = array_pop($teamIds);
            array_splice($teamIds, 1, 0, $last);
        }

        $this->update(['schedule' => $rounds]);
        return $rounds;
    }

    public function getStandingsTable(): array
    {
        return $this->standings ?? [];
    }

    public function getTeamPosition(int $teamId): ?int
    {
        $standings = $this->getStandingsTable();
        
        foreach ($standings as $standing) {
            if ($standing['team_id'] === $teamId) {
                return $standing['position'];
            }
        }
        
        return null;
    }

    public function getGroupLetter(): string
    {
        return chr(64 + $this->group_number); // A, B, C, etc.
    }

    public function getRequiredMatches(): int
    {
        $teamsCount = $this->current_teams;
        
        if ($teamsCount < 2) {
            return 0;
        }
        
        // Round-robin: cada equipo juega contra todos los demás una vez
        // Fórmula: n * (n-1) / 2 donde n es el número de equipos
        return ($teamsCount * ($teamsCount - 1)) / 2;
    }

    public function getCompletedMatches(): int
    {
        return $this->matches()->finished()->count();
    }

    public function isGroupPhaseComplete(): bool
    {
        return $this->getCompletedMatches() >= $this->getRequiredMatches();
    }

    public function getMatchesProgress(): array
    {
        $required = $this->getRequiredMatches();
        $completed = $this->getCompletedMatches();
        
        return [
            'required' => $required,
            'completed' => $completed,
            'remaining' => max(0, $required - $completed),
            'percentage' => $required > 0 ? round(($completed / $required) * 100, 2) : 0
        ];
    }
}
