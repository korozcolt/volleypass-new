<?php

namespace App\Filament\Resources\PlayerResource\Widgets;

use App\Models\Player;
use App\Enums\FederationStatus;
use App\Enums\MedicalStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlayerStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPlayers = Player::count();
        $federatedPlayers = Player::where('federation_status', FederationStatus::Federated)->count();
        $notFederatedPlayers = Player::where('federation_status', FederationStatus::NotFederated)->count();
        $pendingPaymentPlayers = Player::whereIn('federation_status', [
            FederationStatus::PendingPayment,
            FederationStatus::PaymentSubmitted
        ])->count();
        $expiredPlayers = Player::where('federation_status', FederationStatus::Expired)
            ->orWhere('federation_expires_at', '<', now())
            ->count();
        $eligiblePlayers = Player::where('is_eligible', true)->count();
        $medicallyFitPlayers = Player::where('medical_status', MedicalStatus::Fit)->count();

        return [
            Stat::make('Total Jugadoras', $totalPlayers)
                ->description('Registradas en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Federadas', $federatedPlayers)
                ->description(($totalPlayers > 0 ? round(($federatedPlayers / $totalPlayers) * 100, 1) : 0) . '% del total')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('No Federadas', $notFederatedPlayers)
                ->description('Requieren federación')
                ->descriptionIcon('heroicon-m-minus-circle')
                ->color('gray'),

            Stat::make('Pago Pendiente', $pendingPaymentPlayers)
                ->description('En proceso de federación')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Federación Vencida', $expiredPlayers)
                ->description('Requieren renovación')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Elegibles', $eligiblePlayers)
                ->description('Pueden participar en torneos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Médicamente Aptas', $medicallyFitPlayers)
                ->description('Estado médico óptimo')
                ->descriptionIcon('heroicon-m-heart')
                ->color('success'),
        ];
    }
}
