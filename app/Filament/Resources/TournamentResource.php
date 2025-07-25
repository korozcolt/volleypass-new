<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TournamentResource\Pages;
use App\Models\Tournament;
use App\Models\League;
use App\Enums\UserStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class TournamentResource extends Resource
{
    protected static ?string $model = Tournament::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Torneos';
    protected static ?string $modelLabel = 'Torneo';
    protected static ?string $pluralModelLabel = 'Torneos';
    protected static ?string $navigationGroup = 'Administración de Ligas';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('league_id')
                            ->label('Liga')
                            ->options(\App\Models\League::pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(\App\Enums\TournamentStatus::class)
                            ->default(\App\Enums\TournamentStatus::Draft)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Fechas y Configuración')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Fecha de Inicio')
                            ->required(),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Fecha de Fin')
                            ->required()
                            ->after('start_date'),

                        Forms\Components\DateTimePicker::make('registration_start')
                            ->label('Inicio de Inscripciones'),

                        Forms\Components\DateTimePicker::make('registration_end')
                            ->label('Fin de Inscripciones'),

                        Forms\Components\TextInput::make('max_teams')
                            ->label('Máximo de Equipos')
                            ->numeric()
                            ->minValue(2),

                        Forms\Components\TextInput::make('registration_fee')
                            ->label('Costo de Inscripción')
                            ->numeric()
                            ->prefix('$'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración Avanzada')
                    ->schema([
                        Forms\Components\KeyValue::make('rules')
                            ->label('Reglas del Torneo')
                            ->keyLabel('Regla')
                            ->valueLabel('Descripción'),

                        Forms\Components\KeyValue::make('prizes')
                            ->label('Premios')
                            ->keyLabel('Posición')
                            ->valueLabel('Premio'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('league_id')
                    ->label('Liga')
                    ->formatStateUsing(fn ($state) => \App\Models\League::find($state)?->name ?? 'Sin Liga')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => $state instanceof \App\Enums\TournamentStatus ? $state->getColor() : 'gray'),

                Tables\Columns\TextColumn::make('teams_count')
                    ->label('Equipos')
                    ->state(fn ($record) => 0) // Placeholder hasta implementar relación
                    ->badge(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inicio')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('registration_fee')
                    ->label('Inscripción')
                    ->money('COP'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(\App\Enums\TournamentStatus::class),

                Tables\Filters\SelectFilter::make('league_id')
                    ->label('Liga')
                    ->options(\App\Models\League::pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
                Infolists\Components\Section::make('Información General')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre'),

                        Infolists\Components\TextEntry::make('league_id')
                            ->label('Liga')
                            ->formatStateUsing(fn ($state) => \App\Models\League::find($state)?->name ?? 'Sin Liga'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),
                    ])->columns(2),

                Infolists\Components\Section::make('Fechas')
                    ->schema([
                        Infolists\Components\TextEntry::make('start_date')
                            ->label('Fecha de Inicio')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('end_date')
                            ->label('Fecha de Fin')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('registration_start')
                            ->label('Inicio de Inscripciones')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('registration_end')
                            ->label('Fin de Inscripciones')
                            ->dateTime(),
                    ])->columns(2),

                Infolists\Components\Section::make('Configuración')
                    ->schema([
                        Infolists\Components\TextEntry::make('max_teams')
                            ->label('Máximo de Equipos'),

                        Infolists\Components\TextEntry::make('registration_fee')
                            ->label('Costo de Inscripción')
                            ->money('COP'),

                        Infolists\Components\TextEntry::make('teams_count')
                            ->label('Equipos Inscritos')
                            ->state(fn ($record) => 0), // Placeholder hasta implementar relación
                    ])->columns(3),
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
            'index' => Pages\ListTournaments::route('/'),
            'create' => Pages\CreateTournament::route('/create'),
            'view' => Pages\ViewTournament::route('/{record}'),
            'edit' => Pages\EditTournament::route('/{record}/edit'),
        ];
    }
}
