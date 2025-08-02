<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatchResource\Pages;
use App\Models\VolleyMatch;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\Referee;
use App\Enums\MatchStatus;
use App\Enums\MatchPhase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MatchResource extends Resource
{
    protected static ?string $model = VolleyMatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Partidos';

    protected static ?string $modelLabel = 'Partido';

    protected static ?string $pluralModelLabel = 'Partidos';

    protected static ?string $navigationGroup = 'Competiciones';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Partido')
                    ->schema([
                        Forms\Components\Select::make('tournament_id')
                            ->label('Torneo')
                            ->relationship('tournament', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('home_team_id')
                            ->label('Equipo Local')
                            ->relationship('homeTeam', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('away_team_id')
                            ->label('Equipo Visitante')
                            ->relationship('awayTeam', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('first_referee')
                            ->label('Árbitro Principal')
                            ->relationship('referee', 'license_number')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('match_number')
                            ->label('Número de Partido')
                            ->numeric()
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(MatchStatus::class)
                            ->required()
                            ->default(MatchStatus::Scheduled),

                        Forms\Components\Select::make('phase')
                            ->label('Fase')
                            ->options(MatchPhase::class)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Programación y Ubicación')
                    ->schema([
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Fecha y Hora Programada')
                            ->required(),

                        Forms\Components\TextInput::make('venue')
                            ->label('Sede')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('venue_address')
                            ->label('Dirección de la Sede')
                            ->maxLength(500)
                            ->rows(2),

                        Forms\Components\TextInput::make('round')
                            ->label('Ronda')
                            ->maxLength(50),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Resultados')
                    ->schema([
                        Forms\Components\TextInput::make('home_sets')
                            ->label('Sets Equipo Local')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(5),

                        Forms\Components\TextInput::make('away_sets')
                            ->label('Sets Equipo Visitante')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(5),

                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('Hora de Inicio'),

                        Forms\Components\DateTimePicker::make('finished_at')
                            ->label('Hora de Finalización'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Notas')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas del Partido')
                            ->maxLength(1000)
                            ->rows(3),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('match_number')
                    ->label('N°')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tournament.name')
                    ->label('Torneo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('homeTeam.name')
                    ->label('Equipo Local')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('awayTeam.name')
                    ->label('Equipo Visitante')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('result')
                    ->label('Resultado')
                    ->getStateUsing(fn (VolleyMatch $record): string => 
                        "{$record->home_sets} - {$record->away_sets}"
                    ),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => MatchStatus::Scheduled,
                        'warning' => MatchStatus::In_Progress,
                        'success' => MatchStatus::Finished,
                        'danger' => MatchStatus::Cancelled,
                        'gray' => MatchStatus::Postponed,
                    ]),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Fecha Programada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('referee.license_number')
                    ->label('Árbitro')
                    ->sortable(),

                Tables\Columns\TextColumn::make('venue')
                    ->label('Sede')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(MatchStatus::class),

                Tables\Filters\SelectFilter::make('tournament')
                    ->label('Torneo')
                    ->relationship('tournament', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('phase')
                    ->label('Fase')
                    ->options(MatchPhase::class),

                Tables\Filters\Filter::make('scheduled_date')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scheduled_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información del Partido')
                    ->schema([
                        Infolists\Components\TextEntry::make('match_number')
                            ->label('Número de Partido'),
                        Infolists\Components\TextEntry::make('tournament.name')
                            ->label('Torneo'),
                        Infolists\Components\TextEntry::make('phase')
                            ->label('Fase'),
                        Infolists\Components\TextEntry::make('round')
                            ->label('Ronda'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Equipos')
                    ->schema([
                        Infolists\Components\TextEntry::make('homeTeam.name')
                            ->label('Equipo Local'),
                        Infolists\Components\TextEntry::make('awayTeam.name')
                            ->label('Equipo Visitante'),
                        Infolists\Components\TextEntry::make('home_sets')
                            ->label('Sets Equipo Local'),
                        Infolists\Components\TextEntry::make('away_sets')
                            ->label('Sets Equipo Visitante'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Programación')
                    ->schema([
                        Infolists\Components\TextEntry::make('scheduled_at')
                            ->label('Fecha Programada')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('started_at')
                            ->label('Hora de Inicio')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('finished_at')
                            ->label('Hora de Finalización')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('venue')
                            ->label('Sede'),
                        Infolists\Components\TextEntry::make('venue_address')
                            ->label('Dirección')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Arbitraje')
                    ->schema([
                        Infolists\Components\TextEntry::make('referee.license_number')
                            ->label('Árbitro Principal'),
                        Infolists\Components\TextEntry::make('referee.user.name')
                            ->label('Nombre del Árbitro'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Notas')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notas del Partido')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
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
            'index' => Pages\ListMatches::route('/'),
            'create' => Pages\CreateMatch::route('/create'),
            'view' => Pages\ViewMatch::route('/{record}'),
            'edit' => Pages\EditMatch::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['Super Admin', 'Admin', 'League Admin']);
    }
}