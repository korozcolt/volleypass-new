<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Enums\Gender;
use App\Enums\TeamType;
use App\Enums\UserStatus;
use App\Filament\Resources\TeamResource;
use App\Filament\Resources\TeamResource\Widgets\DepartmentalSelectionsStatsWidget;
use App\Models\Club;
use App\Models\Department;
use App\Models\League;
use App\Models\Player;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ManageDepartmentalSelections extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TeamResource::class;

    protected static string $view = 'filament.resources.team-resource.pages.manage-departmental-selections';

    protected static ?string $navigationLabel = 'Selecciones Departamentales';

    protected static ?string $title = 'Gestión de Selecciones Departamentales';

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();

        // Solo SuperAdmin y LeagueAdmin pueden acceder
        return $user && $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('create_selection')
                ->label('Crear Selección')
                ->icon('heroicon-o-plus')
                ->form([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombre de la Selección')
                                ->required()
                                ->maxLength(255),

                            Select::make('league_id')
                                ->label('Liga')
                                ->options(League::pluck('name', 'id'))
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn (callable $set) => $set('department_id', null)),

                            Select::make('department_id')
                                ->label('Departamento')
                                ->options(function (callable $get) {
                                    $leagueId = $get('league_id');
                                    if (!$leagueId) {
                                        return [];
                                    }
                                    $league = League::find($leagueId);
                                    if (!$league) {
                                        return [];
                                    }
                                    return Department::where('country_id', $league->country_id)
                                        ->pluck('name', 'id');
                                })
                                ->required()
                                ->reactive(),

                            Select::make('gender')
                                ->label('Género')
                                ->options([
                                    Gender::Female->value => Gender::Female->getLabel(),
                                    Gender::Male->value => Gender::Male->getLabel(),
                                    Gender::Mixed->value => Gender::Mixed->getLabel(),
                                ])
                                ->required(),

                            Select::make('league_category_id')
                                ->label('Categoría')
                                ->options(function (callable $get) {
                                    $leagueId = $get('league_id');
                                    if (!$leagueId) {
                                        return [];
                                    }
                                    
                                    return \App\Models\LeagueCategory::where('league_id', $leagueId)
                                        ->active()
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive(),

                            Select::make('status')
                                ->label('Estado')
                                ->options([
                                    UserStatus::Active->value => UserStatus::Active->getLabel(),
                                    UserStatus::Inactive->value => UserStatus::Inactive->getLabel(),
                                    UserStatus::Suspended->value => UserStatus::Suspended->getLabel(),
                                    UserStatus::Pending->value => UserStatus::Pending->getLabel(),
                                    UserStatus::Blocked->value => UserStatus::Blocked->getLabel(),
                                ])
                                ->default(UserStatus::Active)
                                ->required(),
                        ]),

                    Section::make('Selección de Jugadores')
                        ->schema([
                            Repeater::make('players')
                                ->label('Jugadores Seleccionados')
                                ->schema([
                                    Select::make('player_id')
                                        ->label('Jugador')
                                        ->options(function (callable $get) {
                                            $departmentId = $get('../../department_id');
                                            $gender = $get('../../gender');
                                            $categoryId = $get('../../league_category_id');

                                            if (!$departmentId || !$gender || !$categoryId) {
                                                return [];
                                            }

                                            // Obtener la categoría de liga para determinar el rango de edad
                                            $leagueCategory = \App\Models\LeagueCategory::find($categoryId);
                                            if (!$leagueCategory) {
                                                return [];
                                            }

                                            return Player::whereHas('currentClub', function (Builder $query) use ($departmentId) {
                                                $query->where('department_id', $departmentId);
                                            })
                                            ->whereHas('user', function (Builder $query) use ($gender) {
                                                $query->where('gender', $gender);
                                            })
                                            ->where('status', UserStatus::Active)
                                            ->with(['user', 'currentClub'])
                                            ->get()
                                            ->filter(function ($player) use ($leagueCategory) {
                                                $age = $player->age;
                                                return $age >= $leagueCategory->min_age && $age <= $leagueCategory->max_age;
                                            })
                                            ->mapWithKeys(function ($player) {
                                                return [
                                                    $player->id => $player->user->name . ' (' . $player->currentClub->name . ')'
                                                ];
                                            });
                                        })
                                        ->required()
                                        ->searchable()
                                        ->distinct(),
                                ])
                                ->addActionLabel('Agregar Jugador')
                                ->collapsible()
                                ->itemLabel(function (array $state): ?string {
                                    if (!isset($state['player_id'])) {
                                        return null;
                                    }
                                    $player = Player::find($state['player_id']);
                                    return $player ? $player->user->name . ' (' . $player->currentClub->name . ')' : null;
                                }),
                        ])
                        ->visible(fn (callable $get) => $get('department_id') && $get('gender') && $get('league_category_id')),
                ])
                ->action(function (array $data) {
                    $team = Team::create([
                        'name' => $data['name'],
                        'team_type' => TeamType::SELECTION,
                        'league_id' => $data['league_id'],
                        'department_id' => $data['department_id'],
                        'gender' => $data['gender'],
                        'league_category_id' => $data['league_category_id'],
                        'status' => $data['status'],
                    ]);

                    if (isset($data['players'])) {
                        foreach ($data['players'] as $playerData) {
                            if (isset($playerData['player_id'])) {
                                $team->players()->attach($playerData['player_id']);
                            }
                        }
                    }

                    Notification::make()
                        ->title('Selección creada exitosamente')
                        ->success()
                        ->send();

                    return redirect()->to(static::getUrl());
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Team::query()->where('team_type', TeamType::SELECTION))
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('league.name')
                    ->label('Liga')
                    ->sortable(),

                TextColumn::make('department.name')
                    ->label('Departamento')
                    ->sortable(),

                TextColumn::make('gender')
                    ->label('Género')
                    ->badge()
                    ->formatStateUsing(fn (Gender $state): string => $state->getLabel()),

                TextColumn::make('leagueCategory.name')
                    ->label('Categoría')
                    ->sortable(),

                TextColumn::make('players_count')
                    ->label('Jugadores')
                    ->counts('players')
                    ->badge(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (UserStatus $state): string => $state->getLabel()),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                TableAction::make('manage_players')
                    ->label('Gestionar Jugadores')
                    ->icon('heroicon-o-users')
                    ->form([
                        Section::make('Jugadores Actuales')
                            ->schema([
                                Repeater::make('current_players')
                                    ->label('')
                                    ->schema([
                                        Select::make('player_id')
                                            ->label('Jugador')
                                            ->disabled()
                                            ->options(function ($record) {
                                                return $record->players->mapWithKeys(function ($player) {
                                                    return [
                                                        $player->id => $player->user->name . ' (' . $player->currentClub->name . ')'
                                                    ];
                                                });
                                            }),
                                    ])
                                    ->default(function ($record) {
                                        return $record->players->map(function ($player) {
                                            return ['player_id' => $player->id];
                                        })->toArray();
                                    })
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false),
                            ]),

                        Section::make('Agregar Nuevos Jugadores')
                            ->schema([
                                Repeater::make('new_players')
                                    ->label('')
                                    ->schema([
                                        Select::make('player_id')
                                            ->label('Jugador')
                                            ->options(function ($record) {
                                                // Obtener la categoría de liga para determinar el rango de edad
                                                $leagueCategory = \App\Models\LeagueCategory::find($record->league_category_id);
                                                if (!$leagueCategory) {
                                                    return [];
                                                }

                                                return Player::whereHas('currentClub', function (Builder $query) use ($record) {
                                                    $query->where('department_id', $record->department_id);
                                                })
                                                ->whereHas('user', function (Builder $query) use ($record) {
                                                    $query->where('gender', $record->gender);
                                                })
                                                ->where('status', UserStatus::Active)
                                                ->whereNotIn('id', $record->players->pluck('id'))
                                                ->with(['user', 'currentClub'])
                                                ->get()
                                                ->filter(function ($player) use ($leagueCategory) {
                                                    $age = $player->age;
                                                    return $age >= $leagueCategory->min_age && $age <= $leagueCategory->max_age;
                                                })
                                                ->mapWithKeys(function ($player) {
                                                    return [
                                                        $player->id => $player->user->name . ' (' . $player->currentClub->name . ')'
                                                    ];
                                                });
                                            })
                                            ->required()
                                            ->searchable()
                                            ->distinct(),
                                    ])
                                    ->addActionLabel('Agregar Jugador')
                                    ->collapsible(),
                            ]),
                    ])
                    ->action(function ($record, array $data) {
                        if (isset($data['new_players'])) {
                            foreach ($data['new_players'] as $playerData) {
                                if (isset($playerData['player_id'])) {
                                    $record->players()->attach($playerData['player_id']);
                                }
                            }
                        }

                        Notification::make()
                            ->title('Jugadores actualizados exitosamente')
                            ->success()
                            ->send();
                    }),

                TableAction::make('remove_players')
                    ->label('Remover Jugadores')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->form([
                        Select::make('players_to_remove')
                            ->label('Jugadores a Remover')
                            ->multiple()
                            ->options(function ($record) {
                                return $record->players->mapWithKeys(function ($player) {
                                    return [
                                        $player->id => $player->user->name . ' (' . $player->currentClub->name . ')'
                                    ];
                                });
                            })
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        if (isset($data['players_to_remove'])) {
                            $record->players()->detach($data['players_to_remove']);
                        }

                        Notification::make()
                            ->title('Jugadores removidos exitosamente')
                            ->success()
                            ->send();
                    }),

                TableAction::make('manage_selections')
                    ->label('Gestionar Selecciones')
                    ->icon('heroicon-o-flag')
                    ->color('primary')
                    ->url(fn ($record) => TeamResource::getUrl('player-selections', ['record' => $record->id]))
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DepartmentalSelectionsStatsWidget::class,
        ];
    }
}
