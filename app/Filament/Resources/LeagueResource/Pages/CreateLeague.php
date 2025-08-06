<?php

namespace App\Filament\Resources\LeagueResource\Pages;

use App\Filament\Resources\LeagueResource;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use App\Enums\LeagueType;
use App\Enums\PlayerCategory;
use App\Enums\Gender;
use App\Rules\NoAccentsEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Support\Facades\Auth;

class CreateLeague extends CreateRecord
{
    use HasWizard;
    
    protected static string $resource = LeagueResource::class;
    
    protected function getSteps(): array
    {
        return [
            Step::make('Información Básica')
                ->description('Datos generales de la liga')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nombre de la Liga')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }),

                            Forms\Components\TextInput::make('slug')
                                ->label('Slug (URL amigable)')
                                ->required()
                                ->unique('leagues', 'slug')
                                ->maxLength(255),

                            Forms\Components\Textarea::make('description')
                                ->label('Descripción')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])->columns(2),
                ]),

            Step::make('Ubicación')
                ->description('Ubicación geográfica de la liga')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Select::make('country_id')
                                ->label('País')
                                ->options(Country::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set) {
                                    $set('department_id', null);
                                    $set('city_id', null);
                                }),

                            Forms\Components\Select::make('department_id')
                                ->label('Departamento/Estado')
                                ->options(function (Forms\Get $get) {
                                    $countryId = $get('country_id');
                                    if (!$countryId) return [];
                                    return Department::where('country_id', $countryId)->pluck('name', 'id');
                                })
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
                ]),

            Step::make('Configuración')
                ->description('Configuración de la liga')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\Section::make('Tipo y Categorías')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('Tipo de Liga')
                                ->options([
                                    'professional' => 'Profesional',
                                    'amateur' => 'Amateur',
                                    'youth' => 'Juvenil',
                                    'recreational' => 'Recreativa',
                                ])
                                ->required()
                                ->default('amateur'),

                            Forms\Components\CheckboxList::make('categories')
                                ->label('Categorías Permitidas')
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
                                ->default([PlayerCategory::Mayores->value])
                                ->columnSpanFull(),

                            Forms\Components\CheckboxList::make('genders')
                                ->label('Géneros Permitidos')
                                ->options([
                                    Gender::Male->value => Gender::Male->getLabel(),
                                    Gender::Female->value => Gender::Female->getLabel(),
                                    Gender::Mixed->value => Gender::Mixed->getLabel(),
                                ])
                                ->required()
                                ->default([Gender::Female->value])
                                ->columnSpanFull(),
                        ])->columns(2),

                    Forms\Components\Section::make('Configuración Avanzada')
                        ->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->label('Liga Activa')
                                ->default(true),

                            Forms\Components\Toggle::make('allows_external_clubs')
                                ->label('Permite Clubes Externos')
                                ->default(true)
                                ->helperText('Permite que clubes de otras ligas participen'),

                            Forms\Components\TextInput::make('max_clubs')
                                ->label('Máximo de Clubes')
                                ->numeric()
                                ->minValue(1)
                                ->default(50),

                            Forms\Components\TextInput::make('registration_fee')
                                ->label('Costo de Afiliación')
                                ->numeric()
                                ->prefix('$')
                                ->default(0),
                        ])->columns(2),
                ]),

            Step::make('Información de Contacto')
                ->description('Datos de contacto y configuración final')
                ->icon('heroicon-o-phone')
                ->schema([
                    Forms\Components\Section::make('Contacto')
                        ->schema([
                            Forms\Components\TextInput::make('phone')
                                ->label('Teléfono')
                                ->tel()
                                ->maxLength(20),

                            Forms\Components\TextInput::make('email')
                                ->label('Email de Contacto')
                                ->rules([new NoAccentsEmail()])
                                ->maxLength(255),

                            Forms\Components\TextInput::make('website')
                                ->label('Sitio Web')
                                ->url()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('social_media')
                                ->label('Redes Sociales')
                                ->maxLength(255)
                                ->placeholder('@liga_voleibol'),
                        ])->columns(2),

                    Forms\Components\Section::make('Configuración Final')
                        ->schema([
                            Forms\Components\KeyValue::make('settings')
                                ->label('Configuraciones Adicionales')
                                ->keyLabel('Configuración')
                                ->valueLabel('Valor')
                                ->default([
                                    'season_duration' => '12 meses',
                                    'tournament_frequency' => 'Mensual',
                                    'referee_certification' => 'Requerida',
                                ]),

                            Forms\Components\Textarea::make('notes')
                                ->label('Notas Adicionales')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['president_id'] = Auth::id();
        $data['founded_year'] = now()->year;
        $data['status'] = 'active';
        
        // Convert arrays to JSON
        if (isset($data['categories'])) {
            $data['categories'] = json_encode($data['categories']);
        }
        if (isset($data['genders'])) {
            $data['genders'] = json_encode($data['genders']);
        }
        
        return $data;
    }
}
