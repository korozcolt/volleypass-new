<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use App\Models\Club;
use App\Models\League;
use App\Models\Department;
use App\Models\City;
use App\Models\User;
use App\Enums\FederationStatus;
use App\Enums\UserStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CreateClub extends CreateRecord
{
    use HasWizard;
    
    protected static string $resource = ClubResource::class;

    public function getTitle(): string
    {
        return 'Crear Nuevo Club';
    }

    public function getSubheading(): ?string
    {
        return 'Registra un nuevo club en el sistema paso a paso';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSteps(): array
    {
        return [
            Step::make('Información Básica')
                ->description('Datos generales del club')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nombre del Club')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }),

                            Forms\Components\TextInput::make('slug')
                                ->label('Slug (URL amigable)')
                                ->required()
                                ->unique('clubs', 'slug')
                                ->maxLength(255),

                            Forms\Components\Select::make('league_id')
                                ->label('Liga')
                                ->options(League::pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            Forms\Components\Textarea::make('description')
                                ->label('Descripción')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])->columns(2),
                ]),

            Step::make('Ubicación')
                ->description('Ubicación y datos de contacto')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Forms\Components\Section::make('Ubicación')
                        ->schema([
                            Forms\Components\Select::make('department_id')
                                ->label('Departamento')
                                ->options(Department::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set) {
                                    $set('city_id', null);
                                }),

                            Forms\Components\Select::make('city_id')
                                ->label('Ciudad')
                                ->options(function (Forms\Get $get) {
                                    $departmentId = $get('department_id');
                                    if (!$departmentId) return [];
                                    return City::where('department_id', $departmentId)->pluck('name', 'id');
                                })
                                ->searchable()
                                ->required(),

                            Forms\Components\TextInput::make('address')
                                ->label('Dirección')
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ])->columns(2),

                    Forms\Components\Section::make('Contacto')
                        ->schema([
                            Forms\Components\TextInput::make('phone')
                                ->label('Teléfono')
                                ->tel()
                                ->maxLength(20),

                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('website')
                                ->label('Sitio Web')
                                ->url()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('social_media')
                                ->label('Redes Sociales')
                                ->maxLength(255)
                                ->placeholder('@club_voleibol'),
                        ])->columns(2),
                ]),

            Step::make('Federación')
                ->description('Configuración de federación')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Toggle::make('is_federated')
                                ->label('Club Federado')
                                ->default(false)
                                ->live()
                                ->helperText('Los clubes federados pueden participar en competencias oficiales'),

                            Forms\Components\TextInput::make('federation_code')
                                ->label('Código de Federación')
                                ->maxLength(20)
                                ->visible(fn (Forms\Get $get) => $get('is_federated'))
                                ->helperText('Se generará automáticamente si se deja vacío'),

                            Forms\Components\Select::make('federation_status')
                                 ->label('Estado de Federación')
                                 ->options([
                                     FederationStatus::PendingPayment->value => FederationStatus::PendingPayment->getLabel(),
                                     FederationStatus::Federated->value => FederationStatus::Federated->getLabel(),
                                     FederationStatus::Suspended->value => FederationStatus::Suspended->getLabel(),
                                     FederationStatus::NotFederated->value => FederationStatus::NotFederated->getLabel(),
                                 ])
                                 ->default(FederationStatus::PendingPayment->value)
                                 ->visible(fn (Forms\Get $get) => $get('is_federated')),

                             Forms\Components\DatePicker::make('federation_date')
                                ->label('Fecha de Federación')
                                ->visible(fn (Forms\Get $get) => $get('is_federated')),
                        ])->columns(2),
                ]),

            Step::make('Directivos')
                ->description('Configuración de directivos del club')
                ->icon('heroicon-o-users')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Repeater::make('directors')
                                ->label('Directivos del Club')
                                ->relationship('directors')
                                ->schema([
                                    Forms\Components\Select::make('user_id')
                                        ->label('Usuario')
                                        ->options(User::where('status', UserStatus::Active->value)->pluck('name', 'id'))
                                        ->searchable()
                                        ->required(),

                                    Forms\Components\Select::make('rol')
                                        ->label('Cargo')
                                        ->options([
                                            'presidente' => 'Presidente',
                                            'vicepresidente' => 'Vicepresidente',
                                            'secretario' => 'Secretario',
                                            'tesorero' => 'Tesorero',
                                            'vocal' => 'Vocal',
                                            'director_tecnico' => 'Director Técnico',
                                        ])
                                        ->required(),

                                    Forms\Components\Toggle::make('activo')
                                        ->label('Activo')
                                        ->default(true),

                                    Forms\Components\DatePicker::make('fecha_inicio')
                                        ->label('Fecha de Inicio')
                                        ->default(now()),

                                    Forms\Components\DatePicker::make('fecha_fin')
                                        ->label('Fecha de Fin'),

                                    Forms\Components\Textarea::make('observaciones')
                                        ->label('Observaciones')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->itemLabel(fn (array $state): ?string => 
                                    isset($state['user_id']) && isset($state['rol']) 
                                        ? User::find($state['user_id'])?->name . ' - ' . $state['rol']
                                        : 'Nuevo Directivo'
                                )
                                ->collapsible()
                                ->addActionLabel('Agregar Directivo')
                                ->minItems(1),
                        ]),
                ]),
        ];
    }

    protected function afterCreate(): void
    {
        $club = $this->record;
        
        // Log de la creación
        Log::info('Nuevo club creado', [
            'club_id' => $club->id,
            'name' => $club->name,
            'created_by' => Auth::id(),
        ]);
        
        // Notificación de éxito
        Notification::make()
            ->title('Club creado exitosamente')
            ->body("El club '{$club->name}' ha sido registrado correctamente.")
            ->success()
            ->send();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Club registrado exitosamente';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asegurar que el código de federación sea único si es federado
        if (isset($data['is_federated']) && $data['is_federated'] && empty($data['federation_code'])) {
            $data['federation_code'] = $this->generateFederationCode($data['name']);
        }
        
        // Establecer valores por defecto
        if (Auth::check()) {
             $data['created_by'] = Auth::id();
         }
        
        return $data;
    }

    private function generateFederationCode(string $name): string
    {
        $prefix = strtoupper(substr($name, 0, 3));
        $suffix = str_pad(Club::where('is_federated', true)->count() + 1, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $suffix;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $club = static::getModel()::create($data);
        
        // Crear directivo inicial si se proporcionó
        if (!empty($data['directors'])) {
            foreach ($data['directors'] as $director) {
                $club->directors()->attach($director['user_id'], [
                    'role' => $director['role'],
                    'is_active' => $director['is_active'] ?? true,
                    'start_date' => $director['start_date'] ?? now(),
                    'end_date' => $director['end_date'] ?? null,
                ]);
            }
        }
        
        return $club;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Crear Club'),
            $this->getCreateAnotherFormAction()
                ->label('Crear y Agregar Otro'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}
