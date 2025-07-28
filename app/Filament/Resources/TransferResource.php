<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferResource\Pages;
use App\Models\PlayerTransfer;
use App\Models\Player;
use App\Models\Club;
use App\Models\League;
use App\Models\User;
use App\Enums\TransferStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class TransferResource extends Resource
{
    protected static ?string $model = PlayerTransfer::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Traspasos';
    protected static ?string $modelLabel = 'Traspaso';
    protected static ?string $pluralModelLabel = 'Traspasos';
    protected static ?string $navigationGroup = 'Gestión Deportiva';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Traspaso')
                    ->schema([
                        Forms\Components\Select::make('player_id')
                            ->label('Jugadora')
                            ->relationship('player.user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $player = Player::find($state);
                                    if ($player && $player->current_club_id) {
                                        $set('from_club_id', $player->current_club_id);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('from_club_id')
                            ->label('Club Origen')
                            ->relationship('fromClub', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($get) => !$get('player_id')),

                        Forms\Components\Select::make('to_club_id')
                            ->label('Club Destino')
                            ->relationship('toClub', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->different('from_club_id'),

                        Forms\Components\Select::make('league_id')
                            ->label('Liga')
                            ->relationship('league', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles del Traspaso')
                    ->schema([
                        Forms\Components\DatePicker::make('transfer_date')
                            ->label('Fecha de Solicitud')
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('effective_date')
                            ->label('Fecha Efectiva')
                            ->after('transfer_date'),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(TransferStatus::class)
                            ->default(TransferStatus::Pending)
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== TransferStatus::Pending),

                        Forms\Components\TextInput::make('transfer_fee')
                            ->label('Costo del Traspaso')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),

                        Forms\Components\Select::make('currency')
                            ->label('Moneda')
                            ->options([
                                'COP' => 'Pesos Colombianos (COP)',
                                'USD' => 'Dólares (USD)',
                            ])
                            ->default('COP')
                            ->required(),

                        Forms\Components\Textarea::make('reason')
                            ->label('Motivo del Traspaso')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas Adicionales')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Información de Aprobación')
                    ->schema([
                        Forms\Components\Select::make('requested_by')
                            ->label('Solicitado por')
                            ->relationship('requestedBy', 'name')
                            ->default(Auth::id())
                            ->required()
                            ->disabled(),

                        Forms\Components\Select::make('approved_by')
                            ->label('Aprobado por')
                            ->relationship('approvedBy', 'name')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Fecha de Aprobación')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('rejected_at')
                            ->label('Fecha de Rechazo')
                            ->disabled(),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Motivo de Rechazo')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->visible(fn ($record) => $record && ($record->approved_at || $record->rejected_at)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('player.user.name')
                    ->label('Jugadora')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fromClub.name')
                    ->label('Club Origen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('toClub.name')
                    ->label('Club Destino')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('league.name')
                    ->label('Liga')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('Fecha Solicitud')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('effective_date')
                    ->label('Fecha Efectiva')
                    ->date()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->colors([
                        'warning' => TransferStatus::Pending,
                        'info' => TransferStatus::Approved,
                        'danger' => TransferStatus::Rejected,
                        'success' => TransferStatus::Completed,
                        'gray' => TransferStatus::Cancelled,
                    ]),

                Tables\Columns\TextColumn::make('transfer_fee')
                    ->label('Costo')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('requestedBy.name')
                    ->label('Solicitado por')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(TransferStatus::class),

                Tables\Filters\SelectFilter::make('league_id')
                    ->label('Liga')
                    ->relationship('league', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('from_club_id')
                    ->label('Club Origen')
                    ->relationship('fromClub', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('to_club_id')
                    ->label('Club Destino')
                    ->relationship('toClub', 'name')
                    ->searchable()
                    ->preload(),

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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === TransferStatus::Pending),
                
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
                    ->visible(fn ($record) => $record->canBeApproved() && Auth::user()->can('approve_transfers')),

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
                    ->visible(fn ($record) => $record->canBeApproved() && Auth::user()->can('approve_transfers')),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información del Traspaso')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('player.user.name')
                                    ->label('Jugadora'),
                                Infolists\Components\TextEntry::make('player.user.document_number')
                                    ->label('Documento'),
                                Infolists\Components\TextEntry::make('fromClub.name')
                                    ->label('Club Origen'),
                                Infolists\Components\TextEntry::make('toClub.name')
                                    ->label('Club Destino'),
                                Infolists\Components\TextEntry::make('league.name')
                                    ->label('Liga'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Estado')
                                    ->formatStateUsing(fn ($state) => $state->getLabel())
                                    ->badge()
                                    ->color(fn ($state) => $state->getColor()),
                            ]),
                    ]),

                Infolists\Components\Section::make('Detalles')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('transfer_date')
                                    ->label('Fecha de Solicitud')
                                    ->date(),
                                Infolists\Components\TextEntry::make('effective_date')
                                    ->label('Fecha Efectiva')
                                    ->date(),
                                Infolists\Components\TextEntry::make('transfer_fee')
                                    ->label('Costo del Traspaso')
                                    ->money('COP'),
                                Infolists\Components\TextEntry::make('currency')
                                    ->label('Moneda'),
                            ]),
                        Infolists\Components\TextEntry::make('reason')
                            ->label('Motivo del Traspaso')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notas Adicionales')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Información de Gestión')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('requestedBy.name')
                                    ->label('Solicitado por'),
                                Infolists\Components\TextEntry::make('approvedBy.name')
                                    ->label('Aprobado por'),
                                Infolists\Components\TextEntry::make('approved_at')
                                    ->label('Fecha de Aprobación')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('rejected_at')
                                    ->label('Fecha de Rechazo')
                                    ->dateTime(),
                            ]),
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Motivo de Rechazo')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->approved_at || $record->rejected_at),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransfers::route('/'),
            'create' => Pages\CreateTransfer::route('/create'),
            'view' => Pages\ViewTransfer::route('/{record}'),
            'edit' => Pages\EditTransfer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['player.user', 'fromClub', 'toClub', 'league', 'requestedBy', 'approvedBy']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', TransferStatus::Pending)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}