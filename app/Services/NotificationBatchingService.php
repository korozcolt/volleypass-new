<?php

namespace App\Services;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendBatchNotifications; // ✅ AHORA SÍ EXISTE

class NotificationBatchingService
{
    public function batchNotifications(string $type, array $recipients, array $data): void
    {
        $batches = array_chunk($recipients, 50); // 50 por batch

        foreach ($batches as $batch) {
            SendBatchNotifications::dispatch($type, $batch, $data) // ✅ CORREGIDO
                ->delay(now()->addSeconds(rand(1, 30))); // Delay aleatorio
        }
    }

    public function respectRateLimit(int $userId, string $channel): bool
    {
        $key = "notification_rate_limit_{$userId}_{$channel}";
        $hourlyLimit = config('notify.rate_limiting.per_user_per_hour', 10); // ✅ CORREGIDO

        $current = Cache::get($key, 0);

        if ($current >= $hourlyLimit) {
            return false;
        }

        Cache::put($key, $current + 1, 3600); // 1 hora
        return true;
    }

    public function canSendNotification(int $userId, string $type): bool
    {
        // Verificar límites diarios
        $dailyKey = "notification_daily_{$userId}";
        $dailyLimit = config('notify.rate_limiting.per_user_per_day', 50);
        $dailyCount = Cache::get($dailyKey, 0);

        if ($dailyCount >= $dailyLimit) {
            return false;
        }

        // Verificar límites por hora
        return $this->respectRateLimit($userId, $type);
    }

    public function incrementNotificationCount(int $userId): void
    {
        $dailyKey = "notification_daily_{$userId}";
        $current = Cache::get($dailyKey, 0);
        Cache::put($dailyKey, $current + 1, 86400); // 24 horas
    }
}
