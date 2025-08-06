<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use App\Enums\FederationStatus;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms;
use Filament\Forms\Form;
use App\Models\Club;
use App\Models\Team;
use App\Models\User;
use App\Models\Department;
use App\Models\City;
use App\Enums\PlayerCategory;
use App\Enums\PlayerPosition;
use App\Enums\MedicalStatus;
use App\Enums\UserStatus;
use App\Enums\Gender;
use App\Rules\NoAccentsEmail;
use Illuminate\Support\Facades\Auth;

class CreatePlayer extends CreateRecord
{
    use HasWizard;
    
    protected static string $resource = PlayerResource::class;
    
    public function getTitle(): string
    {
        return 'Registrar Nueva Jugadora';
    }

    public function getSubheading(): ?string
    {
        return 'Registra una nueva jugadora paso a paso';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSteps(): array
    {
        return [
            Step::make('Información Personal')
                ->description('Datos personales de la jugadora')
                ->icon('heroicon-o-user')
                ->schema([
                    Forms\Components\Section::make('Datos Básicos')
                        ->schema([
                            Forms\Components\TextInput::make('user.first_name')
                                ->label('Nombres')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('user.last_name')
                                ->label('Apellidos')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\Select::make('user.document_type')
                                ->label('Tipo de Documento')
                                ->options([
                                    'CC' => 'Cédula de Ciudadanía',
                                    'TI' => 'Tarjeta de Identidad',
                                    'CE' => 'Cédula de Extranjería',
                                    'PP' => 'Pasaporte',
                                ])
                                ->required()
                                ->default('CC'),

                            Forms\Components\TextInput::make('user.document_number')
                                ->label('Número de Documento')
                                ->required()
                                ->unique('users', 'document_number')
                                ->maxLength(20),

                            Forms\Components\DatePicker::make('user.birth_date')
                                ->label('Fecha de Nacimiento')
                                ->required()
                                ->maxDate(now()->subYears(10))
                                ->minDate(now()->subYears(50)),

                            Forms\Components\Select::make('user.gender')
                                ->label('Género')
                                ->options(Gender::class)
                                ->required()
                                ->default(Gender::Female),
                        ])->columns(2),
                ]),

            Step::make('Ubicación y Contacto')
                ->description('Información de ubicación y contacto')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Forms\Components\Section::make('Ubicación')
                        ->schema([
                            Forms\Components\Select::make('user.department_id')
                                ->label('Departamento')
                                ->options(Department::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set) {
                                    $set('user.city_id', null);
                                }),

                            Forms\Components\Select::make('user.city_id')
                                ->label('Ciudad')
                                ->options(function (Forms\Get $get) {
                                    $departmentId = $get('user.department_id');
                                    if (!$departmentId) return [];
                                    return City::where('department_id', $departmentId)->pluck('name', 'id');
                                })
                                ->searchable()
                                ->required(),

                            Forms\Components\TextInput::make('user.address')
                                ->label('Dirección')
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ])->columns(2),

                    Forms\Components\Section::make('Contacto')
                        ->schema([
                            Forms\Components\TextInput::make('user.phone')
                                ->label('Teléfono')
                                ->tel()
                                ->maxLength(20),

                            Forms\Components\TextInput::make('user.email')
                                ->label('Email')
                                ->rules([new NoAccentsEmail()])
                                ->unique('users', 'email')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('emergency_contact_name')
                                ->label('Contacto de Emergencia')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('emergency_contact_phone')
                                ->label('Teléfono de Emergencia')
                                ->tel()
                                ->maxLength(20),
                        ])->columns(2),
                ]),

