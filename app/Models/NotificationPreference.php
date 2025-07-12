<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\NotificationType;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'channel',
        'notification_type',
        'is_enabled',
        'schedule_time',
        'frequency',
        'metadata',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'schedule_time' => 'datetime',
        'metadata' => 'array',
        'notification_type' => NotificationType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener preferencias de un usuario para un tipo específico
     */
    public static function getChannelsFor(User $user, NotificationType $type): array
    {
        return self::where('user_id', $user->id)
            ->where('notification_type', $type)
            ->where('is_enabled', true)
            ->pluck('channel')
            ->toArray();
    }

    /**
     * Crear preferencias por defecto para un usuario
     */
    public static function createDefaultsFor(User $user): void
    {
        $defaults = [
            ['channel' => 'mail', 'notification_type' => NotificationType::Card_Expiry],
            ['channel' => 'mail', 'notification_type' => NotificationType::Medical_Expiry],
            ['channel' => 'mail', 'notification_type' => NotificationType::Document_Approved],
        ];

        foreach ($defaults as $default) {
            self::firstOrCreate([
                'user_id' => $user->id,
                'channel' => $default['channel'],
                'notification_type' => $default['notification_type'],
            ], [
                'is_enabled' => true,
                'frequency' => 'immediate',
            ]);
        }
    }
}
