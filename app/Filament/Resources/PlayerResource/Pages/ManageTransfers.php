<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use App\Models\PlayerTransfer;
use App\Models\Club;
use App\Models\League;
use App\Enums\TransferStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ManageTransfers extends ManageRelatedRecords
{
    protected static string $resource = PlayerResource::class;

    protected static string $relationship = 'transfers';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    public static function getNavigationLabel(): string
    {
        return 'Traspasos';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Traspaso')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('from_club_id')
                                    ->label('Club Origen')
                                    ->relationship('fromClub', 'name')
                                    ->default(fn() => $this->getOwnerRecord()->current_club_id)
                                    ->disabled()
                                    ->required(),

                                Forms\Components\Select::make('to_club_id')
                                    ->label('Club Destino')
                                    ->options(Club::where('id', '!=', $this->getOwnerRecord()->current_club_id)->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('league_id')
                                    ->label('Liga')
                                    ->relationship('league', 'name')
                                    ->default(fn() => $this->getOwnerRecord()->currentClub?->league_id)
                                    ->required(),

                                Forms\Components\DatePicker::make('transfer_date')
                                    ->label('Fecha de Traspaso')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\TextInput::make('transfer_fee')
                                    ->label('Costo del Traspaso')
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('0.00'),

                                Forms\Components\Select::make('currency')
                                    ->label('Moneda')
                                    ->options([
                                        'COP' => 'Pesos Colombianos (COP)',
                                        'USD' => 'Dólares (USD)',
                                        'EUR' => 'Euros (EUR)',
                                    ])
                                    ->default('COP'),
                            ]),

                        Forms\Components\Textarea::make('reason')
                            ->label('Motivo del Traspaso')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Explique el motivo del traspaso...'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas Adicionales')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Información adicional sobre el traspaso...'),
                    ]),

                Forms\Components\Section::make('Estado del Traspaso')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(TransferStatus::class)
                            ->default(TransferStatus::Pending)
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Placeholder::make('requested_by_info')
                            ->label('Solicitado por')
                            ->content(fn() => Auth::user()->name)
                            ->visible(fn($operation) => $operation === 'create'),
                    ])
                    ->visible(fn($operation) => $operation === 'edit'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fromClub.name')
                    ->label('Club Origen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('toClub.name')
                    ->label('Club Destino')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('Fecha de Traspaso')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn($record) => $record->status->getColor())
                    ->icon(fn($record) => $record->status->getIcon())
                    ->sortable(),

                Tables\Columns\TextColumn::make('transfer_fee')
                    ->label('Costo')
                    ->money('COP')
                    ->placeholder('Gratuito')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('requestedBy.name')
                    ->label('Solicitado por')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Aprobado por')
                    ->searchable()
                    ->placeholder('Pendiente')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Fecha de Aprobación')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Pendiente')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(TransferStatus::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('from_club_id')
                    ->label('Club Origen')
                    ->relationship('fromClub', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('to_club_id')
                    ->label('Club Destino')
                    ->relationship('toClub', 'name')
                    ->searchable(),

                Tables\Filters\Filter::make('transfer_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transfer_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transfer_date', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Solicitar Traspaso')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['player_id'] = $this->getOwnerRecord()->id;
                        $data['requested_by'] = Auth::id();
                        return $data;
                    })
                    ->after(function (PlayerTransfer $record) {
                        // Enviar notificación de nueva solicitud
                        Notification::make()
                            ->title('Solicitud de traspaso creada')
                            ->body("Se ha creado una solicitud de traspaso para {$record->player->user->name}")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (PlayerTransfer $record): string => "/admin/transfers/{$record->id}"),

                Tables\Actions\EditAction::make()
                    ->visible(fn (PlayerTransfer $record): bool => $record->status === TransferStatus::Pending),

                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar Traspaso')
                    ->modalDescription('¿Está seguro de que desea aprobar este traspaso?')
                    ->action(function (PlayerTransfer $record) {
                        $record->approve(Auth::user());
                        
                        Notification::make()
                            ->title('Traspaso aprobado')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (PlayerTransfer $record): bool => 
                        $record->canBeApproved() && Auth::user()->can('approve_transfers')
                    ),

                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Motivo del Rechazo')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (PlayerTransfer $record, array $data) {
                        $record->reject(Auth::user(), $data['rejection_reason']);
                        
                        Notification::make()
                            ->title('Traspaso rechazado')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (PlayerTransfer $record): bool => 
                        $record->canBeApproved() && Auth::user()->can('approve_transfers')
                    ),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (PlayerTransfer $record): bool => $record->status === TransferStatus::Pending),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Aprobar Seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $approved = 0;
                            foreach ($records as $record) {
                                if ($record->canBeApproved()) {
                                    $record->approve(Auth::user());
                                    $approved++;
                                }
                            }
                            
                            Notification::make()
                                ->title("{$approved} traspasos aprobados")
                                ->success()
                                ->send();
                        })
                        ->visible(fn () => Auth::user()->can('approve_transfers')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}