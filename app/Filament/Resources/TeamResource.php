<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use App\Models\Club;
use App\Models\User;
use App\Enums\UserStatus;
use App\Enums\PlayerCategory;
use App\Enums\Gender;
use App\Enums\TeamType;
use App\Models\League;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Equipos';
    protected static ?string $modelLabel = 'Equipo';
    protected static ?string $pluralModelLabel = 'Equipos';
    protected static ?string $navigationGroup = 'Gestión Deportiva';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Select::make('team_type')
                            ->label('Tipo de Equipo')
                            ->options(TeamType::options())
                            ->default(TeamType::CLUB)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if ($state === TeamType::SELECTION->value) {
                                    $set('club_id', null);
                                } else {
                                    $set('league_id', null);
                                    $set('department_id', null);
                                }
                            }),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('club_id')
                            ->label('Club')
                            ->relationship('club', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Forms\Get $get) => $get('team_type') === TeamType::CLUB->value)
                            ->visible(fn (Forms\Get $get) => $get('team_type') === TeamType::CLUB->value),

                        Forms\Components\Select::make('league_id')
                            ->label('Liga')
                            ->relationship('league', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Forms\Get $get) => $get('team_type') === TeamType::SELECTION->value)
                            ->visible(fn (Forms\Get $get) => $get('team_type') === TeamType::SELECTION->value)
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('department_id', null)),

                        Forms\Components\Select::make('department_id')
                            ->label('Departamento')
                            ->options(function (Forms\Get $get) {
                                $leagueId = $get('league_id');
                                if (!$leagueId) return [];
                                
                                $league = League::find($leagueId);
                                if (!$league) return [];
                                
                                return Department::where('country_id', $league->country_id)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(fn (Forms\Get $get) => $get('team_type') === TeamType::SELECTION->value)
                            ->visible(fn (Forms\Get $get) => $get('team_type') === TeamType::SELECTION->value),

                        Forms\Components\Select::make('league_category_id')
                            ->label('Categoría')
                            ->relationship('leagueCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('gender')
                            ->label('Género')
                            ->options(Gender::class)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración del Equipo')
                    ->schema([
                        Forms\Components\Select::make('coach_id')
                            ->label('Entrenador Principal')
                            ->options(\App\Models\Coach::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable(),

                        Forms\Components\Select::make('assistant_coach_id')
                            ->label('Entrenador Asistente')
                            ->options(\App\Models\Coach::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable(),

                        Forms\Components\Select::make('captain_id')
                            ->label('Capitana')
                            ->options(\App\Models\Player::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable(),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(UserStatus::class)
                            ->default(UserStatus::Active)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles Adicionales')
                    ->schema([
                        Forms\Components\TextInput::make('colors')
                            ->label('Colores del Uniforme')
                            ->maxLength(100),

                        Forms\Components\DatePicker::make('founded_date')
                            ->label('Fecha de Fundación'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración Avanzada')
                    ->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('Configuraciones')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (TeamType $state): string => match ($state) {
                        TeamType::CLUB => 'primary',
                        TeamType::SELECTION => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (TeamType $state): string => $state->label()),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('club.name')
                    ->label('Club')
                    ->sortable()
                    ->placeholder('N/A')
                    ->visible(fn ($record) => $record?->team_type === TeamType::CLUB),

                Tables\Columns\TextColumn::make('league.name')
                    ->label('Liga')
                    ->sortable()
                    ->visible(fn ($record) => $record?->team_type === TeamType::SELECTION),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departamento')
                    ->sortable()
                    ->visible(fn ($record) => $record?->team_type === TeamType::SELECTION),

                Tables\Columns\TextColumn::make('leagueCategory.name')
                    ->label('Categoría')
                    ->badge()
                    ->placeholder('Sin categoría'),

                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría (Legacy)')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('gender')
                    ->label('Género')
                    ->badge(),

                Tables\Columns\TextColumn::make('coach_id')
                    ->label('Entrenador')
                    ->formatStateUsing(fn ($state) => $state ? \App\Models\Coach::find($state)?->user?->name ?? 'Sin Entrenador' : 'Sin Entrenador')
                    ->sortable(),

                Tables\Columns\TextColumn::make('captain_id')
                    ->label('Capitana')
                    ->formatStateUsing(fn ($state) => $state ? \App\Models\Player::find($state)?->user?->name ?? 'Sin Capitana' : 'Sin Capitana')
                    ->sortable(),

                Tables\Columns\TextColumn::make('players_count')
                    ->label('Jugadores')
                    ->counts('players')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('founded_date')
                    ->label('Fundado')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('team_type')
                    ->label('Tipo de Equipo')
                    ->options(TeamType::options()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(UserStatus::class),

                Tables\Filters\SelectFilter::make('league_category_id')
                    ->label('Categoría')
                    ->relationship('leagueCategory', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('gender')
                    ->label('Género')
                    ->options(Gender::class),

                Tables\Filters\SelectFilter::make('club')
                    ->label('Club')
                    ->relationship('club', 'name')
                    ->visible(fn () => request()->get('tableFilters.team_type.value') !== TeamType::SELECTION->value),

                Tables\Filters\SelectFilter::make('league')
                    ->label('Liga')
                    ->relationship('league', 'name')
                    ->visible(fn () => request()->get('tableFilters.team_type.value') === TeamType::SELECTION->value),

                Tables\Filters\SelectFilter::make('department')
                    ->label('Departamento')
                    ->relationship('department', 'name')
                    ->visible(fn () => request()->get('tableFilters.team_type.value') === TeamType::SELECTION->value),
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

                        Infolists\Components\TextEntry::make('club.name')
                            ->label('Club'),

                        Infolists\Components\TextEntry::make('leagueCategory.name')
                            ->label('Categoría')
                            ->badge()
                            ->placeholder('Sin categoría'),

                        Infolists\Components\TextEntry::make('gender')
                            ->label('Género')
                            ->badge(),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),

                        Infolists\Components\TextEntry::make('colors')
                            ->label('Colores del Uniforme'),

                        Infolists\Components\TextEntry::make('founded_date')
                            ->label('Fecha de Fundación')
                            ->date(),
                    ])->columns(2),

                Infolists\Components\Section::make('Cuerpo Técnico')
                    ->schema([
                        Infolists\Components\TextEntry::make('coach_id')
                            ->label('Entrenador Principal')
                            ->formatStateUsing(fn ($state) => $state ? \App\Models\Coach::find($state)?->user?->name ?? 'Sin Entrenador' : 'Sin Entrenador'),

                        Infolists\Components\TextEntry::make('assistant_coach_id')
                            ->label('Entrenador Asistente')
                            ->formatStateUsing(fn ($state) => $state ? \App\Models\Coach::find($state)?->user?->name ?? 'Sin Entrenador' : 'Sin Entrenador'),

                        Infolists\Components\TextEntry::make('captain_id')
                            ->label('Capitana')
                            ->formatStateUsing(fn ($state) => $state ? \App\Models\Player::find($state)?->user?->name ?? 'Sin Capitana' : 'Sin Capitana'),
                    ])->columns(3),

                Infolists\Components\Section::make('Estadísticas')
                    ->schema([
                        Infolists\Components\TextEntry::make('players_count')
                            ->label('Total de Jugadores')
                            ->state(fn ($record) => $record->players()->count()),

                        Infolists\Components\TextEntry::make('matches_played')
                            ->label('Partidos Jugados')
                            ->state(fn ($record) => 0), // Placeholder hasta implementar relación

                        Infolists\Components\TextEntry::make('tournaments_count')
                            ->label('Torneos Participados')
                            ->state(fn ($record) => 0), // Placeholder hasta implementar relación
                    ])->columns(3),

                Infolists\Components\Section::make('Descripción')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),

                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notas'),
                    ]),
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'manage-departmental-selections' => Pages\ManageDepartmentalSelections::route('/departmental-selections'),
            'player-selections' => Pages\PlayerSelections::route('/{record}/player-selections'),
            'view' => Pages\ViewTeam::route('/{record}'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            \Filament\Navigation\NavigationItem::make(static::getNavigationLabel())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->group(static::getNavigationGroup())
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->url(static::getUrl()),
            \Filament\Navigation\NavigationItem::make('Selecciones Departamentales')
                ->icon('heroicon-o-flag')
                ->group(static::getNavigationGroup())
                ->sort(static::getNavigationSort() + 1)
                ->url(static::getUrl('manage-departmental-selections')),
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
            'SuperAdmin', 'LeagueAdmin', 'ClubDirector', 'Coach'
        ]);
    }

    public static function canCreate(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        return $user->hasAnyRole([
            'SuperAdmin', 'LeagueAdmin', 'ClubDirector'
        ]);
    }

    public static function canEdit($record): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        return match($user->getRoleNames()->first()) {
            'SuperAdmin' => true,
            'LeagueAdmin' => $record->club?->league_id === $user->league_id,
            'ClubDirector' => $record->club_id === $user->club_id,
            default => false
        };
    }

    public static function canDelete($record): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin', 'ClubDirector']);
    }
}
