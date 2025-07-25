<?php

namespace App\Filament\Resources\TournamentResource\Pages;

use App\Filament\Resources\TournamentResource;
use App\Models\League;
use App\Enums\PlayerCategory;
use App\Enums\TournamentType;
use App\Enums\Gender;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CreateTournament extends CreateRecord
{
    use HasWizard;
    
    protected static string $resource = TournamentResource::class;
    
    protected function getSteps(): array
    {
        return [
            Step::make('Información Básica')
                ->description('Configuración general del torneo')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nombre del Torneo')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }),

                            Forms\Components\TextInput::make('slug')
                                ->label('Slug (URL amigable)')
                                ->required()
                                ->unique('tournaments', 'slug')
                                ->maxLength(255),

                            Forms\Components\Select::make('league_id')
                                ->label('Liga')
                                ->options(League::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                    // Reset category when league changes
                                    $set('category', null);
                                }),

                            Forms\Components\Textarea::make('description')
                                ->label('Descripción')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])->columns(2),
                ]),

            Step::make('Categoría y Tipo')
                ->description('Selecciona la categoría y tipo de torneo')
                ->icon('heroicon-o-squares-2x2')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('Tipo de Torneo')
                                ->options([
                                    TournamentType::League->value => TournamentType::League->getLabel(),
                                    TournamentType::Cup->value => TournamentType::Cup->getLabel(),
                                    TournamentType::Mixed->value => TournamentType::Mixed->getLabel(),
                                    TournamentType::Flash->value => TournamentType::Flash->getLabel(),
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                    $this->applyTournamentTypeDefaults($set, $state);
                                }),

                            Forms\Components\Select::make('category')
                                ->label('Categoría')
                                ->options([
                                    PlayerCategory::Mini->value => PlayerCategory::Mini->getLabel(),
                                    PlayerCategory::Pre_Mini->value => PlayerCategory::Pre_Mini->getLabel(),
                                    PlayerCategory::Infantil->value => PlayerCategory::Infantil->getLabel(),
                                    PlayerCategory::Cadete->value => PlayerCategory::Cadete->getLabel(),
                                    PlayerCategory::Juvenil->value => PlayerCategory::Juvenil->getLabel(),
                                    PlayerCategory::Mayores->value => PlayerCategory::Mayores->getLabel(),
                                    PlayerCategory::Masters->value => PlayerCategory::Masters->getLabel(),
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                    $this->applyCategoryDefaults($set, $state);
                                }),

                            Forms\Components\Select::make('gender')
                                ->label('Género')
                                ->options([
                                    Gender::Male->value => Gender::Male->getLabel(),
                                    Gender::Female->value => Gender::Female->getLabel(),
                                    Gender::Mixed->value => Gender::Mixed->getLabel(),
                                ])
                                ->required()
                                ->default(Gender::Mixed->value),

                            Forms\Components\Placeholder::make('category_info')
                                ->label('Información de la Categoría')
                                ->content(function (Forms\Get $get) {
                                    $category = $get('category');
                                    if (!$category) {
                                        return 'Selecciona una categoría para ver la información';
                                    }
                                    
                                    $categoryEnum = PlayerCategory::from($category);
                                    $ageRange = $categoryEnum->getDefaultAgeRange();
                                    
                                    return "Rango de edad: {$ageRange[0]}-{$ageRange[1]} años";
                                })
                                ->columnSpanFull(),
                        ])->columns(2),
                ]),

            Step::make('Fechas y Configuración')
                ->description('Configura las fechas y parámetros del torneo')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Forms\Components\Section::make('Fechas del Torneo')
                        ->schema([
                            Forms\Components\DatePicker::make('registration_start')
                                ->label('Inicio de Inscripciones')
                                ->required()
                                ->default(now())
                                ->live(),

                            Forms\Components\DatePicker::make('registration_end')
                                ->label('Fin de Inscripciones')
                                ->required()
                                ->after('registration_start')
                                ->live(),

                            Forms\Components\DatePicker::make('start_date')
                                ->label('Fecha de Inicio del Torneo')
                                ->required()
                                ->after('registration_end'),

                            Forms\Components\DatePicker::make('end_date')
                                ->label('Fecha de Fin del Torneo')
                                ->required()
                                ->after('start_date'),
                        ])->columns(2),

                    Forms\Components\Section::make('Configuración de Participación')
                        ->schema([
                            Forms\Components\TextInput::make('max_teams')
                                ->label('Máximo de Equipos')
                                ->numeric()
                                ->minValue(2)
                                ->maxValue(64)
                                ->default(16),

                            Forms\Components\TextInput::make('min_teams')
                                ->label('Mínimo de Equipos')
                                ->numeric()
                                ->minValue(2)
                                ->default(4),

                            Forms\Components\TextInput::make('registration_fee')
                                ->label('Costo de Inscripción')
                                ->numeric()
                                ->prefix('$')
                                ->default(0),

                            Forms\Components\Select::make('currency')
                                ->label('Moneda')
                                ->options([
                                    'COP' => 'Pesos Colombianos (COP)',
                                    'USD' => 'Dólares (USD)',
                                    'EUR' => 'Euros (EUR)',
                                ])
                                ->default('COP'),
                        ])->columns(2),
                ]),

            Step::make('Reglas y Configuración Avanzada')
                ->description('Configura las reglas específicas del torneo')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\Section::make('Reglas del Juego')
                        ->schema([
                            Forms\Components\KeyValue::make('rules')
                                ->label('Reglas del Juego')
                                ->keyLabel('Regla')
                                ->valueLabel('Valor')
                                ->default(function (Forms\Get $get) {
                                    return $this->getDefaultGameRules($get('category'), $get('type'));
                                }),
                        ]),

                    Forms\Components\Section::make('Configuración de Grupos')
                        ->schema([
                            Forms\Components\KeyValue::make('settings')
                                ->label('Configuración de Grupos')
                                ->keyLabel('Configuración')
                                ->valueLabel('Valor')
                                ->default(function (Forms\Get $get) {
                                    return $this->getDefaultGroupConfig($get('type'));
                                }),
                        ]),

                    Forms\Components\Section::make('Premios')
                        ->schema([
                            Forms\Components\KeyValue::make('prizes')
                                ->label('Configuración de Premios')
                                ->keyLabel('Posición')
                                ->valueLabel('Premio')
                                ->default(function (Forms\Get $get) {
                                    return $this->getDefaultPrizes($get('category'));
                                }),
                        ]),

                    Forms\Components\Section::make('Información Adicional')
                        ->schema([
                            Forms\Components\TextInput::make('venue')
                                ->label('Sede/Lugar')
                                ->maxLength(255),

                            Forms\Components\Textarea::make('venue_address')
                                ->label('Dirección de la Sede')
                                ->rows(2),

                            Forms\Components\Textarea::make('notes')
                                ->label('Notas Adicionales')
                                ->rows(3),

                            Forms\Components\Toggle::make('is_public')
                                ->label('Torneo Público')
                                ->default(true)
                                ->helperText('Los torneos públicos son visibles para todos los usuarios'),

                            Forms\Components\Toggle::make('requires_approval')
                                ->label('Requiere Aprobación')
                                ->default(false)
                                ->helperText('Las inscripciones requieren aprobación manual'),
                        ])->columns(2),
                ]),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values
        $data['status'] = \App\Enums\TournamentStatus::Draft->value;
        $data['season'] = now()->year;
        $data['organizer_id'] = Auth::id();
        $data['total_teams'] = 0;
        $data['total_matches'] = 0;
        $data['format'] = 'round_robin';
        
        return $data;
    }

    private function applyTournamentTypeDefaults(Forms\Set $set, ?string $tournamentType): void
    {
        if (!$tournamentType) return;

        $type = TournamentType::from($tournamentType);
        
        switch ($type) {
            case TournamentType::League:
                $set('max_teams', 16);
                $set('min_teams', 6);
                break;
            case TournamentType::Cup:
                $set('max_teams', 32);
                $set('min_teams', 8);
                break;
            case TournamentType::Mixed:
                $set('max_teams', 24);
                $set('min_teams', 8);
                break;
            case TournamentType::Flash:
                $set('max_teams', 8);
                $set('min_teams', 4);
                break;
        }
    }

    private function applyCategoryDefaults(Forms\Set $set, ?string $category): void
    {
        if (!$category) return;

        $categoryEnum = PlayerCategory::from($category);
        
        // Apply category-specific defaults
        switch ($categoryEnum) {
            case PlayerCategory::Mini:
            case PlayerCategory::Pre_Mini:
                $set('registration_fee', 25000);
                break;
            case PlayerCategory::Infantil:
            case PlayerCategory::Cadete:
                $set('registration_fee', 50000);
                break;
            case PlayerCategory::Juvenil:
                $set('registration_fee', 75000);
                break;
            case PlayerCategory::Mayores:
                $set('registration_fee', 100000);
                break;
            case PlayerCategory::Masters:
                $set('registration_fee', 80000);
                break;
        }
    }

    private function getDefaultGameRules(?string $category, ?string $tournamentType): array
    {
        $baseRules = [
            'sets_to_win' => '2',
            'points_per_set' => '25',
            'tiebreak_points' => '15',
            'timeout_per_set' => '2',
            'substitutions_limit' => '6',
        ];

        if ($category) {
            $categoryEnum = PlayerCategory::from($category);
            
            switch ($categoryEnum) {
                case PlayerCategory::Mini:
                case PlayerCategory::Pre_Mini:
                    $baseRules['points_per_set'] = '21';
                    $baseRules['tiebreak_points'] = '15';
                    $baseRules['substitutions_limit'] = '12';
                    break;
                case PlayerCategory::Infantil:
                    $baseRules['points_per_set'] = '23';
                    break;
            }
        }

        if ($tournamentType === TournamentType::Flash->value) {
            $baseRules['sets_to_win'] = '1';
            $baseRules['points_per_set'] = '21';
        }

        return $baseRules;
    }

    private function getDefaultGroupConfig(?string $tournamentType): array
    {
        $config = [
            'auto_distribution' => 'true',
            'balance_groups' => 'true',
            'seeding_method' => 'random',
        ];

        if ($tournamentType) {
            $type = TournamentType::from($tournamentType);
            
            switch ($type) {
                case TournamentType::League:
                    $config['teams_per_group'] = '8';
                    $config['round_robin'] = 'true';
                    break;
                case TournamentType::Cup:
                    $config['teams_per_group'] = '1';
                    $config['elimination'] = 'true';
                    break;
                case TournamentType::Mixed:
                    $config['teams_per_group'] = '4';
                    $config['group_stage'] = 'true';
                    $config['playoff_stage'] = 'true';
                    break;
                case TournamentType::Flash:
                    $config['teams_per_group'] = '4';
                    $config['quick_matches'] = 'true';
                    break;
            }
        }

        return $config;
    }

    private function getDefaultPrizes(?string $category): array
    {
        $basePrizes = [
            '1er Lugar' => 'Trofeo + Medallas',
            '2do Lugar' => 'Trofeo + Medallas',
            '3er Lugar' => 'Trofeo + Medallas',
        ];

        if ($category) {
            $categoryEnum = PlayerCategory::from($category);
            
            switch ($categoryEnum) {
                case PlayerCategory::Mini:
                case PlayerCategory::Pre_Mini:
                    $basePrizes['Participación'] = 'Medalla de Participación';
                    break;
                case PlayerCategory::Mayores:
                    $basePrizes['MVP'] = 'Trofeo Mejor Jugador';
                    $basePrizes['Goleador'] = 'Trofeo Mejor Atacante';
                    break;
            }
        }

        return $basePrizes;
    }
}
