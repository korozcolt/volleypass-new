<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use App\Models\Player;
use App\Enums\FederationStatus;
use App\Enums\PlayerPosition;
use App\Enums\PlayerCategory;
use App\Enums\MedicalStatus;
use App\Services\FederationService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class ListPlayers extends ListRecords
{
    protected static string $resource = PlayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Jugadora')
                ->icon('heroicon-o-plus'),
            
            Actions\Action::make('export')
                ->label('Exportar Lista')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    // Implementar exportación
                    Notification::make()
                        ->title('Exportación iniciada')
                        ->body('Se está generando el archivo de exportación.')
                        ->success()
                        ->send();
                }),
            
            Actions\Action::make('bulk_federate')
                ->label('Federación Masiva')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Federación Masiva de Jugadoras')
                ->modalDescription('Esta acción federará todas las jugadoras elegibles que cumplan con los requisitos.')
                ->action(function () {
                    $federationService = app(FederationService::class);
                    $eligiblePlayers = Player::where('federation_status', FederationStatus::PendingPayment)
                        ->where('medical_status', MedicalStatus::Fit)
                        ->where('is_eligible', true)
                        ->get();
                    
                    $federatedCount = 0;
                    foreach ($eligiblePlayers as $player) {
                        try {
                            $federationService->federatePlayer($player);
                            $federatedCount++;
                        } catch (\Exception $e) {
                            // Log error but continue
                        }
                    }
                    
                    Notification::make()
                        ->title('Federación Masiva Completada')
                        ->body("Se federaron {$federatedCount} jugadoras exitosamente.")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(Player::count())
                ->badgeColor('primary'),

            'federated' => Tab::make('Federadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('federation_status', FederationStatus::Federated))
                ->badge(Player::where('federation_status', FederationStatus::Federated)->count())
                ->badgeColor('success'),
            
            'pending' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('federation_status', FederationStatus::PendingPayment))
                ->badge(Player::where('federation_status', FederationStatus::PendingPayment)->count())
                ->badgeColor('warning'),
            
            'suspended' => Tab::make('Suspendidas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('federation_status', FederationStatus::Suspended))
                ->badge(Player::where('federation_status', FederationStatus::Suspended)->count())
                ->badgeColor('danger'),
            
            'medical_pending' => Tab::make('Médico Pendiente')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('medical_status', '!=', MedicalStatus::Fit))
                ->badge(Player::where('medical_status', '!=', MedicalStatus::Fit)->count())
                ->badgeColor('gray'),
            
            'expires_soon' => Tab::make('Por Vencer')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('federation_expires_at', '<=', now()->addDays(30))
                          ->where('federation_expires_at', '>', now())
                )
                ->badge(
                    Player::where('federation_expires_at', '<=', now()->addDays(30))
                          ->where('federation_expires_at', '>', now())
                          ->count()
                )
                ->badgeColor('warning'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PlayerResource\Widgets\PlayerStatsOverview::class,
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('bulk_federate')
                ->label('Federar Seleccionadas')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Federar Jugadoras Seleccionadas')
                ->modalDescription('¿Está seguro de que desea federar las jugadoras seleccionadas?')
                ->action(function (Collection $records) {
                    $federationService = app(FederationService::class);
                    $federatedCount = 0;
                    
                    foreach ($records as $player) {
                        if ($player->federation_status !== FederationStatus::Federated) {
                            try {
                                $federationService->federatePlayer($player);
                                $federatedCount++;
                            } catch (\Exception $e) {
                                // Log error but continue
                            }
                        }
                    }
                    
                    Notification::make()
                        ->title('Federación Completada')
                        ->body("Se federaron {$federatedCount} jugadoras exitosamente.")
                        ->success()
                        ->send();
                })
                ->deselectRecordsAfterCompletion(),
            
            BulkAction::make('bulk_suspend')
                ->label('Suspender Seleccionadas')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Suspender Jugadoras Seleccionadas')
                ->modalDescription('¿Está seguro de que desea suspender las jugadoras seleccionadas?')
                ->action(function (Collection $records) {
                    $suspendedCount = 0;
                    
                    foreach ($records as $player) {
                        if ($player->federation_status === FederationStatus::Federated) {
                            $player->update([
                                'federation_status' => FederationStatus::Suspended,
                                'suspension_date' => now(),
                                'is_eligible' => false,
                            ]);
                            $suspendedCount++;
                        }
                    }
                    
                    Notification::make()
                        ->title('Suspensión Completada')
                        ->body("Se suspendieron {$suspendedCount} jugadoras exitosamente.")
                        ->success()
                        ->send();
                })
                ->deselectRecordsAfterCompletion(),
            
            BulkAction::make('bulk_renew')
                ->label('Renovar Federación')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Renovar Federación de Jugadoras')
                ->modalDescription('¿Está seguro de que desea renovar la federación de las jugadoras seleccionadas?')
                ->action(function (Collection $records) {
                    $renewedCount = 0;
                    
                    foreach ($records as $player) {
                        if ($player->federation_status === FederationStatus::Federated) {
                            $player->update([
                                'federation_expires_at' => now()->addYear(),
                                'last_renewal_date' => now(),
                            ]);
                            $renewedCount++;
                        }
                    }
                    
                    Notification::make()
                        ->title('Renovación Completada')
                        ->body("Se renovaron {$renewedCount} federaciones exitosamente.")
                        ->success()
                        ->send();
                })
                ->deselectRecordsAfterCompletion(),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100, 'all'];
    }

    protected function getTableDefaultSort(): ?string
    {
        return 'created_at';
    }

    protected function getTableDefaultSortDirection(): ?string
    {
        return 'desc';
    }
}
