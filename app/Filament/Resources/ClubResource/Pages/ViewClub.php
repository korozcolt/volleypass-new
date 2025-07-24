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
        return $this->record->nombre;
    }

    public function getSubheading(): ?string
    {
        return 'Información detallada del club';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Grid::make(2)
                        ->schema([
                            Section::make('Información General')
                                ->schema([
                                    TextEntry::make('nombre')
                                        ->label('Nombre del Club')
                                        ->weight(FontWeight::Bold)
                                        ->size('lg'),
                                    TextEntry::make('nombre_corto')
                                        ->label('Nombre Corto'),
                                    TextEntry::make('email')
                                        ->label('Email')
                                        ->copyable(),
                                    TextEntry::make('telefono')
                                        ->label('Teléfono')
                                        ->copyable(),
                                    TextEntry::make('fundacion')
                                        ->label('Fecha de Fundación')
                                        ->date(),
                                    TextEntry::make('created_at')
                                        ->label('Fecha de Registro')
                                        ->dateTime(),
                                ])
                                ->columnSpan(1),
                            
                            Section::make('Estado y Federación')
                                ->schema([
                                    TextEntry::make('es_federado')
                                        ->label('Estado de Federación')
                                        ->formatStateUsing(fn (bool $state): string => $state ? 'Federado' : 'No Federado')
                                        ->badge()
                                        ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                                    TextEntry::make('tipo_federacion')
                                        ->label('Tipo de Federación')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'nacional' => 'success',
                                            'departamental' => 'warning',
                                            'municipal' => 'info',
                                            default => 'gray',
                                        })
                                        ->visible(fn ($record) => $record->es_federado),
                                    TextEntry::make('codigo_federacion')
                                        ->label('Código de Federación')
                                        ->copyable()
                                        ->visible(fn ($record) => $record->es_federado),
                                    TextEntry::make('vencimiento_federacion')
                                        ->label('Vencimiento Federación')
                                        ->date()
                                        ->visible(fn ($record) => $record->es_federado),
                                ])
                                ->columnSpan(1),
                        ]),
                    
                    ImageEntry::make('logo')
                        ->label('Logo del Club')
                        ->circular()
                        ->size(120)
                        ->defaultImageUrl('/images/default-club-logo.png'),
                ])
                ->from('lg'),
                
                Grid::make(2)
                    ->schema([
                        Section::make('Ubicación')
                            ->schema([
                                TextEntry::make('departamento.name')
                                    ->label('Departamento'),
                                TextEntry::make('ciudad.name')
                                    ->label('Ciudad'),
                                TextEntry::make('direccion')
                                    ->label('Dirección')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),
                        
                        Section::make('Estadísticas')
                            ->schema([
                                TextEntry::make('jugadoras_count')
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
                                    ->where('pivot.activo', true)
                                    ->map(function ($directivo) {
                                        $rol = ucfirst($directivo->pivot->rol ?? 'Directivo');
                                        $nombre = $directivo->name;
                                        $fechaInicio = $directivo->pivot->fecha_inicio ? 
                                            \Carbon\Carbon::parse($directivo->pivot->fecha_inicio)->format('d/m/Y') : '';
                                        
                                        return "{$rol}: {$nombre}" . ($fechaInicio ? " (desde {$fechaInicio})" : '');
                                    })
                                    ->join("\n");
                            })
                            ->placeholder('No hay directivos activos'),
                    ])
                    ->collapsible(),
                
                Section::make('Observaciones')
                    ->schema([
                        TextEntry::make('observaciones_federacion')
                            ->label('Observaciones de Federación')
                            ->placeholder('Sin observaciones'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => !empty($record->observaciones_federacion)),
            ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            '/admin/clubs' => 'Clubes',
            '' => $this->record->nombre,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
