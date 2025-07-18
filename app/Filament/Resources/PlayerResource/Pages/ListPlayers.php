<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use App\Models\Player;
use App\Enums\FederationStatus;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPlayers extends ListRecords
{
    protected static string $resource = PlayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Jugadora')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(Player::count())
                ->badgeColor('primary'),

            'federated' => Tab::make('Federadas')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('federation_status', FederationStatus::Federated)
                )
                ->badge(Player::where('federation_status', FederationStatus::Federated)->count())
                ->badgeColor('success'),

            'not_federated' => Tab::make('No Federadas')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('federation_status', FederationStatus::NotFederated)
                )
                ->badge(Player::where('federation_status', FederationStatus::NotFederated)->count())
                ->badgeColor('gray'),

            'pending_payment' => Tab::make('Pago Pendiente')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereIn('federation_status', [
                        FederationStatus::PendingPayment,
                        FederationStatus::PaymentSubmitted
                    ])
                )
                ->badge(Player::whereIn('federation_status', [
                    FederationStatus::PendingPayment,
                    FederationStatus::PaymentSubmitted
                ])->count())
                ->badgeColor('warning'),

            'expired' => Tab::make('Vencidas')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('federation_status', FederationStatus::Expired)
                          ->orWhere('federation_expires_at', '<', now())
                )
                ->badge(Player::where('federation_status', FederationStatus::Expired)
                    ->orWhere('federation_expires_at', '<', now())
                    ->count())
                ->badgeColor('danger'),

            'suspended' => Tab::make('Suspendidas')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('federation_status', FederationStatus::Suspended)
                )
                ->badge(Player::where('federation_status', FederationStatus::Suspended)->count())
                ->badgeColor('danger'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PlayerResource\Widgets\PlayerStatsOverview::class,
        ];
    }
}
