<?php

namespace App\Filament\Widgets;

use App\Models\Player;
use App\Models\QrScanLog;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LiveMetricsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    
    protected static ?string $pollingInterval = '10s';
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        return [
            $this->getQrVerificationsStats(),
            $this->getNewRegistrationsStats(),
            $this->getSystemPerformanceStats(),
            $this->getActiveUsersStats(),
        ];
    }
    
    protected function getQrVerificationsStats(): Stat
    {
        $cacheKey = 'live_metrics.qr_verifications';
        
        $data = Cache::remember($cacheKey, now()->addMinutes(1), function () {
            $lastHour = QrScanLog::where('created_at', '>=', now()->subHour())
                ->count();
            
            $previousHour = QrScanLog::whereBetween('created_at', [
                now()->subHours(2),
                now()->subHour()
            ])->count();
            
            $trend = $previousHour > 0 
                ? (($lastHour - $previousHour) / $previousHour) * 100 
                : ($lastHour > 0 ? 100 : 0);
            
            return [
                'count' => $lastHour,
                'trend' => round($trend, 1),
                'is_increasing' => $trend >= 0,
            ];
        });
        
        return Stat::make('Verificaciones QR (Última Hora)', $data['count'])
            ->description($data['is_increasing'] ? "+{$data['trend']}% vs hora anterior" : "{$data['trend']}% vs hora anterior")
            ->descriptionIcon($data['is_increasing'] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($data['is_increasing'] ? 'success' : 'danger')
            ->chart($this->getQrVerificationChart())
            ->extraAttributes([
                'class' => 'cursor-pointer transition-all hover:scale-105',
                'wire:click' => '$dispatch("open-qr-details")',
            ]);
    }
    
    protected function getNewRegistrationsStats(): Stat
    {
        $cacheKey = 'live_metrics.new_registrations';
        
        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $today = Player::whereDate('created_at', today())
                ->count();
            
            $yesterday = Player::whereDate('created_at', today()->subDay())
                ->count();
            
            $trend = $yesterday > 0 
                ? (($today - $yesterday) / $yesterday) * 100 
                : ($today > 0 ? 100 : 0);
            
            return [
                'count' => $today,
                'trend' => round($trend, 1),
                'is_increasing' => $trend >= 0,
            ];
        });
        
        return Stat::make('Registros Nuevos (Hoy)', $data['count'])
            ->description($data['is_increasing'] ? "+{$data['trend']}% vs ayer" : "{$data['trend']}% vs ayer")
            ->descriptionIcon($data['is_increasing'] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($data['is_increasing'] ? 'success' : 'warning')
            ->chart($this->getRegistrationChart())
            ->extraAttributes([
                'class' => 'cursor-pointer transition-all hover:scale-105',
                'wire:click' => '$dispatch("open-registration-details")',
            ]);
    }
    
    protected function getSystemPerformanceStats(): Stat
    {
        $cacheKey = 'live_metrics.system_performance';
        
        $data = Cache::remember($cacheKey, now()->addMinutes(2), function () {
            // Simular métricas de performance
            $avgResponseTime = $this->getAverageResponseTime();
            $previousAvg = Cache::get('system.previous_avg_response_time', $avgResponseTime);
            
            $trend = $previousAvg > 0 
                ? (($avgResponseTime - $previousAvg) / $previousAvg) * 100 
                : 0;
            
            // Guardar para próxima comparación
            Cache::put('system.previous_avg_response_time', $avgResponseTime, now()->addHours(1));
            
            return [
                'avg_time' => $avgResponseTime,
                'trend' => round($trend, 1),
                'is_improving' => $trend <= 0, // Menor tiempo es mejor
            ];
        });
        
        $color = match(true) {
            $data['avg_time'] <= 100 => 'success',
            $data['avg_time'] <= 200 => 'warning',
            default => 'danger'
        };
        
        return Stat::make('Performance Promedio', $data['avg_time'] . 'ms')
            ->description($data['is_improving'] ? "Mejora de {$data['trend']}%" : "Degradación de +{$data['trend']}%")
            ->descriptionIcon($data['is_improving'] ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
            ->color($color)
            ->chart($this->getPerformanceChart())
            ->extraAttributes([
                'class' => 'cursor-pointer transition-all hover:scale-105',
                'wire:click' => '$dispatch("open-performance-details")',
            ]);
    }
    
    protected function getActiveUsersStats(): Stat
    {
        $cacheKey = 'live_metrics.active_users';
        
        $data = Cache::remember($cacheKey, now()->addMinutes(1), function () {
            // Usuarios activos en los últimos 15 minutos
            $activeUsers = User::where('last_activity_at', '>=', now()->subMinutes(15))
                ->count();
            
            // Usuarios activos en los 15 minutos anteriores
            $previousActive = User::whereBetween('last_activity_at', [
                now()->subMinutes(30),
                now()->subMinutes(15)
            ])->count();
            
            $trend = $previousActive > 0 
                ? (($activeUsers - $previousActive) / $previousActive) * 100 
                : ($activeUsers > 0 ? 100 : 0);
            
            return [
                'count' => $activeUsers,
                'trend' => round($trend, 1),
                'is_increasing' => $trend >= 0,
            ];
        });
        
        return Stat::make('Usuarios Activos (15min)', $data['count'])
            ->description($data['is_increasing'] ? "+{$data['trend']}% actividad" : "{$data['trend']}% actividad")
            ->descriptionIcon($data['is_increasing'] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($data['is_increasing'] ? 'success' : 'info')
            ->chart($this->getActiveUsersChart())
            ->extraAttributes([
                'class' => 'cursor-pointer transition-all hover:scale-105',
                'wire:click' => '$dispatch("open-users-details")',
            ]);
    }
    
    protected function getQrVerificationChart(): array
    {
        $cacheKey = 'chart.qr_verifications_hourly';
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $data = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $hour = now()->subHours($i);
                $count = QrScanLog::whereBetween('created_at', [
                    $hour->copy()->startOfHour(),
                    $hour->copy()->endOfHour()
                ])->count();
                
                $data[] = $count;
            }
            
            return $data;
        });
    }

    /**
     * Get performance background color based on response time
     */
    protected function getPerformanceColor(int $responseTime): string
    {
        if ($responseTime <= 100) {
            return 'bg-green-100 dark:bg-green-900';
        } elseif ($responseTime <= 200) {
            return 'bg-yellow-100 dark:bg-yellow-900';
        } else {
            return 'bg-red-100 dark:bg-red-900';
        }
    }

    /**
     * Get performance icon color based on response time
     */
    protected function getPerformanceIconColor(int $responseTime): string
    {
        if ($responseTime <= 100) {
            return 'text-green-600 dark:text-green-400';
        } elseif ($responseTime <= 200) {
            return 'text-yellow-600 dark:text-yellow-400';
        } else {
            return 'text-red-600 dark:text-red-400';
        }
    }

    /**
     * Get performance bar color based on response time
     */
    protected function getPerformanceBarColor(int $responseTime): string
    {
        if ($responseTime <= 100) {
            return 'bg-green-500';
        } elseif ($responseTime <= 200) {
            return 'bg-yellow-500';
        } else {
            return 'bg-red-500';
        }
    }
    
    protected function getRegistrationChart(): array
    {
        $cacheKey = 'chart.registrations_daily';
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            $data = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = today()->subDays($i);
                $count = Player::whereDate('created_at', $date)->count();
                $data[] = $count;
            }
            
            return $data;
        });
    }
    
    protected function getPerformanceChart(): array
    {
        $cacheKey = 'chart.performance_hourly';
        
        return Cache::remember($cacheKey, now()->addMinutes(10), function () {
            $data = [];
            
            // Simular datos de performance por hora
            for ($i = 11; $i >= 0; $i--) {
                // En producción, esto vendría de métricas reales
                $baseTime = 150;
                $variation = rand(-50, 100);
                $data[] = max(50, $baseTime + $variation);
            }
            
            return $data;
        });
    }
    
    protected function getActiveUsersChart(): array
    {
        $cacheKey = 'chart.active_users_hourly';
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $data = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $hour = now()->subHours($i);
                
                // Contar usuarios únicos activos en esa hora
                $count = User::where('last_activity_at', '>=', $hour->copy()->startOfHour())
                    ->where('last_activity_at', '<=', $hour->copy()->endOfHour())
                    ->count();
                
                $data[] = $count;
            }
            
            return $data;
        });
    }
    
    protected function getAverageResponseTime(): int
    {
        // Simular tiempo de respuesta promedio
        // En producción, esto vendría de métricas reales de APM
        $cacheKey = 'system.avg_response_time';
        
        return Cache::remember($cacheKey, now()->addMinutes(1), function () {
            // Simular variación en el tiempo de respuesta
            $baseTime = 120; // 120ms base
            $variation = rand(-30, 80);
            return max(50, $baseTime + $variation);
        });
    }
    
    public function getHeading(): ?string
    {
        return 'Métricas en Tiempo Real';
    }
    
    public function getDescription(): ?string
    {
        return 'Monitoreo en vivo del sistema - Actualización cada 10 segundos';
    }
    
    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->can('view_live_metrics');
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}