<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;

class ViewClub extends ViewRecord
{
    protected static string $resource = ClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Club'),
            Actions\DeleteAction::make()
                ->label('Eliminar')
                ->requiresConfirmation()
                ->modalHeading('Eliminar Club')
                ->modalDescription('¿Estás seguro de que deseas eliminar este club? Esta acción no se puede deshacer.')
                ->modalSubmitActionLabel('Sí, eliminar'),
        ];
    }

    public function getTitle(): string
    {
        return $this->record->name;
    }

    public function getSubheading(): ?string
    {
        return 'Información detallada del club';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(3)
                    ->schema([
                        Section::make('Información General')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nombre del Club')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg'),
                                TextEntry::make('short_name')
                                    ->label('Nombre Corto'),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable(),
                                TextEntry::make('phone')
                                    ->label('Teléfono')
                                    ->copyable(),
                                TextEntry::make('foundation_date')
                                    ->label('Fecha de Fundación')
                                    ->date(),
                                TextEntry::make('created_at')
                                    ->label('Fecha de Registro')
                                    ->dateTime(),
                            ])
                            ->columnSpan(1)
                            ->extraAttributes(['class' => 'mb-6']),
                        
                        Section::make('Estado y Federación')
                            ->schema([
                                TextEntry::make('is_federated')
                                    ->label('Estado de Federación')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Federado' : 'No Federado')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                                TextEntry::make('federation_type')
                                    ->label('Tipo de Federación')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'nacional' => 'success',
                                        'departamental' => 'warning',
                                        'municipal' => 'info',
                                        default => 'gray',
                                    })
                                    ->visible(fn ($record) => $record->is_federated),
                                TextEntry::make('federation_code')
                                    ->label('Código de Federación')
                                    ->copyable()
                                    ->visible(fn ($record) => $record->is_federated),
                                TextEntry::make('federation_expiry')
                                    ->label('Vencimiento Federación')
                                    ->date()
                                    ->visible(fn ($record) => $record->is_federated),
                            ])
                            ->columnSpan(1)
                            ->extraAttributes(['class' => 'mb-6']),
                        
                        Section::make('Logo del Club')
                            ->schema([
                                ImageEntry::make('logo')
                                    ->label('')
                                    ->circular()
                                    ->size(120)
                                    ->defaultImageUrl('/images/default-club-logo.png')
                                    ->alignCenter(),
                            ])
                            ->columnSpan(1)
                            ->extraAttributes(['class' => 'mb-6 text-center']),
                    ])
                    ->extraAttributes(['class' => 'gap-6']),
                
                Grid::make(2)
                    ->schema([
                        Section::make('Ubicación')
                            ->schema([
                                TextEntry::make('department.name')
                                    ->label('Departamento'),
                                TextEntry::make('city.name')
                                    ->label('Ciudad'),
                                TextEntry::make('address')
                                    ->label('Dirección')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),
                        
                        Section::make('Estadísticas')
                            ->schema([
                                TextEntry::make('players_count')
                                    ->label('Total de Jugadoras')
                                    ->numeric()
                                    ->suffix(' jugadoras'),
                                TextEntry::make('directivos_count')
                                    ->label('Directivos Activos')
                                    ->numeric()
                                    ->suffix(' directivos'),
                                TextEntry::make('torneos_count')
                                    ->label('Torneos Participados')
                                    ->numeric()
                                    ->suffix(' torneos'),
                            ])
                            ->columnSpan(1),
                    ]),
                
                Section::make('Directivos')
                    ->schema([
                        TextEntry::make('directivos')
                            ->label('')
                            ->listWithLineBreaks()
                            ->formatStateUsing(function ($record) {
                                return $record->directivos
                                    ->where('pivot.is_active', true)
                                ->map(function ($directivo) {
                                    $rol = ucfirst($directivo->pivot->role ?? 'Directivo');
                                    $nombre = $directivo->name;
                                    $fechaInicio = $directivo->pivot->start_date ? 
                                        \Carbon\Carbon::parse($directivo->pivot->start_date)->format('d/m/Y') : '';
                                    
                                    return "{$rol}: {$nombre}" . ($fechaInicio ? " (desde {$fechaInicio})" : '');
                                })
                                    ->join("\n");
                            })
                            ->placeholder('No hay directivos activos'),
                    ])
                    ->collapsible(),
                
                Section::make('Observaciones')
                    ->schema([
                        TextEntry::make('federation_notes')
                            ->label('Observaciones de Federación')
                            ->placeholder('Sin observaciones'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => !empty($record->federation_notes)),
            ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            '/admin/clubs' => 'Clubes',
            '' => $this->record->name,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
