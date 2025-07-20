<?php

namespace App\Filament\Resources\LeagueResource\Pages;

use App\Filament\Resources\LeagueResource;
use App\Models\LeagueConfiguration;
use App\Services\LeagueConfigurationService;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditLeague extends EditRecord
{
    protected static string $resource = LeagueResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Información General';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('reload_configurations')
                ->label('Recargar Configuraciones')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function () {
                    app(LeagueConfigurationService::class)->reload($this->record->id);
                    Notification::make()
                        ->title('Configuraciones de liga recargadas exitosamente.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
