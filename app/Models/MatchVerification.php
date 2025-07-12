<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;
use App\Enums\EventType;

class MatchVerification extends Model
{
    use LogsActivity, HasSearch;

    protected $fillable = [
        'event_name',
        'event_type',
        'event_date',
        'event_time',
        'venue',
        'home_team_id',
        'away_team_id',
        'teams_description',
        'verifier_id',
        'verification_started_at',
        'verification_completed_at',
        'total_players_verified',
        'approved_players',
        'rejected_players',
        'players_with_restrictions',
        'status',
        'verification_notes',
        'summary_report',
        'latitude',
        'longitude',
        'ip_address',
        'device_info',
    ];

    protected $casts = [
        'event_type' => EventType::class,
        'event_date' => 'date',
        'event_time' => 'datetime',
        'verification_started_at' => 'datetime',
        'verification_completed_at' => 'datetime',
        'total_players_verified' => 'integer',
        'approved_players' => 'integer',
        'rejected_players' => 'integer',
        'players_with_restrictions' => 'integer',
        'summary_report' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'device_info' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = [
        'event_name',
        'venue',
        'verifier.name',
        'teams_description'
    ];

    // =======================
    // SPATIE CONFIGURATION
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'status',
                'total_players_verified',
                'approved_players',
                'rejected_players'
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Verificación de partido iniciada',
                'updated' => 'Verificación de partido actualizada',
                'deleted' => 'Verificación de partido eliminada',
                default => "Verificación {$eventName}"
            });
    }

    // =======================
    // RELACIONES
    // =======================

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    public function qrScanLogs(): HasMany
    {
        return $this->hasMany(QrScanLog::class, 'match_id');
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getVerifierNameAttribute(): string
    {
        return $this->verifier->name;
    }

    public function getTeamsDisplayAttribute(): string
    {
        if ($this->homeTeam && $this->awayTeam) {
            return $this->homeTeam->name . ' vs ' . $this->awayTeam->name;
        }
        return $this->teams_description ?? 'Equipos no especificados';
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->verification_started_at || !$this->verification_completed_at) {
            return null;
        }

        return $this->verification_started_at
            ->diff($this->verification_completed_at)
            ->format('%H:%I:%S');
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->total_players_verified === 0) {
            return 0;
        }

        return round(($this->approved_players / $this->total_players_verified) * 100, 2);
    }

    public function getLocationDisplayAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->venue} ({$this->latitude}, {$this->longitude})";
        }
        return $this->venue;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed' &&
            $this->verification_completed_at !== null;
    }

    public function getIsInProgressAttribute(): bool
    {
        return $this->status === 'in_progress';
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeByEventType($query, EventType $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByVerifier($query, $verifierId)
    {
        return $query->where('verifier_id', $verifierId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('event_date', $date);
    }

    public function scopeAtVenue($query, string $venue)
    {
        return $query->where('venue', 'like', "%{$venue}%");
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('event_date', '>=', now()->subDays($days));
    }

    // =======================
    // MÉTODOS DE NEGOCIO
    // =======================

    public function startVerification(): void
    {
        $this->update([
            'verification_started_at' => now(),
            'status' => 'in_progress'
        ]);
    }

    public function completeVerification(array $summary = []): void
    {
        $this->update([
            'verification_completed_at' => now(),
            'status' => 'completed',
            'summary_report' => array_merge($this->summary_report ?? [], $summary)
        ]);
    }

    public function updateStats(): void
    {
        $logs = $this->qrScanLogs();

        $this->update([
            'total_players_verified' => $logs->count(),
            'approved_players' => $logs->clone()->where('verification_status', 'apta')->count(),
            'rejected_players' => $logs->clone()->where('verification_status', 'no_apta')->count(),
            'players_with_restrictions' => $logs->clone()->where('verification_status', 'restriccion')->count(),
        ]);
    }

    public function generateReport(): array
    {
        $logs = $this->qrScanLogs()->with(['player.user', 'scanner'])->get();

        return [
            'event_info' => [
                'name' => $this->event_name,
                'type' => $this->event_type->getLabel(),
                'date' => $this->event_date->format('Y-m-d'),
                'venue' => $this->venue,
                'teams' => $this->teams_display,
            ],
            'verification_summary' => [
                'verifier' => $this->verifier_name,
                'started_at' => $this->verification_started_at?->format('Y-m-d H:i:s'),
                'completed_at' => $this->verification_completed_at?->format('Y-m-d H:i:s'),
                'duration' => $this->duration,
                'total_verified' => $this->total_players_verified,
                'approved' => $this->approved_players,
                'rejected' => $this->rejected_players,
                'with_restrictions' => $this->players_with_restrictions,
                'success_rate' => $this->success_rate . '%',
            ],
            'player_details' => $logs->map(function ($log) {
                return [
                    'player_name' => $log->player_name,
                    'verification_status' => $log->verification_status,
                    'scanned_at' => $log->scanned_at->format('H:i:s'),
                    'notes' => $log->additional_notes,
                ];
            })->toArray(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    public static function getVerificationSummary(int $days = 30): array
    {
        $verifications = self::recent($days);

        return [
            'total_events' => $verifications->count(),
            'completed_events' => $verifications->clone()->completed()->count(),
            'total_players_verified' => $verifications->sum('total_players_verified'),
            'total_approved' => $verifications->sum('approved_players'),
            'total_rejected' => $verifications->sum('rejected_players'),
            'average_success_rate' => $verifications->avg('success_rate'),
            'top_verifiers' => $verifications->with('verifier')
                ->get()
                ->groupBy('verifier.name')
                ->map->count()
                ->sortDesc()
                ->take(5)
                ->toArray(),
        ];
    }
}
