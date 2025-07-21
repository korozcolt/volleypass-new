<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\CardGenerationStatus;
use App\Traits\HasSearch;

class CardGenerationLog extends Model
{
    use HasSearch;

    protected $fillable = [
        'player_id',
        'league_id',
        'player_card_id',
        'card_number',
        'status',
        'processing_time_ms',
        'error_message',
        'metadata',
        'generated_by',
        'triggered_by',
        'started_at',
        'completed_at',
        'validation_results',
        'retry_count',
        'last_retry_at',
    ];

    protected $casts = [
        'status' => CardGenerationStatus::class,
        'processing_time_ms' => 'integer',
        'metadata' => 'array',
        'validation_results' => 'array',
        'retry_count' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_retry_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = [
        'card_number',
        'player.user.name',
        'league.name',
        'error_message'
    ];

    // =======================
    // RELACIONES
    // =======================

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function playerCard(): BelongsTo
    {
        return $this->belongsTo(PlayerCard::class);
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getPlayerNameAttribute(): string
    {
        return $this->player->user->full_name;
    }

    public function getLeagueNameAttribute(): string
    {
        return $this->league->name;
    }

    public function getProcessingTimeSecondsAttribute(): ?float
    {
        return $this->processing_time_ms ? round($this->processing_time_ms / 1000, 2) : null;
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return $this->status === CardGenerationStatus::Completed;
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === CardGenerationStatus::Failed;
    }

    public function getIsInProgressAttribute(): bool
    {
        return $this->status->isInProgress();
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeSuccessful($query)
    {
        return $query->where('status', CardGenerationStatus::Completed);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', CardGenerationStatus::Failed);
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', [
            CardGenerationStatus::Pending,
            CardGenerationStatus::Validating,
            CardGenerationStatus::Generating,
            CardGenerationStatus::Retrying
        ]);
    }

    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeForLeague($query, $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeWithRetries($query)
    {
        return $query->where('retry_count', '>', 0);
    }

    // =======================
    // MÉTODOS ESTÁTICOS
    // =======================

    public static function logGeneration(array $data): self
    {
        return self::create(array_merge($data, [
            'started_at' => now(),
            'generated_by' => $data['generated_by'] ?? 'system_auto',
        ]));
    }

    public static function getGenerationStats(int $days = 30): array
    {
        $logs = self::where('created_at', '>=', now()->subDays($days));

        $successful = $logs->clone()->successful()->count();
        $failed = $logs->clone()->failed()->count();
        $total = $logs->count();

        return [
            'total_generations' => $total,
            'successful_generations' => $successful,
            'failed_generations' => $failed,
            'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
            'avg_processing_time' => $logs->clone()->successful()->avg('processing_time_ms'),
            'total_retries' => $logs->clone()->sum('retry_count'),
            'unique_players' => $logs->clone()->distinct('player_id')->count('player_id'),
            'unique_leagues' => $logs->clone()->distinct('league_id')->count('league_id'),
        ];
    }

    public static function getErrorStats(int $days = 30): array
    {
        return self::failed()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('error_message, COUNT(*) as count')
            ->groupBy('error_message')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'error_message')
            ->toArray();
    }

    // =======================
    // MÉTODOS DE INSTANCIA
    // =======================

    public function markAsCompleted(PlayerCard $card, int $processingTimeMs = null): void
    {
        $this->update([
            'status' => CardGenerationStatus::Completed,
            'player_card_id' => $card->id,
            'card_number' => $card->card_number,
            'completed_at' => now(),
            'processing_time_ms' => $processingTimeMs ?? $this->getProcessingTime(),
        ]);
    }

    public function markAsFailed(string $errorMessage, array $metadata = []): void
    {
        $this->update([
            'status' => CardGenerationStatus::Failed,
            'error_message' => $errorMessage,
            'completed_at' => now(),
            'processing_time_ms' => $this->getProcessingTime(),
            'metadata' => array_merge($this->metadata ?? [], $metadata),
        ]);
    }

    public function incrementRetry(): void
    {
        $this->increment('retry_count');
        $this->update([
            'status' => CardGenerationStatus::Retrying,
            'last_retry_at' => now(),
        ]);
    }

    public function updateStatus(CardGenerationStatus $status, array $metadata = []): void
    {
        $this->update([
            'status' => $status,
            'metadata' => array_merge($this->metadata ?? [], $metadata),
        ]);
    }

    private function getProcessingTime(): ?int
    {
        if (!$this->started_at) {
            return null;
        }

        return now()->diffInMilliseconds($this->started_at);
    }
}
