<?php

namespace App\Models;

use App\Enums\CardType;
use App\Enums\ViolationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TournamentCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tournament_id',
        'match_id',
        'player_id',
        'team_id',
        'card_type',
        'violation_type',
        'description',
        'referee_notes',
        'set_number',
        'point_number',
        'issued_at',
        'issued_by',
        'is_active',
        'expires_at',
        'sanctions',
        'metadata',
    ];

    protected $casts = [
        'card_type' => CardType::class,
        'violation_type' => ViolationType::class,
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'sanctions' => 'array',
        'metadata' => 'array',
    ];

    // Relationships
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(VolleyMatch::class, 'match_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
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

    public function scopeByPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeByTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeByCardType($query, CardType $cardType)
    {
        return $query->where('card_type', $cardType);
    }

    public function scopeByViolationType($query, ViolationType $violationType)
    {
        return $query->where('violation_type', $violationType);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                    ->whereNotNull('expires_at');
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('expires_at', '>', now())
              ->orWhereNull('expires_at');
        });
    }

    // Helper methods
    public function isYellow(): bool
    {
        return $this->card_type === CardType::Yellow;
    }

    public function isRed(): bool
    {
        return in_array($this->card_type, [CardType::Red, CardType::RedMatch, CardType::RedTournament]);
    }

    public function isMatchSuspension(): bool
    {
        return $this->card_type === CardType::RedMatch;
    }

    public function isTournamentSuspension(): bool
    {
        return $this->card_type === CardType::RedTournament;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isCurrentlyActive(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    public function extend(\DateTime $newExpiryDate): bool
    {
        return $this->update(['expires_at' => $newExpiryDate]);
    }

    public function addSanction(array $sanction): bool
    {
        $sanctions = $this->sanctions ?? [];
        $sanctions[] = array_merge($sanction, [
            'added_at' => now()->toISOString(),
            'added_by' => auth()->id(),
        ]);
        
        return $this->update(['sanctions' => $sanctions]);
    }

    public function getActiveSanctions(): array
    {
        $sanctions = $this->sanctions ?? [];
        
        return array_filter($sanctions, function ($sanction) {
            if (!isset($sanction['expires_at'])) {
                return true;
            }
            
            return now()->isBefore($sanction['expires_at']);
        });
    }

    public function getSeverityLevel(): int
    {
        return match($this->card_type) {
            CardType::Yellow => 1,
            CardType::Red => 2,
            CardType::RedMatch => 3,
            CardType::RedTournament => 4,
            default => 0,
        };
    }

    public function getDisplayText(): string
    {
        $cardText = $this->card_type->getLabel();
        $violationText = $this->violation_type->getLabel();
        
        return "{$cardText} - {$violationText}";
    }

    public function getMatchContext(): string
    {
        if (!$this->match_id) {
            return 'Fuera de partido';
        }
        
        $context = "Set {$this->set_number}";
        
        if ($this->point_number) {
            $context .= ", Punto {$this->point_number}";
        }
        
        return $context;
    }

    public function canBeAppealed(): bool
    {
        // Las tarjetas pueden ser apeladas dentro de 24 horas
        return $this->issued_at->diffInHours(now()) <= 24 && $this->is_active;
    }

    public function getTimeUntilExpiry(): ?string
    {
        if (!$this->expires_at) {
            return null;
        }
        
        if ($this->isExpired()) {
            return 'Expirada';
        }
        
        return $this->expires_at->diffForHumans();
    }

    public static function getPlayerCardHistory(int $playerId, ?int $tournamentId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::where('player_id', $playerId)
                      ->with(['tournament', 'match', 'team', 'issuedBy'])
                      ->orderBy('issued_at', 'desc');
        
        if ($tournamentId) {
            $query->where('tournament_id', $tournamentId);
        }
        
        return $query->get();
    }

    public static function getTeamCardHistory(int $teamId, ?int $tournamentId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::where('team_id', $teamId)
                      ->with(['player', 'tournament', 'match', 'issuedBy'])
                      ->orderBy('issued_at', 'desc');
        
        if ($tournamentId) {
            $query->where('tournament_id', $tournamentId);
        }
        
        return $query->get();
    }

    public static function getTournamentDisciplinaryReport(int $tournamentId): array
    {
        $cards = static::where('tournament_id', $tournamentId)
                      ->with(['player', 'team', 'match'])
                      ->get();
        
        return [
            'total_cards' => $cards->count(),
            'yellow_cards' => $cards->where('card_type', CardType::Yellow)->count(),
            'red_cards' => $cards->whereIn('card_type', [CardType::Red, CardType::RedMatch, CardType::RedTournament])->count(),
            'active_suspensions' => $cards->where('is_active', true)->whereIn('card_type', [CardType::RedMatch, CardType::RedTournament])->count(),
            'cards_by_violation' => $cards->groupBy('violation_type')->map->count(),
            'cards_by_team' => $cards->groupBy('team_id')->map->count(),
            'most_carded_players' => $cards->groupBy('player_id')->map->count()->sortDesc()->take(10),
        ];
    }
}
