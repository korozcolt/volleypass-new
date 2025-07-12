<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasSearch;
use App\Enums\VerificationResult;
use App\Enums\EventType;

class QrScanLog extends Model
{
    use HasSearch;

    protected $fillable = [
        'player_card_id',
        'player_id',
        'qr_code_scanned',
        'scan_result',
        'verification_status',
        'scanned_by',
        'scan_location',
        'event_type',
        'match_id',
        'ip_address',
        'user_agent',
        'device_info',
        'latitude',
        'longitude',
        'verification_response',
        'additional_notes',
        'manual_override',
        'override_reason',
        'scanned_at',
        'response_time_ms',
        'debug_info',
        'app_version',
    ];

    protected $casts = [
        'scan_result' => VerificationResult::class,
        'event_type' => EventType::class,
        'device_info' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'verification_response' => 'array',
        'manual_override' => 'boolean',
        'scanned_at' => 'datetime',
        'response_time_ms' => 'integer',
        'debug_info' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = [
        'player.user.name',
        'scanner.name',
        'scan_location',
        'qr_code_scanned'
    ];

    // =======================
    // RELACIONES
    // =======================

    public function playerCard(): BelongsTo
    {
        return $this->belongsTo(PlayerCard::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getPlayerNameAttribute(): string
    {
        return $this->player?->user->full_name ?? 'Jugadora no encontrada';
    }

    public function getScannerNameAttribute(): string
    {
        return $this->scanner->name;
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return $this->scan_result === VerificationResult::Success;
    }

    public function getLocationDisplayAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->scan_location} ({$this->latitude}, {$this->longitude})";
        }
        return $this->scan_location ?? 'Ubicación no especificada';
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeSuccessful($query)
    {
        return $query->where('scan_result', VerificationResult::Success);
    }

    public function scopeFailed($query)
    {
        return $query->where('scan_result', VerificationResult::Error);
    }

    public function scopeForEvent($query, EventType $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByScanner($query, $userId)
    {
        return $query->where('scanned_by', $userId);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('scanned_at', '>=', now()->subDays($days));
    }

    public function scopeAtLocation($query, string $location)
    {
        return $query->where('scan_location', 'like', "%{$location}%");
    }

    // =======================
    // MÉTODOS ESTÁTICOS
    // =======================

    public static function logScan(array $data): self
    {
        return self::create(array_merge($data, [
            'scanned_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]));
    }

    public static function getVerificationStats(int $days = 30): array
    {
        $logs = self::where('scanned_at', '>=', now()->subDays($days));

        return [
            'total_scans' => $logs->count(),
            'successful_scans' => $logs->clone()->successful()->count(),
            'failed_scans' => $logs->clone()->failed()->count(),
            'unique_players' => $logs->clone()->distinct('player_id')->count('player_id'),
            'unique_scanners' => $logs->clone()->distinct('scanned_by')->count('scanned_by'),
            'avg_response_time' => $logs->clone()->whereNotNull('response_time_ms')->avg('response_time_ms'),
        ];
    }
}
