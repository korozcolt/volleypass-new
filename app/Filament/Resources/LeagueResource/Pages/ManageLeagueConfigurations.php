<?php

namespace App\Filament\Resources\LeagueResource\Pages;

use App\Filament\Resources\LeagueResource;
use App\Models\League;
use App\Models\LeagueConfiguration;
use App\Services\LeagueConfigurationService;
use App\Enums\ConfigurationType;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;

class ManageLeagueConfigurations extends Page
{
    protected static string $resource = LeagueResource::class;
    protected static string $view = 'filament.resources.league-resource.pages.manage-league-configurations';

    public League $record;
    public array $data = [];

    public function mount(League $record): void
    {
        $this->record = $record;
        $this->loadConfigurations();
    }

    protected function loadConfigurations(): void
    {
        $configurations = LeagueConfiguration::where('league_id', $this->record->id)
            ->get()
            ->keyBy('key');

        // Define default values for all expected configurations
        $defaultConfigurations = [
            // Transfers
            'transfer_approval_required' => true,
            'transfer_timeout_days' => 7,
            'max_transfers_per_season' => 2,
            'transfer_window_start' => '2024-01-01',
            'transfer_window_end' => '2024-03-31',
            'inter_league_transfers_allowed' => false,
            
            // Documentation
            'document_strictness_level' => 'medium',
            'medical_certificate_required' => true,
            'medical_validity_months' => 6,
            'photo_required' => true,
            'parent_authorization_under_18' => true,
            'insurance_required' => false,
            
            // Categories
            'age_verification_strict' => true,
            'category_mixing_allowed' => false,
            'guest_players_allowed' => true,
            'max_guest_players_per_match' => 2,
            
            // Discipline
            'yellow_card_accumulation_limit' => 3,
            'suspension_games_per_red_card' => 1,
            'appeal_process_enabled' => true,
            'appeal_deadline_days' => 3,
            
            // Federation
            'federation_required_for_tournaments' => true,
            'federation_grace_period_days' => 30,
            'federation_validity_months' => 12,
            'manual_approval_process' => true,
            
            // Tournament Interface
            'live_match_updates' => true,
            'real_time_scoring' => true,
            'player_eligibility_check' => true,
            'referee_interface_enabled' => true,
            
            // Public Data
            'show_live_scores' => true,
            'show_team_rosters' => false,
            'show_player_stats' => true,
            'show_tournament_brackets' => true,
            'public_api_enabled' => true,
        ];

        $this->data = [];
        foreach ($defaultConfigurations as $key => $defaultValue) {
            if ($configurations->has($key)) {
                $this->data[$key] = $configurations[$key]->typed_value;
            } else {
                $this->data[$key] = $defaultValue;
            }
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Configuraciones')
                    ->tabs([
                        // Tab de Traspasos
                        Forms\Components\Tabs\Tab::make('Traspasos')
                            ->icon('heroicon-o-arrow-path')
                            ->schema([
                                Forms\Components\Section::make('Reglas de Traspasos')
                                    ->schema([
                                        Forms\Components\Toggle::make('transfer_approval_required')
                                            ->label('Requiere Aprobación de Liga')
                                            ->helperText('Si está activado, todos los traspasos deben ser aprobados por la liga'),

                                        Forms\Components\TextInput::make('transfer_timeout_days')
                                            ->label('Días para Aprobar/Rechazar')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(30)
                                            ->suffix('días'),

                                        Forms\Components\TextInput::make('max_transfers_per_season')
                                            ->label('Máximo Traspasos por Temporada')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(10)
                                            ->suffix('traspasos'),

                                        Forms\Components\DatePicker::make('transfer_window_start')
                                            ->label('Inicio Ventana de Traspasos'),

                                        Forms\Components\DatePicker::make('transfer_window_end')
                                            ->label('Fin Ventana de Traspasos'),

                                        Forms\Components\Toggle::make('inter_league_transfers_allowed')
                                            ->label('Permitir Traspasos Entre Ligas')
                                            ->helperText('Permite traspasos hacia/desde otras ligas'),
                                    ])->columns(2),
                            ]),

                        // Tab de Documentación
                        Forms\Components\Tabs\Tab::make('Documentación')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Requisitos Documentales')
                                    ->schema([
                                        Forms\Components\Select::make('document_strictness_level')
                                            ->label('Nivel de Exigencia')
                                            ->options([
                                                'low' => 'Bajo - Documentos básicos',
                                                'medium' => 'Medio - Documentos estándar',
                                                'high' => 'Alto - Documentos completos',
                                            ])
                                            ->required(),

                                        Forms\Components\Toggle::make('medical_certificate_required')
                                            ->label('Certificado Médico Obligatorio'),

                                        Forms\Components\TextInput::make('medical_validity_months')
                                            ->label('Validez Certificado Médico')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(24)
                                            ->suffix('meses'),

                                        Forms\Components\Toggle::make('photo_required')
                                            ->label('Fotografía Obligatoria'),

                                        Forms\Components\Toggle::make('parent_authorization_under_18')
                                            ->label('Autorización Padres (Menores 18)'),

                                        Forms\Components\Toggle::make('insurance_required')
                                            ->label('Seguro Deportivo Obligatorio'),
                                    ])->columns(2),
                            ]),

                        // Tab de Categorías
                        Forms\Components\Tabs\Tab::make('Categorías')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Forms\Components\Section::make('Reglas de Categorías')
                                    ->schema([
                                        Forms\Components\Toggle::make('age_verification_strict')
                                            ->label('Verificación Estricta de Edad')
                                            ->helperText('Requiere documentos oficiales para verificar edad'),

                                        Forms\Components\Toggle::make('category_mixing_allowed')
                                            ->label('Permitir Jugar en Categoría Superior')
                                            ->helperText('Jugadoras pueden participar en categorías mayores'),

                                        Forms\Components\Toggle::make('guest_players_allowed')
                                            ->label('Permitir Jugadoras Invitadas')
                                            ->helperText('Permite jugadoras de otros clubes como invitadas'),

                                        Forms\Components\TextInput::make('max_guest_players_per_match')
                                            ->label('Máximo Jugadoras Invitadas por Partido')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(6)
                                            ->suffix('jugadoras'),
                                    ])->columns(2),
                            ]),

