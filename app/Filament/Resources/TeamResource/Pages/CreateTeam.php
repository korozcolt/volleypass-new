<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms;
use Filament\Forms\Form;
use App\Models\Club;
use App\Models\League;
use App\Models\User;
use App\Enums\PlayerCategory;

use App\Enums\UserStatus;
use Illuminate\Support\Facades\Auth;

class CreateTeam extends CreateRecord
{
    use HasWizard;
    
    protected static string $resource = TeamResource::class;
    
    public function getTitle(): string
    {
        return 'Crear Nuevo Equipo';
    }

    public function getSubheading(): ?string
    {
        return 'Registra un nuevo equipo paso a paso';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSteps(): array
    {
        return [
            Step::make('Información Básica')
                ->description('Datos generales del equipo')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nombre del Equipo')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }),

                            Forms\Components\TextInput::make('slug')
                                ->label('Slug (URL amigable)')
                                ->required()
                                ->unique('teams', 'slug')
                                ->maxLength(255),

                            Forms\Components\Select::make('club_id')
                                ->label('Club')
                                ->options(Club::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                    if ($state) {
                                        $club = Club::find($state);
                                        if ($club) {
                                            $set('league_id', $club->league_id);
                                        }
                                    }
                                }),

                            Forms\Components\Select::make('league_id')
                                ->label('Liga')
                                ->options(League::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->disabled(fn (Forms\Get $get) => !empty($get('club_id'))),

                            Forms\Components\Textarea::make('description')
                                ->label('Descripción')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])->columns(2),
                ]),

            Step::make('Categoría y Configuración')
                ->description('Categoría y configuración del equipo')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\Section::make('Categoría')
                        ->schema([
                            Forms\Components\Select::make('category')
                                ->label('Categoría')
                                ->options(PlayerCategory::class)
                                ->required()
                                ->live(),

                            Forms\Components\Select::make('gender')
                                ->label('Género')
                                ->options([
                                    'female' => 'Femenino',
                                    'male' => 'Masculino',
                                    'mixed' => 'Mixto',
                                ])
                                ->required()
                                ->default('female'),

                            Forms\Components\TextInput::make('min_age')
                                ->label('Edad Mínima')
                                ->numeric()
                                ->minValue(10)
                                ->maxValue(50),

                            Forms\Components\TextInput::make('max_age')
                                ->label('Edad Máxima')
                                ->numeric()
                                ->minValue(10)
                                ->maxValue(50),
                        ])->columns(2),

                    Forms\Components\Section::make('Configuración')
                        ->schema([
                            Forms\Components\Select::make('status')
                                 ->label('Estado')
                                 ->options(UserStatus::class)
                                 ->required()
                                 ->default(UserStatus::Active),

                            Forms\Components\TextInput::make('max_players')
                                ->label('Máximo de Jugadoras')
                                ->numeric()
                                ->minValue(6)
                                ->maxValue(25)
                                ->default(15),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Equipo Activo')
                                ->default(true),

                            Forms\Components\Toggle::make('accepts_new_players')
                                ->label('Acepta Nuevas Jugadoras')
                                ->default(true),
                        ])->columns(2),
                ]),

            Step::make('Cuerpo Técnico')
                ->description('Entrenadores y cuerpo técnico')
                ->icon('heroicon-o-users')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Select::make('head_coach_id')
                                ->label('Entrenador Principal')
                                ->options(User::where('status', UserStatus::Active->value)->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            Forms\Components\Select::make('assistant_coach_id')
                                ->label('Entrenador Asistente')
                                ->options(User::where('status', UserStatus::Active->value)->pluck('name', 'id'))
                                ->searchable(),

                            Forms\Components\Repeater::make('technical_staff')
                                ->label('Cuerpo Técnico Adicional')
                                ->schema([
                                    Forms\Components\Select::make('user_id')
                                        ->label('Persona')
                                        ->options(User::where('status', UserStatus::Active->value)->pluck('name', 'id'))
                                        ->searchable()
                                        ->required(),

                                    Forms\Components\Select::make('role')
                                        ->label('Rol')
                                        ->options([
                                            'preparador_fisico' => 'Preparador Físico',
                                            'medico' => 'Médico',
                                            'fisioterapeuta' => 'Fisioterapeuta',
                                            'manager' => 'Manager',
                                            'estadistico' => 'Estadístico',
                                            'otro' => 'Otro',
                                        ])
                                        ->required(),

                                    Forms\Components\TextInput::make('notes')
                                        ->label('Notas')
                                        ->maxLength(255),
                                ])
                                ->columns(3)
                                ->itemLabel(fn (array $state): ?string => 
                                    isset($state['user_id']) && isset($state['role']) 
                                        ? User::find($state['user_id'])?->name . ' - ' . $state['role']
                                        : 'Nuevo Miembro'
                                )
                                ->collapsible()
                                ->addActionLabel('Agregar Miembro')
                                ->columnSpanFull(),
                        ])->columns(2),
                ]),

            Step::make('Información Adicional')
                ->description('Detalles adicionales del equipo')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\Section::make('Colores y Uniformes')
                        ->schema([
                            Forms\Components\ColorPicker::make('primary_color')
                                ->label('Color Primario')
                                ->default('#1f2937'),

                            Forms\Components\ColorPicker::make('secondary_color')
                                ->label('Color Secundario')
                                ->default('#ffffff'),

                            Forms\Components\TextInput::make('uniform_number_prefix')
                                ->label('Prefijo de Números')
                                ->maxLength(5)
                                ->placeholder('Ej: T1, A, etc.'),
                        ])->columns(3),

                    Forms\Components\Section::make('Notas y Observaciones')
                        ->schema([
                            Forms\Components\Textarea::make('notes')
                                ->label('Notas Internas')
                                ->rows(3)
                                ->helperText('Información adicional sobre el equipo'),

                            Forms\Components\Textarea::make('goals')
                                ->label('Objetivos de la Temporada')
                                ->rows(3)
                                ->helperText('Metas y objetivos del equipo'),
                        ]),
                ]),
        ];
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asignar el usuario actual como creador
        $data['created_by'] = Auth::id();
        
        // Convertir arrays a JSON si es necesario
        if (isset($data['technical_staff'])) {
            $data['technical_staff'] = json_encode($data['technical_staff']);
        }
        
        return $data;
    }
}
