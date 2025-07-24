<?php

namespace App\Filament\Resources\TransferResource\Pages;

use App\Filament\Resources\TransferResource;
use App\Enums\TransferStatus;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ViewTransfer extends ViewRecord
{
    protected static string $resource = TransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->record->status === TransferStatus::Pending),
            
            Actions\Action::make('approve')
                ->label('Aprobar Traspaso')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Aprobar Traspaso')
                ->modalDescription('¿Está seguro de que desea aprobar este traspaso? Esta acción no se puede deshacer.')
                ->action(function () {
                    $this->record->approve(Auth::user());
                    
                    Notification::make()
                        ->title('Traspaso aprobado exitosamente')
                        ->success()
                        ->send();
                        
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->canBeApproved() && Auth::user()->can('approve_transfers')),

            Actions\Action::make('reject')
                ->label('Rechazar Traspaso')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('Motivo del Rechazo')
                        ->required()
                        ->rows(3)
                        ->placeholder('Explique el motivo por el cual se rechaza este traspaso...'),
                ])
                ->action(function (array $data) {
                    $this->record->reject(Auth::user(), $data['rejection_reason']);
                    
                    Notification::make()
                        ->title('Traspaso rechazado')
                        ->success()
                        ->send();
                        
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->canBeApproved() && Auth::user()->can('approve_transfers')),
        ];
    }
}