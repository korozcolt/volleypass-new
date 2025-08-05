<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerCardResource\Pages;
use App\Models\PlayerCard;
use App\Models\Player;
use App\Enums\CardStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class PlayerCardResource extends Resource
{
    protected static ?string $model = PlayerCard::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Carnets';
    protected static ?string $modelLabel = 'Carnet';
    protected static ?string $pluralModelLabel = 'Carnets';
    protected static ?string $navigationGroup = 'Gestión Médica y Documentos';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Carnet')
                    ->schema([
                        Forms\Components\Select::make('player_id')
                            ->label('Jugadora')
                            ->options(\App\Models\Player::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $player = \App\Models\Player::find($state);
                                    if ($player && $player->currentClub) {
                                        $set('league_id', $player->currentClub->league_id);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('league_id')
                            ->label('Liga')
                            ->options(\App\Models\League::pluck('name', 'id'))
                            ->required(),

                        Forms\Components\TextInput::make('card_number')
                            ->label('Número de Carnet')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('Dejar vacío para generar automáticamente'),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'expired' => 'Expirado',
                                'suspended' => 'Suspendido',
                                'cancelled' => 'Cancelado',
                            ])
                            ->default('active')
                            ->required(),

                        Forms\Components\DatePicker::make('issued_at')
                            ->label('Fecha de Emisión')
                            ->default(now()),

                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->default(now()->addYear())
                            ->after('issued_at'),
                    ])->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3),

                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor'),
                    ]),

                Forms\Components\Section::make('QR y Verificación')
                    ->schema([
                        Forms\Components\TextInput::make('qr_code')
                            ->label('Código QR')
                            ->disabled()
                            ->helperText('Se genera automáticamente al crear el carnet'),

                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificado')
                            ->helperText('Indica si el carnet ha sido verificado por un administrador'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('card_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('player.user.name')
                    ->label('Jugadora')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('player.currentClub.name')
                    ->label('Club')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('issued_date')
                    ->label('Emitido')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at->isPast() ? 'danger' : 'success'),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'expired' => 'Expirado',
                        'suspended' => 'Suspendido',
                        'cancelled' => 'Cancelado',
                    ]),

                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verificado'),

                Tables\Filters\Filter::make('expires_soon')
                    ->label('Expiran Pronto')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<=', now()->addDays(30))),

                Tables\Filters\Filter::make('expired')
                    ->label('Expirados')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_card')
                    ->label('Ver Carnet')
                    ->icon('heroicon-o-identification')
                    ->color('info')
                    ->url(fn ($record) => route('player.card.show', $record->card_number))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('verify')
                    ->label('Verificar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_verified)
                    ->action(function ($record) {
                        $record->update(['is_verified' => true]);
                    }),
                Tables\Actions\Action::make('suspend')
                    ->label('Suspender')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'active')
                    ->action(function ($record) {
                        $record->update(['status' => 'suspended']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información del Carnet')
                    ->schema([
                        Infolists\Components\TextEntry::make('card_number')
                            ->label('Número de Carnet')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('player.user.name')
                            ->label('Jugadora'),

                        Infolists\Components\TextEntry::make('player.currentClub.name')
                            ->label('Club'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),

                        Infolists\Components\IconEntry::make('is_verified')
                            ->label('Verificado')
                            ->boolean(),
                    ])->columns(2),

                Infolists\Components\Section::make('Fechas')
                    ->schema([
                        Infolists\Components\TextEntry::make('issued_date')
                            ->label('Fecha de Emisión')
                            ->date(),

                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->date(),

                        Infolists\Components\TextEntry::make('days_until_expiry')
                            ->label('Días hasta Expiración')
                            ->state(fn ($record) => $record->expires_at->diffInDays(now(), false)),
                    ])->columns(3),

                Infolists\Components\Section::make('Código QR')
                    ->schema([
                        Infolists\Components\TextEntry::make('qr_code')
                            ->label('Código QR')
                            ->copyable(),

                        Infolists\Components\ImageEntry::make('qr_image')
                            ->label('QR Code')
                            ->state(function ($record) {
                                // Aquí podrías generar la imagen del QR
                                return null;
                            }),
                    ])->columns(2),
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
            'index' => Pages\ListPlayerCards::route('/'),
            'create' => Pages\CreatePlayerCard::route('/create'),
            'view' => Pages\ViewPlayerCard::route('/{record}'),
            'edit' => Pages\EditPlayerCard::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Referee no puede acceder al panel admin
        if ($user->hasRole('Referee')) {
            return false;
        }

        return $user->hasAnyRole([
            'SuperAdmin', 'LeagueAdmin', 'ClubDirector'
        ]);
    }

    public static function canCreate(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin', 'ClubDirector']);
    }

    public static function canEdit($record): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        return match($user->getRoleNames()->first()) {
            'SuperAdmin' => true,
            'LeagueAdmin' => $record->player?->currentClub?->league_id === $user->league_id,
            'ClubDirector' => $record->player?->currentClub?->id === $user->club_id,
            default => false
        };
    }

    public static function canDelete($record): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
    }
}
