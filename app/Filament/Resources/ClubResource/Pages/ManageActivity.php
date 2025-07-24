<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use App\Models\Club;
use Spatie\Activitylog\Models\Activity;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ManageActivity extends ManageRelatedRecords
{
    protected static string $resource = ClubResource::class;

    protected static string $relationship = 'activities';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Actividad';

    public static function getNavigationLabel(): string
    {
        return 'Actividad';
    }

    public function getTitle(): string
    {
        return 'Log de Actividad - ' . $this->getRecord()->name;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->where('subject_type', Club::class)
                    ->where('subject_id', $this->getRecord()->id)
                    ->orWhere(function (Builder $query) {
                        $query->where('properties->club_id', $this->getRecord()->id)
                              ->orWhere('properties->related_club_id', $this->getRecord()->id);
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Tipo')
                    ->badge()
                    ->color(function (string $state): string {
                        return match ($state) {
                            'club' => 'primary',
                            'player' => 'success',
                            'payment' => 'warning',
                            'document' => 'info',
                            'federation' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Activity $record): ?string {
                        return $record->description;
                    }),

                Tables\Columns\TextColumn::make('event')
                    ->label('Evento')
                    ->badge()
                    ->color(function (string $state): string {
                        return match ($state) {
                            'created' => 'success',
                            'updated' => 'warning',
                            'deleted' => 'danger',
                            'restored' => 'info',
                            default => 'gray',
                        };
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->default('Sistema'),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Modelo')
                    ->formatStateUsing(function (?string $state): string {
                        if (!$state) return 'N/A';
                        return class_basename($state);
                    })
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->description(function (Activity $record): string {
                        return $record->created_at->diffForHumans();
                    }),

                Tables\Columns\IconColumn::make('has_changes')
                    ->label('Cambios')
                    ->getStateUsing(function (Activity $record): bool {
                        return !empty($record->changes) || !empty($record->properties);
                    })
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Tipo de Log')
                    ->options([
                        'club' => 'Club',
                        'player' => 'Jugador',
                        'payment' => 'Pago',
                        'document' => 'Documento',
                        'federation' => 'Federación',
                        'system' => 'Sistema',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('event')
                    ->label('Evento')
                    ->options([
                        'created' => 'Creado',
                        'updated' => 'Actualizado',
                        'deleted' => 'Eliminado',
                        'restored' => 'Restaurado',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('has_user')
                    ->label('Con Usuario')
                    ->query(function (Builder $query): Builder {
                        return $query->whereNotNull('causer_id');
                    }),

                Tables\Filters\Filter::make('system_actions')
                    ->label('Acciones del Sistema')
                    ->query(function (Builder $query): Builder {
                        return $query->whereNull('causer_id');
                    }),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Desde'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                function (Builder $query, $date): Builder {
                                    return $query->whereDate('created_at', '>=', $date);
                                }
                            )
                            ->when(
                                $data['until'],
                                function (Builder $query, $date): Builder {
                                    return $query->whereDate('created_at', '<=', $date);
                                }
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->label('Ver Detalles')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Detalles de la Actividad')
                    ->modalContent(function (Activity $record): \Illuminate\Contracts\View\View {
                        return view('filament.pages.activity-details', [
                            'activity' => $record,
                            'properties' => $record->properties ?? [],
                            'changes' => $record->changes ?? [],
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Tables\Actions\Action::make('view_changes')
                    ->label('Ver Cambios')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->visible(function (Activity $record): bool {
                        return !empty($record->changes);
                    })
                    ->modalHeading('Cambios Realizados')
                    ->modalContent(function (Activity $record): \Illuminate\Contracts\View\View {
                        return view('filament.pages.activity-changes', [
                            'changes' => $record->changes ?? [],
                            'old' => $record->changes['old'] ?? [],
                            'attributes' => $record->changes['attributes'] ?? [],
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export_activities')
                        ->label('Exportar Actividades')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            // Implementar lógica de exportación
                            \Filament\Notifications\Notification::make()
                                ->title('Exportación iniciada')
                                ->body('Las actividades se están preparando para descarga.')
                                ->info()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('generate_report')
                        ->label('Generar Reporte')
                        ->icon('heroicon-o-document-chart-bar')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            // Implementar lógica de generación de reporte
                            \Filament\Notifications\Notification::make()
                                ->title('Reporte generado')
                                ->body('El reporte de actividades ha sido generado.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_all')
                ->label('Exportar Todo')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    $club = $this->getRecord();
                    
                    // Implementar lógica de exportación completa
                    \Filament\Notifications\Notification::make()
                        ->title('Exportación completa iniciada')
                        ->body('Todas las actividades del club se están preparando para descarga.')
                        ->info()
                        ->send();
                }),

            Actions\Action::make('generate_audit_report')
                ->label('Reporte de Auditoría')
                ->icon('heroicon-o-document-chart-bar')
                ->color('success')
                ->action(function () {
                    $club = $this->getRecord();
                    
                    // Implementar lógica de reporte de auditoría
                    \Filament\Notifications\Notification::make()
                        ->title('Reporte de auditoría generado')
                        ->body('El reporte de auditoría ha sido generado exitosamente.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('cleanup_old')
                ->label('Limpiar Antiguos')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Limpiar Registros Antiguos')
                ->modalDescription('¿Está seguro de que desea eliminar los registros de actividad anteriores a 6 meses?')
                ->action(function () {
                    $club = $this->getRecord();
                    $cutoffDate = now()->subMonths(6);
                    
                    $deleted = Activity::query()
                        ->where('subject_type', Club::class)
                        ->where('subject_id', $club->id)
                        ->where('created_at', '<', $cutoffDate)
                        ->delete();
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Limpieza completada')
                        ->body("Se eliminaron {$deleted} registros antiguos.")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100];
    }
}