                        // Tab de Disciplina
                        Forms\Components\Tabs\Tab::make('Disciplina')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->schema([
                                Forms\Components\Section::make('Reglas Disciplinarias')
                                    ->schema([
                                        Forms\Components\TextInput::make('yellow_card_accumulation_limit')
                                            ->label('Límite Tarjetas Amarillas')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(10)
                                            ->suffix('tarjetas'),

                                        Forms\Components\TextInput::make('suspension_games_per_red_card')
                                            ->label('Partidos Suspensión por Roja')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(5)
                                            ->suffix('partidos'),

                                        Forms\Components\Toggle::make('appeal_process_enabled')
                                            ->label('Proceso de Apelación Habilitado'),

                                        Forms\Components\TextInput::make('appeal_deadline_days')
                                            ->label('Días Límite para Apelar')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(15)
                                            ->suffix('días'),
                                    ])->columns(2),
                            ]),

                        // Tab de Federación
                        Forms\Components\Tabs\Tab::make('Federación')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Section::make('Reglas de Federación')
                                    ->schema([
                                        Forms\Components\Toggle::make('federation_required_for_tournaments')
                                            ->label('Federación Requerida para Torneos')
                                            ->helperText('Solo clubes federados pueden participar en torneos oficiales'),

                                        Forms\Components\TextInput::make('federation_grace_period_days')
                                            ->label('Días de Gracia para Federación Vencida')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(90)
                                            ->suffix('días'),

                                        Forms\Components\TextInput::make('federation_validity_months')
                                            ->label('Validez de Federación')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(24)
                                            ->suffix('meses'),

                                        Forms\Components\Toggle::make('manual_approval_process')
                                            ->label('Proceso Manual (Sin Pagos Automáticos)')
                                            ->helperText('La federación se aprueba manualmente sin validación de pagos')
                                            ->disabled()
                                            ->default(true),
                                    ])->columns(2),
                            ]),

                        // Tab de Vista Pública
                        Forms\Components\Tabs\Tab::make('Vista Pública')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Forms\Components\Section::make('Configuraciones Públicas')
                                    ->schema([
                                        Forms\Components\Toggle::make('show_live_scores')
                                            ->label('Mostrar Marcadores en Vivo')
                                            ->helperText('Los marcadores se muestran públicamente en tiempo real'),

                                        Forms\Components\Toggle::make('show_team_rosters')
                                            ->label('Mostrar Nóminas de Equipos')
                                            ->helperText('Las nóminas son visibles públicamente'),

                                        Forms\Components\Toggle::make('show_player_stats')
                                            ->label('Mostrar Estadísticas de Jugadoras')
                                            ->helperText('Estadísticas individuales visibles públicamente'),

                                        Forms\Components\Toggle::make('show_tournament_brackets')
                                            ->label('Mostrar Llaves de Torneos')
                                            ->helperText('Brackets y eliminatorias visibles públicamente'),

                                        Forms\Components\Toggle::make('public_api_enabled')
                                            ->label('API Pública Habilitada')
                                            ->helperText('Permite acceso a datos vía API sin autenticación'),
                                    ])->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Guardar Configuraciones')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('save'),

            Actions\Action::make('reset')
                ->label('Restaurar Valores por Defecto')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->action('resetToDefaults'),

            Actions\Action::make('back')
                ->label('Volver a Liga')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => LeagueResource::getUrl('edit', ['record' => $this->record])),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data) {
            $service = app(LeagueConfigurationService::class);

            foreach ($data as $key => $value) {
                $service->set($this->record->id, $key, $value);
            }
        });

        Notification::make()
            ->title('Configuraciones guardadas exitosamente')
            ->success()
            ->send();

        $this->loadConfigurations();
    }

    public function resetToDefaults(): void
    {
        DB::transaction(function () {
            // Obtener configuraciones por defecto y resetear
            $configurations = LeagueConfiguration::where('league_id', $this->record->id)->get();

            foreach ($configurations as $config) {
                if ($config->default_value !== null) {
                    $config->update(['value' => $config->default_value]);
                }
            }
        });

        Notification::make()
            ->title('Configuraciones restauradas a valores por defecto')
            ->success()
            ->send();

        $this->loadConfigurations();
        $this->form->fill($this->data);
    }

    public function getTitle(): string
    {
        return "Configuraciones de Liga: {$this->record->name}";
    }

    public function getBreadcrumbs(): array
    {
        return [
            LeagueResource::getUrl() => 'Ligas',
            LeagueResource::getUrl('edit', ['record' => $this->record]) => $this->record->name,
            '' => 'Configuraciones',
        ];
    }
}
