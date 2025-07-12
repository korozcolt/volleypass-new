<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;
use App\Enums\DocumentType;
use App\Enums\DocumentFormat;
use App\Enums\DocumentStatus;

class PlayerDocument extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, LogsActivity, HasSearch;

    protected $fillable = [
        'player_id',
        'document_type',
        'document_format',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'file_hash',
        'status',
        'rejection_reason',
        'issued_date',
        'expires_at',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'metadata',
        'version',
        'is_required',
        'uploaded_by',
    ];

    protected $casts = [
        'document_type' => DocumentType::class,
        'document_format' => DocumentFormat::class,
        'status' => DocumentStatus::class,
        'issued_date' => 'date',
        'expires_at' => 'date',
        'reviewed_at' => 'datetime',
        'metadata' => 'array',
        'is_required' => 'boolean',
        'file_size' => 'integer',
        'version' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = [
        'original_name',
        'player.user.name',
        'player.user.document_number'
    ];

    // =======================
    // SPATIE CONFIGURATION
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'document_type', 'reviewed_by', 'expires_at'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Documento cargado al sistema',
                'updated' => 'Documento actualizado',
                'deleted' => 'Documento eliminado',
                default => "Documento {$eventName}"
            });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('document')
            ->acceptsMimeTypes([
                'application/pdf',
                'image/jpeg',
                'image/png',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ])
            ->singleFile();
    }

    // =======================
    // RELACIONES
    // =======================

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getPlayerNameAttribute(): string
    {
        return $this->player->user->full_name;
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsExpiringAttribute(): bool
    {
        return $this->expires_at &&
            $this->expires_at->diffInDays(now()) <= 30 &&
            !$this->is_expired;
    }

    public function getDocumentUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('document');
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeApproved($query)
    {
        return $query->where('status', DocumentStatus::Approved);
    }

    public function scopePending($query)
    {
        return $query->where('status', DocumentStatus::Pending);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    public function scopeExpiring($query, int $days = 30)
    {
        return $query->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function scopeByType($query, DocumentType $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    // =======================
    // MÉTODOS DE NEGOCIO
    // =======================

    public function approve(User $reviewer, ?string $notes = null): bool
    {
        if (!$this->status->canTransitionTo(DocumentStatus::Approved)) {
            throw new \Exception("No se puede aprobar un documento en estado {$this->status->getLabel()}");
        }

        return $this->update([
            'status' => DocumentStatus::Approved,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
            'rejection_reason' => null,
        ]);
    }

    public function reject(User $reviewer, string $reason, ?string $notes = null): bool
    {
        if (!$this->status->canTransitionTo(DocumentStatus::Rejected)) {
            throw new \Exception("No se puede rechazar un documento en estado {$this->status->getLabel()}");
        }

        return $this->update([
            'status' => DocumentStatus::Rejected,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
            'review_notes' => $notes,
        ]);
    }

    public function markAsExpired(): bool
    {
        return $this->update(['status' => DocumentStatus::Expired]);
    }

    public function calculateHash(string $filePath): string
    {
        return hash_file('sha256', $filePath);
    }

    public function isValidForEligibility(): bool
    {
        return $this->status === DocumentStatus::Approved &&
            !$this->is_expired;
    }

    public function createNewVersion(array $data): self
    {
        $data['version'] = $this->version + 1;
        $data['player_id'] = $this->player_id;
        $data['document_type'] = $this->document_type;

        return self::create($data);
    }
}
