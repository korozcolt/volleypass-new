<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasSearch;
use App\Enums\CardStatus;
use Illuminate\Support\Facades\Auth;

class CardHistory extends Model
{
    use HasSearch;

    protected $fillable = [
        'player_card_id',
        'player_id',
        'action',
        'previous_status',
        'new_status',
        'reason',
        'additional_data',
        'performed_by',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'previous_status' => CardStatus::class,
        'new_status' => CardStatus::class,
        'additional_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = [
        'action',
        'reason',
        'player.user.name',
        'performer.name'
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

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getPlayerNameAttribute(): string
    {
        return $this->player->user->full_name;
    }

    public function getPerformerNameAttribute(): string
    {
        return $this->performer->name;
    }

    public function getActionDisplayAttribute(): string
    {
        $actions = [
            'created' => 'Carnet creado',
            'activated' => 'Carnet activado',
            'suspended' => 'Carnet suspendido',
            'renewed' => 'Carnet renovado',
            'replaced' => 'Carnet reemplazado',
            'expired' => 'Carnet vencido',
            'medical_restriction_added' => 'Restricción médica agregada',
            'medical_restriction_removed' => 'Restricción médica removida',
        ];

        return $actions[$this->action] ?? $this->action;
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeForCard($query, $cardId)
    {
        return $query->where('player_card_id', $cardId);
    }

    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByPerformer($query, $userId)
    {
        return $query->where('performed_by', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // =======================
    // MÉTODOS ESTÁTICOS
    // =======================

    public static function logAction(
        PlayerCard $card,
        string $action,
        ?CardStatus $previousStatus = null,
        ?CardStatus $newStatus = null,
        ?string $reason = null,
        array $additionalData = [],
        ?User $performer = null
    ): self {
        return self::create([
            'player_card_id' => $card->id,
            'player_id' => $card->player_id,
            'action' => $action,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
            'additional_data' => $additionalData,
            'performed_by' => $performer?->id ?? Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