            Step::make('Información Deportiva')
                ->description('Datos deportivos y de juego')
                ->icon('heroicon-o-trophy')
                ->schema([
                    Forms\Components\Section::make('Club y Equipo')
                        ->schema([
                            Forms\Components\Select::make('current_club_id')
                                ->label('Club')
                                ->options(Club::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                    $set('current_team_id', null);
                                }),

                            Forms\Components\Select::make('current_team_id')
                                ->label('Equipo')
                                ->options(function (Forms\Get $get) {
                                    $clubId = $get('current_club_id');
                                    if (!$clubId) return [];
                                    return Team::where('club_id', $clubId)->pluck('name', 'id');
                                })
                                ->searchable(),

                            Forms\Components\TextInput::make('jersey_number')
                                ->label('Número de Camiseta')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(99),
                        ])->columns(3),

                    Forms\Components\Section::make('Posición y Categoría')
                        ->schema([
                            Forms\Components\Select::make('position')
                                ->label('Posición Principal')
                                ->options(PlayerPosition::class)
                                ->required(),

                            Forms\Components\Select::make('secondary_position')
                                ->label('Posición Secundaria')
                                ->options(PlayerPosition::class),

                            Forms\Components\Select::make('category')
                                ->label('Categoría')
                                ->options(PlayerCategory::class)
                                ->required(),

                            Forms\Components\Select::make('dominant_hand')
                                ->label('Mano Dominante')
                                ->options([
                                    'right' => 'Derecha',
                                    'left' => 'Izquierda',
                                    'both' => 'Ambidiestra',
                                ])
                                ->default('right'),
                        ])->columns(2),

                    Forms\Components\Section::make('Medidas Físicas')
                        ->schema([
                            Forms\Components\TextInput::make('height')
                                ->label('Estatura (cm)')
                                ->numeric()
                                ->minValue(140)
                                ->maxValue(220)
                                ->suffix('cm'),

                            Forms\Components\TextInput::make('weight')
                                ->label('Peso (kg)')
                                ->numeric()
                                ->minValue(40)
                                ->maxValue(120)
                                ->suffix('kg'),

                            Forms\Components\TextInput::make('reach')
                                ->label('Alcance (cm)')
                                ->numeric()
                                ->minValue(180)
                                ->maxValue(280)
                                ->suffix('cm'),

                            Forms\Components\TextInput::make('spike_reach')
                                ->label('Alcance de Remate (cm)')
                                ->numeric()
                                ->minValue(200)
                                ->maxValue(350)
                                ->suffix('cm'),
                        ])->columns(2),
                ]),

            Step::make('Estado Médico')
                ->description('Información médica y de salud')
                ->icon('heroicon-o-heart')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Select::make('medical_status')
                                ->label('Estado Médico')
                                ->options(MedicalStatus::class)
                                ->required()
                                ->default(MedicalStatus::Fit)
                                ->live(),

                            Forms\Components\DatePicker::make('medical_certificate_expiry')
                                ->label('Vencimiento Certificado Médico')
                                ->minDate(now())
                                ->required(fn (Forms\Get $get) => $get('medical_status') === MedicalStatus::Fit->value),

                            Forms\Components\Textarea::make('medical_notes')
                                ->label('Notas Médicas')
                                ->rows(3)
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('allergies')
                                ->label('Alergias')
                                ->rows(2)
                                ->columnSpanFull(),

                            Forms\Components\Toggle::make('is_eligible')
                                ->label('Elegible para Jugar')
                                ->default(true)
                                ->helperText('Determina si la jugadora puede participar en partidos'),
                        ])->columns(2),
                ]),

            Step::make('Configuración Final')
                ->description('Configuración de cuenta y estado')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\Section::make('Estado de la Jugadora')
                        ->schema([
                            Forms\Components\Select::make('user.status')
                                ->label('Estado de Usuario')
                                ->options(UserStatus::class)
                                ->required()
                                ->default(UserStatus::Active),

                            Forms\Components\Select::make('federation_status')
                                ->label('Estado de Federación')
                                ->options(FederationStatus::class)
                                ->required()
                                ->default(FederationStatus::NotFederated)
                                ->helperText('Se actualiza automáticamente según el proceso de federación'),

                            Forms\Components\Toggle::make('can_play')
                                ->label('Puede Jugar')
                                ->default(true)
                                ->helperText('Determina si la jugadora está habilitada para jugar'),
                        ])->columns(3),

                    Forms\Components\Section::make('Notas Adicionales')
                        ->schema([
                            Forms\Components\Textarea::make('notes')
                                ->label('Notas Internas')
                                ->rows(3)
                                ->helperText('Información adicional sobre la jugadora'),

                            Forms\Components\Textarea::make('playing_style')
                                ->label('Estilo de Juego')
                                ->rows(3)
                                ->helperText('Descripción del estilo de juego de la jugadora'),
                        ]),
                ]),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Crear el usuario primero
        $userData = $data['user'] ?? [];
        $userData['password'] = bcrypt('password123'); // Password temporal
        $userData['created_by'] = Auth::id();
        
        $user = User::create($userData);
        
        // Remover datos del usuario del array principal
        unset($data['user']);
        
        // Asignar el ID del usuario creado
        $data['user_id'] = $user->id;
        
        // Establecer valores por defecto para federación
        $data['federation_status'] = $data['federation_status'] ?? FederationStatus::NotFederated;
        
        // Asignar el usuario actual como creador
        $data['created_by'] = Auth::id();
        
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Jugadora registrada exitosamente';
    }
}
