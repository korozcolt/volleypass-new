<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Aquí puedes agregar widgets específicos para ver el usuario
        ];
    }

    public function getTitle(): string
    {
        return 'Ver Usuario';
    }

    public function getHeading(): string
    {
        return $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $roles = $this->record->roles->pluck('name')->join(', ');
        return $roles ? "Roles: {$roles}" : 'Sin roles asignados';
    }
}
