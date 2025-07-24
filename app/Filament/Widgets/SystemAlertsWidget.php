<?php

namespace App\Filament\Widgets;

use App\Models\Player;
use App\Models\MedicalCertificate;
use App\Models\Payment;
use App\Enums\MedicalStatus;
use App\Enums\PaymentStatus;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SystemAlertsWidget extends Widget
{
    protected static string $view = 'filament.widgets.system-alerts';
    
    protected static ?int $sort = 3;
    
    protected static ?string $pollingInterval = '30s';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getViewData(): array
    {
        $cacheKey = 'system.alerts.data';
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            return [
                'medical_alerts' => $this->getMedicalAlerts(),
                'payment_alerts' => $this->getPaymentAlerts(),
                'performance_alerts' => $this->getPerformanceAlerts(),
                'system_alerts' => $this->getSystemAlerts(),
            ];
        });
    }
    
    protected function getMedicalAlerts(): array
    {
        $alerts = [];
        
        // Certificados médicos no aptos
        $unfitCount = MedicalCertificate::where('status', MedicalStatus::Unfit)
            ->count();
        
        if ($unfitCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'heroicon-o-exclamation-triangle',
                'title' => 'Certificados Médicos No Aptos',
                'message' => "{$unfitCount} jugadoras con certificados médicos no aptos.",
                'count' => $unfitCount,
                'action_url' => '/admin/medical-certificates?tableFilters[status][value]=unfit',
                'action_label' => 'Ver Certificados',
            ];
        }
        
        // Certificados con restricciones
        $restrictedCount = MedicalCertificate::where('status', MedicalStatus::Restricted)
            ->count();
        
        if ($restrictedCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'heroicon-o-clock',
                'title' => 'Certificados con Restricciones',
                'message' => "{$restrictedCount} jugadoras con restricciones médicas parciales.",
                'count' => $restrictedCount,
                'action_url' => '/admin/medical-certificates?tableFilters[status][value]=restricted',
                'action_label' => 'Revisar',
            ];
        }
        
        // Certificados en tratamiento
        $underTreatmentCount = MedicalCertificate::where('status', MedicalStatus::Under_Treatment)
            ->where('created_at', '<', now()->subDays(7))
            ->count();
        
        if ($underTreatmentCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'heroicon-o-document-text',
                'title' => 'Jugadoras en Tratamiento',
                'message' => "{$underTreatmentCount} jugadoras llevan más de 7 días en tratamiento médico.",
                'count' => $underTreatmentCount,
                'action_url' => '/admin/medical-certificates?tableFilters[status][value]=under_treatment',
                'action_label' => 'Revisar',
            ];
        }
        
        return $alerts;
    }
    
    protected function getPaymentAlerts(): array
    {
        $alerts = [];
        
        // Pagos pendientes por más de 7 días
        $overduePayments = Payment::where('status', PaymentStatus::Pending)
            ->where('due_date', '<', now()->subDays(7))
            ->count();
        
        if ($overduePayments > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'heroicon-o-currency-dollar',
                'title' => 'Pagos Vencidos',
                'message' => "{$overduePayments} pagos llevan más de 7 días vencidos.",
                'count' => $overduePayments,
                'action_url' => '/admin/payments?tableFilters[overdue][value]=1',
                'action_label' => 'Ver Pagos',
            ];
        }
        
        // Pagos por vencer en 3 días
        $dueSoonPayments = Payment::where('status', PaymentStatus::Pending)
            ->whereBetween('due_date', [now(), now()->addDays(3)])
            ->count();
        
        if ($dueSoonPayments > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'heroicon-o-clock',
                'title' => 'Pagos por Vencer',
                'message' => "{$dueSoonPayments} pagos vencen en los próximos 3 días.",
                'count' => $dueSoonPayments,
                'action_url' => '/admin/payments?tableFilters[due_soon][value]=1',
                'action_label' => 'Revisar',
            ];
        }
        
        // Pagos verificados pendientes de confirmación
        $verifiedPayments = Payment::where('status', PaymentStatus::Verified)
            ->where('updated_at', '<', now()->subDays(2))
            ->count();
        
        if ($verifiedPayments > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'heroicon-o-shield-check',
                'title' => 'Pagos Verificados Pendientes',
                'message' => "{$verifiedPayments} pagos verificados llevan más de 2 días sin procesar.",
                'count' => $verifiedPayments,
                'action_url' => '/admin/payments?tableFilters[status][value]=verified',
                'action_label' => 'Procesar',
            ];
        }
        
        return $alerts;
    }
    
    protected function getPerformanceAlerts(): array
    {
        $alerts = [];
        
        // Verificar queries lentas en los logs
        $slowQueriesCount = $this->getSlowQueriesCount();
        
        if ($slowQueriesCount > 10) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'heroicon-o-chart-bar',
                'title' => 'Queries Lentas Detectadas',
                'message' => "{$slowQueriesCount} queries lentas detectadas en la última hora.",
                'count' => $slowQueriesCount,
                'action_url' => '/admin/system/performance',
                'action_label' => 'Ver Detalles',
            ];
        }
        
        // Verificar cache hit ratio
        $cacheHitRatio = $this->getCacheHitRatio();
        
        if ($cacheHitRatio < 70) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'heroicon-o-server',
                'title' => 'Cache Hit Ratio Bajo',
                'message' => "Cache hit ratio actual: {$cacheHitRatio}%. Se recomienda optimizar.",
                'count' => $cacheHitRatio,
                'action_url' => '/admin/system/cache',
                'action_label' => 'Optimizar Cache',
            ];
        }
        
        // Verificar uso de memoria
        $memoryUsage = $this->getMemoryUsagePercentage();
        
        if ($memoryUsage > 85) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'heroicon-o-cpu-chip',
                'title' => 'Uso Alto de Memoria',
                'message' => "Uso actual de memoria: {$memoryUsage}%. Se recomienda revisar.",
                'count' => $memoryUsage,
                'action_url' => '/admin/system/resources',
                'action_label' => 'Ver Recursos',
            ];
        }
        
        return $alerts;
    }
    
    protected function getSystemAlerts(): array
    {
        $alerts = [];
        
        // Verificar errores en logs
        $recentErrors = $this->getRecentErrorsCount();
        
        if ($recentErrors > 5) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'heroicon-o-exclamation-circle',
                'title' => 'Errores del Sistema',
                'message' => "{$recentErrors} errores detectados en la última hora.",
                'count' => $recentErrors,
                'action_url' => '/admin/system/logs',
                'action_label' => 'Ver Logs',
            ];
        }
        
        // Verificar espacio en disco
        $diskUsage = $this->getDiskUsagePercentage();
        
        if ($diskUsage > 90) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'heroicon-o-server-stack',
                'title' => 'Espacio en Disco Bajo',
                'message' => "Uso actual del disco: {$diskUsage}%. Se requiere limpieza.",
                'count' => $diskUsage,
                'action_url' => '/admin/system/storage',
                'action_label' => 'Gestionar Almacenamiento',
            ];
        } elseif ($diskUsage > 80) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'heroicon-o-server-stack',
                'title' => 'Espacio en Disco Limitado',
                'message' => "Uso actual del disco: {$diskUsage}%. Considere limpieza preventiva.",
                'count' => $diskUsage,
                'action_url' => '/admin/system/storage',
                'action_label' => 'Revisar Almacenamiento',
            ];
        }
        
        // Verificar actualizaciones pendientes
        $pendingUpdates = $this->getPendingUpdatesCount();
        
        if ($pendingUpdates > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'heroicon-o-arrow-down-tray',
                'title' => 'Actualizaciones Disponibles',
                'message' => "{$pendingUpdates} actualizaciones de seguridad disponibles.",
                'count' => $pendingUpdates,
                'action_url' => '/admin/system/updates',
                'action_label' => 'Ver Actualizaciones',
            ];
        }
        
        return $alerts;
    }
    
    protected function getSlowQueriesCount(): int
    {
        // Simular conteo de queries lentas
        // En producción, esto debería leer de logs o métricas reales
        return Cache::remember('system.slow_queries_count', now()->addMinutes(5), function () {
            // Aquí iría la lógica real para contar queries lentas
            return rand(0, 20);
        });
    }
    
    protected function getCacheHitRatio(): int
    {
        // Simular cache hit ratio
        // En producción, esto debería obtener métricas reales de Redis/Cache
        return Cache::remember('system.cache_hit_ratio', now()->addMinutes(10), function () {
            // Aquí iría la lógica real para calcular cache hit ratio
            return rand(60, 95);
        });
    }
    
    protected function getMemoryUsagePercentage(): int
    {
        // Obtener uso real de memoria
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        
        if ($memoryLimit > 0) {
            return (int) (($memoryUsage / $memoryLimit) * 100);
        }
        
        return 0;
    }
    
    protected function parseMemoryLimit(string $limit): int
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit) - 1]);
        $limit = (int) $limit;
        
        switch ($last) {
            case 'g':
                $limit *= 1024;
            case 'm':
                $limit *= 1024;
            case 'k':
                $limit *= 1024;
        }
        
        return $limit;
    }
    
    protected function getRecentErrorsCount(): int
    {
        // Simular conteo de errores recientes
        // En producción, esto debería leer de logs reales
        return Cache::remember('system.recent_errors_count', now()->addMinutes(5), function () {
            // Aquí iría la lógica real para contar errores en logs
            return rand(0, 10);
        });
    }
    
    protected function getDiskUsagePercentage(): int
    {
        // Obtener uso real del disco
        $bytes = disk_free_space('.');
        $total = disk_total_space('.');
        
        if ($total > 0) {
            $used = $total - $bytes;
            return (int) (($used / $total) * 100);
        }
        
        return 0;
    }
    
    protected function getPendingUpdatesCount(): int
    {
        // Simular actualizaciones pendientes
        // En producción, esto podría verificar composer outdated o similar
        return Cache::remember('system.pending_updates_count', now()->addHours(6), function () {
            // Aquí iría la lógica real para verificar actualizaciones
            return rand(0, 5);
        });
    }
    
    public static function canView(): bool
    {
        return true; // Visible para todos los usuarios autenticados
    }
    
    public function getAlertClasses($type): string
    {
        return match($type) {
            'danger' => 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950',
            'warning' => 'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-950',
            'info' => 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950',
            'success' => 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950',
            default => 'border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-950',
        };
    }
    
    public function getIconClasses($type): string
    {
        return match($type) {
            'danger' => 'text-red-500',
            'warning' => 'text-yellow-500',
            'info' => 'text-blue-500',
            'success' => 'text-green-500',
            default => 'text-gray-500',
        };
    }
    
    public function getTitleClasses($type): string
    {
        return match($type) {
            'danger' => 'text-red-800 dark:text-red-200',
            'warning' => 'text-yellow-800 dark:text-yellow-200',
            'info' => 'text-blue-800 dark:text-blue-200',
            'success' => 'text-green-800 dark:text-green-200',
            default => 'text-gray-800 dark:text-gray-200',
        };
    }
    
    public function getMessageClasses($type): string
    {
        return match($type) {
            'danger' => 'text-red-700 dark:text-red-300',
            'warning' => 'text-yellow-700 dark:text-yellow-300',
            'info' => 'text-blue-700 dark:text-blue-300',
            'success' => 'text-green-700 dark:text-green-300',
            default => 'text-gray-700 dark:text-gray-300',
        };
    }
    
    public function getBadgeClasses($type): string
    {
        return match($type) {
            'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }
    
    public function getActionClasses($type): string
    {
        return match($type) {
            'danger' => 'bg-red-600 text-white hover:bg-red-500 focus-visible:outline-red-600',
            'warning' => 'bg-yellow-600 text-white hover:bg-yellow-500 focus-visible:outline-yellow-600',
            'info' => 'bg-blue-600 text-white hover:bg-blue-500 focus-visible:outline-blue-600',
            'success' => 'bg-green-600 text-white hover:bg-green-500 focus-visible:outline-green-600',
            default => 'bg-gray-600 text-white hover:bg-gray-500 focus-visible:outline-gray-600',
        };
    }
}