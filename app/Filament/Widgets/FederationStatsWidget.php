<?php

namespace App\Filament\Widgets;

use App\Models\Player;
use App\Models\Club;
use App\Models\Payment;
use App\Models\PlayerCard;
use App\Models\League;
use App\Enums\FederationStatus;
use App\Enums\PaymentStatus;
use App\Enums\CardStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FederationStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            $this->getFederatedPlayersStats(),
            $this->getPendingPaymentsStats(),
            $this->getExpiringCardsStats(),
            $this->getActiveClubsStats(),
        ];
    }

    protected function getFederatedPlayersStats(): Stat
    {
        $cacheKey = 'federation_stats.players';
        
        $data = Cache::remember($cacheKey, now()->addMinutes(15), function () {
            $federatedCount = Player::where('federation_status', FederationStatus::Federated)->count();
            $totalCount = Player::count();
            $pendingCount = Player::where('federation_status', FederationStatus::PendingPayment)->count();
            
            return [
                'federated' => $federatedCount,
                'total' => $totalCount,
                'pending' => $pendingCount,
                'percentage' => $totalCount > 0 ? round(($federatedCount / $totalCount) * 100, 1) : 0,
            ];
        });

        $color = match (true) {
            $data['percentage'] >= 80 => 'success',
            $data['percentage'] >= 60 => 'warning',
            default => 'danger',
        };

        return Stat::make('Jugadoras Federadas', number_format($data['federated']))
            ->description("{$data['percentage']}% del total ({$data['total']})")
            ->descriptionIcon('heroicon-m-users')
            ->color($color)
            ->chart($this->getFederationTrendChart())
            ->extraAttributes([
                'class' => 'cursor-pointer',
            ]);
    }

    protected function getPendingPaymentsStats(): Stat
    {
        $cacheKey = 'federation_stats.payments';
        
        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () {
            $pendingCount = Payment::where('status', PaymentStatus::Pending)
                ->where('due_date', '<=', now()->addDays(7))
                ->count();
            
            $overdueCount = Payment::where('status', PaymentStatus::Pending)
                ->where('due_date', '<', now())
                ->count();
            
            $totalAmount = Payment::where('status', PaymentStatus::Pending)
                ->sum('amount');
            
            return [
                'pending' => $pendingCount,
                'overdue' => $overdueCount,
                'amount' => $totalAmount,
            ];
        });

        $color = match (true) {
            $data['overdue'] > 10 => 'danger',
            $data['pending'] > 5 => 'warning',
            default => 'success',
        };

        return Stat::make('Pagos Pendientes', number_format($data['pending']))
            ->description("{$data['overdue']} vencidos - $" . number_format($data['amount'], 0))
            ->descriptionIcon('heroicon-m-credit-card')
            ->color($color)
            ->extraAttributes([
                'class' => 'cursor-pointer',
            ]);
    }

    protected function getExpiringCardsStats(): Stat
    {
        $cacheKey = 'federation_stats.cards';
        
        $data = Cache::remember($cacheKey, now()->addMinutes(20), function () {
            $expiring30 = PlayerCard::where('status', CardStatus::Active)
                ->where('expires_at', '<=', now()->addDays(30))
                ->where('expires_at', '>', now())
                ->count();
            
            $expiring15 = PlayerCard::where('status', CardStatus::Active)
                ->where('expires_at', '<=', now()->addDays(15))
                ->where('expires_at', '>', now())
                ->count();
            
            $expiring7 = PlayerCard::where('status', CardStatus::Active)
                ->where('expires_at', '<=', now()->addDays(7))
                ->where('expires_at', '>', now())
                ->count();
            
            $expired = PlayerCard::where('status', CardStatus::Active)
                ->where('expires_at', '<', now())
                ->count();
            
            return [
                'expiring_30' => $expiring30,
                'expiring_15' => $expiring15,
                'expiring_7' => $expiring7,
                'expired' => $expired,
            ];
        });

        $urgentCount = $data['expiring_7'] + $data['expired'];
        
        $color = match (true) {
            $urgentCount > 20 => 'danger',
            $urgentCount > 10 => 'warning',
            default => 'success',
        };

        return Stat::make('Carnets por Vencer', number_format($data['expiring_30']))
            ->description("{$data['expiring_7']} en 7 días, {$data['expired']} vencidos")
            ->descriptionIcon('heroicon-m-identification')
            ->color($color)
            ->extraAttributes([
                'class' => 'cursor-pointer',
            ]);
    }

    protected function getActiveClubsStats(): Stat
    {
        $cacheKey = 'federation_stats.clubs';
        
        $data = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            $clubsByLeague = Club::with('league')
                ->where('status', 'active')
                ->get()
                ->groupBy('league.name')
                ->map(function ($clubs) {
                    return $clubs->count();
                })
                ->toArray();
            
            $totalActive = array_sum($clubsByLeague);
            $totalClubs = Club::count();
            
            return [
                'active' => $totalActive,
                'total' => $totalClubs,
                'by_league' => $clubsByLeague,
                'percentage' => $totalClubs > 0 ? round(($totalActive / $totalClubs) * 100, 1) : 0,
            ];
        });

        $topLeague = !empty($data['by_league']) 
            ? array_key_first(array_slice($data['by_league'], 0, 1, true))
            : 'N/A';
        
        $topLeagueCount = !empty($data['by_league']) 
            ? array_values(array_slice($data['by_league'], 0, 1, true))[0] ?? 0
            : 0;

        return Stat::make('Clubes Activos', number_format($data['active']))
            ->description("{$data['percentage']}% del total - {$topLeague}: {$topLeagueCount}")
            ->descriptionIcon('heroicon-m-building-office')
            ->color('info')
            ->chart($this->getClubsGrowthChart())
            ->extraAttributes([
                'class' => 'cursor-pointer',
            ]);
    }

    protected function getFederationTrendChart(): array
    {
        $cacheKey = 'federation_stats.trend_chart';
        
        return Cache::remember($cacheKey, now()->addHours(1), function () {
            $data = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $count = Player::where('federation_status', FederationStatus::Federated)
                    ->whereDate('created_at', '<=', $date)
                    ->count();
                $data[] = $count;
            }
            
            return $data;
        });
    }

    protected function getClubsGrowthChart(): array
    {
        $cacheKey = 'federation_stats.clubs_chart';
        
        return Cache::remember($cacheKey, now()->addHours(1), function () {
            $data = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $count = Club::where('status', 'active')
                    ->whereDate('created_at', '<=', $date)
                    ->count();
                $data[] = $count;
            }
            
            return $data;
        });
    }

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->can('view_federation_stats');
    }

    protected function getColumns(): int
    {
        return 4;
    }
}