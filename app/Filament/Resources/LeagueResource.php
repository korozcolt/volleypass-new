<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeagueResource\Pages;
use App\Models\League;
use App\Models\Country;
use App\Models\Player;
use App\Enums\UserStatus;
use App\Enums\PlayerCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class LeagueResource extends Resource
{
    protected static ?string $model = League::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Ligas';
    protected static ?string $modelLabel = 'Liga';
    protected static ?string $pluralModelLabel = 'Ligas';
    protected static ?string $navigationGroup = 'Gestión Deportiva';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Información General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Información Básica')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('short_name')
                                            ->label('Nombre Corto')
                                            ->maxLength(50),

                                        Forms\Components\Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(3),

                                        Forms\Components\Select::make('country_id')
                                            ->label('País')
                                            ->relationship('country', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])->columns(2),

                                Forms\Components\Section::make('Configuración Básica')
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('Estado')
                                            ->options(UserStatus::class)
                                            ->default(UserStatus::Active)
                                            ->required(),

                                        Forms\Components\DatePicker::make('founded_date')
                                            ->label('Fecha de Fundación'),

                                        Forms\Components\TextInput::make('website')
                                            ->label('Sitio Web')
                                            ->url()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('phone')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->maxLength(20),
                                    ])->columns(2),

                                Forms\Components\Section::make('Configuración Avanzada')
                                    ->schema([
                                        Forms\Components\KeyValue::make('settings')
                                            ->label('Configuraciones')
                                            ->keyLabel('Clave')
                                            ->valueLabel('Valor'),

                                        Forms\Components\Textarea::make('notes')
                                            ->label('Notas')
                                            ->rows(3),
                                    ]),

                                Forms\Components\Section::make('Logo de la Liga')
                                    ->description('Sube el logo de la liga')
                                    ->schema([
                                        Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                                            ->label('Logo')
                                            ->helperText('Logo de la liga (JPG, PNG o SVG)')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->collection('logo')
                                            ->conversion('thumb')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/svg+xml']),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Categorías')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                Forms\Components\Section::make('Gestión de Categorías')
                                    ->description('Configura las categorías de edad para esta liga')
                                    ->schema([
                                        Forms\Components\Placeholder::make('categories_info')
                                            ->label('')
                                            ->content('Las categorías se gestionan después de crear la liga. Guarda primero la información básica.')
                                            ->visible(fn($record) => !$record),
                                    ])
                                    ->visible(fn($record) => !$record),

                                Forms\Components\Section::make('Configuración de Categorías')
                                    ->description('Gestiona las categorías de edad específicas de esta liga')
                                    ->icon('heroicon-o-users')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Repeater::make('categories')
                                            ->relationship('categories')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->placeholder('Ej: Mini, Infantil, Juvenil')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, $get, $set, $livewire) {
                                                // Auto-generar código basado en el nombre
                                                if ($state && !$get('code')) {
                                                    $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $state), 0, 4));
                                                    $set('code', $code);
                                                }
                                            })
                                            ->columnSpan(1),

                                                        Forms\Components\TextInput::make('code')
                                                            ->label('Código')
                                                            ->placeholder('Ej: MINI, INF, JUV')
                                                            ->alphaDash()
                                                            ->columnSpan(1),

                                                        Forms\Components\Select::make('gender')
                                                            ->label('Género')
                                                            ->options([
                                                                'mixed' => 'Mixto',
                                                                'male' => 'Masculino',
                                                                'female' => 'Femenino'
                                                            ])
                                                            ->required()
                                                            ->columnSpan(1),
                                                    ]),

                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('min_age')
                                            ->label('Edad Mínima')
                                            ->numeric()
                                            ->minValue(5)
                                            ->maxValue(100)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, $get, $set, $livewire) {
                                                // Validar superposiciones con otras categorías
                                                $categories = $livewire->data['categories'] ?? [];
                                                $currentIndex = array_search($get('../../'), $categories);

                                                foreach ($categories as $index => $category) {
                                                    if ($index !== $currentIndex && isset($category['min_age'], $category['max_age'])) {
                                                        $minAge = (int) $state;
                                                        $maxAge = (int) $get('max_age');
                                                        $otherMin = (int) $category['min_age'];
                                                        $otherMax = (int) $category['max_age'];

                                                        if ($maxAge && (($minAge >= $otherMin && $minAge <= $otherMax) ||
                                                            ($maxAge >= $otherMin && $maxAge <= $otherMax))) {
                                                            \Filament\Notifications\Notification::make()
                                                                ->warning()
                                                                ->title('Superposición detectada')
                                                                ->body("El rango {$minAge}-{$maxAge} se superpone con {$category['name']} ({$otherMin}-{$otherMax})")
                                                                ->send();
                                                        }
                                                    }
                                                }
                                            })
                                            ->columnSpan(1),

                                                        Forms\Components\TextInput::make('max_age')
                                                            ->label('Edad Máxima')
                                                            ->numeric()
                                                            ->minValue(5)
                                                            ->maxValue(100)
                                                            ->required()
                                                            ->live()
                                                            ->afterStateUpdated(function ($state, $get, $set) {
                                                if ($state <= $get('min_age')) {
                                                    $set('max_age', $get('min_age') + 1);
                                                }
                                            })
                                            ->helperText(fn ($get) => $get('min_age') && $get('max_age') ?
                                                'Rango: ' . $get('min_age') . '-' . $get('max_age') . ' años' :
                                                'Ingresa edad mínima primero')
                                                            ->columnSpan(1),

                                                        Forms\Components\TextInput::make('sort_order')
                                                            ->label('Orden')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->columnSpan(1),
                                                    ]),

                                                Forms\Components\Textarea::make('description')
                                                    ->label('Descripción')
                                                    ->placeholder('Descripción opcional de la categoría')
                                                    ->columnSpanFull()
                                                    ->rows(2),

                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Activa')
                                                    ->default(true)
                                                    ->inline(false),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nueva categoría')
                                            ->addActionLabel('Agregar categoría')
                                            ->reorderableWithButtons()
                                            ->cloneable()
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->minItems(0)
                                    ])
                                    ->visible(fn($record) => $record),

                                Forms\Components\Section::make('Preview del Impacto en Jugadoras')
                                    ->description('Revisa cómo afectarán tus cambios a las jugadoras existentes')
                                    ->icon('heroicon-o-eye')
                                    ->schema([
                                        // Mensaje para ligas no guardadas
                                        Forms\Components\Placeholder::make('save_first')
                                            ->label('')
                                            ->content(function () {
                                                return new HtmlString('
                                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                        <div class="flex items-start">
                                                            <div class="flex-shrink-0">
                                                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </div>
                                                            <div class="ml-3">
                                                                <h3 class="text-sm font-medium text-blue-800">Información</h3>
                                                                <p class="text-sm text-blue-700 mt-1">Guarda la liga primero para ver el preview del impacto</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ');
                                            })
                                            ->visible(fn($record) => !$record),

                                        // Sistema tradicional activo
                                        Forms\Components\Placeholder::make('traditional_system')
                                            ->label('')
                                            ->content(function () {
                                                return new HtmlString('
                                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                                        <div class="flex items-start">
                                                            <div class="flex-shrink-0">
                                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </div>
                                                            <div class="ml-3">
                                                                <h3 class="text-sm font-medium text-gray-800">Sistema Tradicional Activo</h3>
                                                                <p class="text-sm text-gray-600 mt-1">Configure categorías personalizadas para ver el impacto en las jugadoras.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ');
                                            })
                                            ->visible(fn($record) => $record && !$record->hasCustomCategories()),

                                        // Métricas principales mejoradas
                                        Forms\Components\Grid::make(4)
                                            ->schema([
                                                Forms\Components\Placeholder::make('sin_cambios')
                                                    ->label('Sin cambios')
                                                    ->content(function ($record) {
                                                        if (!$record || !$record->hasCustomCategories()) return new HtmlString('<div class="text-2xl font-bold text-gray-400">0</div><div class="text-sm text-gray-500">Mantienen categoría</div>');
                                                        $impact = static::calculateCategoryImpact($record);
                                                        $count = $impact['summary']['no_change'];
                                                        return new HtmlString('<div class="text-2xl font-bold text-success-600">' . $count . '</div><div class="text-sm text-gray-500">Mantienen categoría</div>');
                                                    }),

                                                Forms\Components\Placeholder::make('cambio_categoria')
                                                    ->label('Cambio categoría')
                                                    ->content(function ($record) {
                                                        if (!$record || !$record->hasCustomCategories()) return new HtmlString('<div class="text-2xl font-bold text-gray-400">0</div><div class="text-sm text-gray-500">Categoría diferente</div>');
                                                        $impact = static::calculateCategoryImpact($record);
                                                        $count = $impact['summary']['category_change'];
                                                        return new HtmlString('<div class="text-2xl font-bold text-warning-600">' . $count . '</div><div class="text-sm text-gray-500">Categoría diferente</div>');
                                                    }),

                                                Forms\Components\Placeholder::make('nueva_categoria')
                                                    ->label('Nueva categoría')
                                                    ->content(function ($record) {
                                                        if (!$record || !$record->hasCustomCategories()) return new HtmlString('<div class="text-2xl font-bold text-gray-400">0</div><div class="text-sm text-gray-500">Primera asignación</div>');
                                                        $impact = static::calculateCategoryImpact($record);
                                                        $count = $impact['summary']['new_category'];
                                                        return new HtmlString('<div class="text-2xl font-bold text-info-600">' . $count . '</div><div class="text-sm text-gray-500">Primera asignación</div>');
                                                    }),

                                                Forms\Components\Placeholder::make('sin_categoria')
                                                    ->label('Sin categoría')
                                                    ->content(function ($record) {
                                                        if (!$record || !$record->hasCustomCategories()) return new HtmlString('<div class="text-2xl font-bold text-gray-400">0</div><div class="text-sm text-gray-500">Requieren atención</div>');
                                                        $impact = static::calculateCategoryImpact($record);
                                                        $count = $impact['summary']['no_category'];
                                                        return new HtmlString('<div class="text-2xl font-bold text-danger-600">' . $count . '</div><div class="text-sm text-gray-500">Requieren atención</div>');
                                                    }),
                                            ])
                                            ->visible(fn($record) => $record && $record->hasCustomCategories()),

                                        // Alerta crítica mejorada
                                        Forms\Components\Placeholder::make('alerta_critica')
                                            ->content(function ($record) {
                                                if (!$record || !$record->hasCustomCategories()) return new HtmlString('');
                                                $impact = static::calculateCategoryImpact($record);
                                                $count = $impact['summary']['no_category'];
                                                if ($count === 0) return new HtmlString('');

                                                return new HtmlString('
                                                    <div class="bg-danger-50 border border-danger-200 rounded-lg p-4">
                                                        <div class="flex items-start">
                                                            <div class="flex-shrink-0">
                                                                <svg class="h-5 w-5 text-danger-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </div>
                                                            <div class="ml-3">
                                                                <h3 class="text-sm font-medium text-danger-800">Atención requerida</h3>
                                                                <p class="text-sm text-danger-700 mt-1">' . $count . ' jugadoras no tienen categoría asignada y necesitan configuración manual.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ');
                                            })
                                            ->visible(function ($record) {
                                                if (!$record || !$record->hasCustomCategories()) return false;
                                                $impact = static::calculateCategoryImpact($record);
                                                return $impact['summary']['no_category'] > 0;
                                            }),

                                        // Sección de jugadoras afectadas mejorada
                                        Forms\Components\Section::make('Jugadoras Afectadas')
                                            ->description('Detalle de las jugadoras que requieren atención')
                                            ->schema([
                                                Forms\Components\Placeholder::make('affected_players_list')
                                                    ->label('')
                                                    ->content(function ($record) {
                                                        if (!$record || !$record->hasCustomCategories()) {
                                                            return new HtmlString('<p class="text-sm text-gray-500">No hay datos disponibles</p>');
                                                        }

                                                        $impact = static::calculateCategoryImpact($record);
                                                        $players = array_slice($impact['affected_players'], 0, 5);

                                                        if (empty($players)) {
                                                            return new HtmlString('
                                                                <div class="text-center py-6">
                                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    <h3 class="mt-2 text-sm font-medium text-gray-900">¡Excelente!</h3>
                                                                    <p class="mt-1 text-sm text-gray-500">No hay jugadoras que requieran cambios</p>
                                                                </div>
                                                            ');
                                                        }

                                                        $html = '<div class="space-y-3">';
                                                        foreach ($players as $player) {
                                                            $changeText = match($player['change_type']) {
                                                                'no_change' => 'Sin cambios',
                                                                'category_change' => 'Cambio de categoría',
                                                                'new_category' => 'Nueva categoría',
                                                                'no_category' => 'Sin categoría'
                                                            };

                                                            $badgeColor = match($player['change_type']) {
                                                                'no_change' => 'bg-green-100 text-green-800',
                                                                'category_change' => 'bg-yellow-100 text-yellow-800',
                                                                'new_category' => 'bg-blue-100 text-blue-800',
                                                                'no_category' => 'bg-red-100 text-red-800'
                                                            };

                                                            $playerName = $player['player']->user->name ?? 'N/A';
                                                            $clubName = $player['player']->currentClub->name ?? 'Sin club';
                                                            $age = $player['player']->age ?? 'N/A';

                                                            $html .= '
                                                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                                    <div class="flex items-center space-x-3">
                                                                        <div class="flex-shrink-0">
                                                                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                                                <svg class="h-5 w-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                                                </svg>
                                                                            </div>
                                                                        </div>
                                                                        <div class="min-w-0 flex-1">
                                                                            <p class="text-sm font-medium text-gray-900">' . htmlspecialchars($playerName) . '</p>
                                                                            <p class="text-sm text-gray-500">' . $age . ' años • ' . htmlspecialchars($clubName) . '</p>
                                                                        </div>
                                                                    </div>
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $badgeColor . '">
                                                                        ' . $changeText . '
                                                                    </span>
                                                                </div>
                                                            ';
                                                        }

                                                        $total = count($impact['affected_players']);
                                                        if ($total > 5) {
                                                            $html .= '<div class="text-center text-sm text-gray-500 mt-4">... y ' . ($total - 5) . ' jugadoras más</div>';
                                                        }

                                                        $html .= '</div>';

                                                        return new HtmlString($html);
                                                    }),

                                                // Acciones mejoradas
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('ver_todas')
                                                        ->label('Ver todas las jugadoras afectadas')
                                                        ->icon('heroicon-o-eye')
                                                        ->color('primary')
                                                        ->action(function ($record) {
                                                            \Filament\Notifications\Notification::make()
                                                                ->title('Funcionalidad en desarrollo')
                                                                ->body('La vista detallada estará disponible próximamente')
                                                                ->info()
                                                                ->send();
                                                        })
                                                        ->visible(function ($record) {
                                                            if (!$record || !$record->hasCustomCategories()) return false;
                                                            $impact = static::calculateCategoryImpact($record);
                                                            return count($impact['affected_players']) > 0;
                                                        }),

                                                    Forms\Components\Actions\Action::make('configurar_automatico')
                                                        ->label('Aplicar configuración estándar')
                                                        ->icon('heroicon-o-cog-6-tooth')
                                                        ->color('success')
                                                        ->action(function ($record) {
                                                            \Filament\Notifications\Notification::make()
                                                                ->title('Funcionalidad en desarrollo')
                                                                ->body('La configuración automática estará disponible próximamente')
                                                                ->info()
                                                                ->send();
                                                        })
                                                        ->visible(function ($record) {
                                                            if (!$record || !$record->hasCustomCategories()) return false;
                                                            $impact = static::calculateCategoryImpact($record);
                                                            return $impact['summary']['no_category'] > 0;
                                                        }),
                                                ]),
                                            ])
                                            ->collapsible()
                                            ->collapsed(false)
                                            ->visible(function ($record) {
                                                if (!$record || !$record->hasCustomCategories()) return false;
                                                $impact = static::calculateCategoryImpact($record);
                                                return count($impact['affected_players']) > 0;
                                            }),

                                        // Distribución por categorías mejorada
                                        Forms\Components\Section::make('Distribución por Categoría')
                                            ->description('Cómo se distribuirán las jugadoras después de aplicar los cambios')
                                            ->schema([
                                                Forms\Components\Placeholder::make('distribucion')
                                                    ->label('')
                                                    ->content(function ($record) {
                                                        if (!$record || !$record->hasCustomCategories()) {
                                                            return new HtmlString('<p class="text-sm text-gray-500">Configure categorías personalizadas para ver la distribución</p>');
                                                        }

                                                        $impact = static::calculateCategoryImpact($record);
                                                        $unassigned = $impact['summary']['no_category'];

                                                        $html = '<div class="space-y-3">';

                                                        // Sin categoría (prioritario)
                                                        if ($unassigned > 0) {
                                                            $html .= '
                                                                <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg border border-amber-200">
                                                                    <div class="flex items-center">
                                                                        <div class="w-3 h-3 bg-amber-500 rounded-full mr-3"></div>
                                                                        <span class="text-sm font-medium text-amber-900">Sin categoría</span>
                                                                    </div>
                                                                    <span class="text-sm font-bold text-amber-900">' . $unassigned . ' jugadoras</span>
                                                                </div>
                                                            ';
                                                        }

                                                        // Obtener distribución por categorías tradicionales
                                                        $stats = $record->getCategoryStats();
                                                        foreach ($stats as $category => $count) {
                                                            if ($count > 0) {
                                                                $html .= '
                                                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                                        <div class="flex items-center">
                                                                            <div class="w-3 h-3 bg-gray-400 rounded-full mr-3"></div>
                                                                            <span class="text-sm text-gray-600">' . htmlspecialchars($category) . '</span>
                                                                        </div>
                                                                        <span class="text-sm text-gray-600">' . $count . ' jugadoras</span>
                                                                    </div>
                                                                ';
                                                            }
                                                        }

                                                        if ($unassigned === 0 && empty($stats)) {
                                                            $html .= '
                                                                <div class="text-center py-4">
                                                                    <p class="text-sm text-gray-500">No hay jugadoras registradas en esta liga</p>
                                                                </div>
                                                            ';
                                                        }

                                                        $html .= '</div>';

                                                        return new HtmlString($html);
                                                    }),
                                            ])
                                            ->collapsible()
                                            ->visible(fn($record) => $record && $record->hasCustomCategories()),
                                    ])
                                    ->collapsible()
                                    ->collapsed(false)
                                    ->visible(fn($record) => $record),
                            ]),

                        Forms\Components\Tabs\Tab::make('Reglas de Liga')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('Configuraciones de Liga')
                                    ->description('Estas configuraciones definen las reglas específicas de esta liga')
                                    ->schema([
                                        Forms\Components\Placeholder::make('config_info')
                                            ->label('')
                                            ->content('Las configuraciones de liga se gestionan después de crear la liga. Guarda primero la información básica.')
                                            ->visible(fn($record) => !$record),
                                    ])
                                    ->visible(fn($record) => !$record),

                                Forms\Components\Section::make('Configuraciones de Liga')
                                    ->description('Gestiona las reglas específicas de esta liga')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('manage_configurations')
                                                ->label('Gestionar Configuraciones')
                                                ->icon('heroicon-o-cog-6-tooth')
                                                ->color('primary')
                                                ->url(fn($record) => route('filament.admin.resources.leagues.configurations', $record)),
                                        ]),
                                    ])
                                    ->visible(fn($record) => $record),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->label('Logo')
                    ->collection('logo')
                    ->conversion('thumb')
                    ->circular()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('short_name')
                    ->label('Nombre Corto')
                    ->searchable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label('País')
                    ->sortable(),

                Tables\Columns\TextColumn::make('clubs_count')
                    ->label('Clubes')
                    ->counts('clubs')
                    ->badge(),

                Tables\Columns\TextColumn::make('categories_count')
                    ->label('Categorías')
                    ->state(fn($record) => $record->categories()->active()->count())
                    ->badge()
                    ->color(fn($record) => $record->hasCustomCategories() ? 'success' : 'gray')
                    ->tooltip(fn($record) => $record->hasCustomCategories() ?
                        'Configuración personalizada' :
                        'Sin configurar'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('founded_date')
                    ->label('Fundada')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(UserStatus::class),

                Tables\Filters\SelectFilter::make('country')
                    ->label('País')
                    ->relationship('country', 'name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('manage_categories')
                        ->label('Gestionar Categorías')
                        ->icon('heroicon-o-tag')
                        ->color('info')
                        ->url(fn($record) => route('filament.admin.resources.leagues.edit', ['record' => $record]) . '#categories')
                        ->visible(fn($record) => $record->hasCustomCategories()),

                    Tables\Actions\Action::make('create_default_categories')
                        ->label('Crear Categorías')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->action(function($record) {
                            $configService = app(\App\Services\LeagueConfigurationService::class);
                            $result = $configService->createDefaultCategories($record);

                            if ($result['success']) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Categorías creadas exitosamente')
                                    ->body("Se crearon {$result['categories_created']} categorías por defecto")
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Error creando categorías')
                                    ->body($result['message'])
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Crear Categorías por Defecto')
                        ->modalDescription('¿Estás seguro de que quieres crear las categorías por defecto para esta liga?')
                        ->visible(fn($record) => !$record->hasCustomCategories()),

                    Tables\Actions\Action::make('validate_categories')
                        ->label('Validar Configuración')
                        ->icon('heroicon-o-check-circle')
                        ->color('warning')
                        ->action(function($record) {
                            $configService = app(\App\Services\LeagueConfigurationService::class);
                            $validation = $configService->validateCategoryConfiguration($record);

                            if ($validation['valid']) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Configuración válida')
                                    ->body('La configuración de categorías es correcta')
                                    ->success()
                                    ->send();
                            } else {
                                $errorCount = count($validation['errors']);
                                $warningCount = count($validation['warnings']);

                                \Filament\Notifications\Notification::make()
                                    ->title('Configuración con problemas')
                                    ->body("Se encontraron {$errorCount} errores y {$warningCount} advertencias")
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->visible(fn($record) => $record->hasCustomCategories()),
                ])
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
                        Infolists\Components\SpatieMediaLibraryImageEntry::make('logo')
                            ->label('Logo')
                            ->collection('logo')
                            ->conversion('thumb')
                            ->circular()
                            ->size(80),

                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre'),

                        Infolists\Components\TextEntry::make('short_name')
                            ->label('Nombre Corto'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),

                        Infolists\Components\TextEntry::make('country.name')
                            ->label('País'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),
                    ])->columns(2),

                Infolists\Components\Section::make('Contacto')
                    ->schema([
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('phone')
                            ->label('Teléfono')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('website')
                            ->label('Sitio Web')
                            ->url(fn($record) => $record->website)
                            ->openUrlInNewTab(),
                    ])->columns(3),

                Infolists\Components\Section::make('Estadísticas')
                    ->schema([
                        Infolists\Components\TextEntry::make('clubs_count')
                            ->label('Total de Clubes')
                            ->state(fn($record) => $record->clubs()->count()),

                        Infolists\Components\TextEntry::make('players_count')
                            ->label('Total de Jugadoras')
                            ->state(fn($record) => $record->clubs()->withCount('players')->get()->sum('players_count')),

                        Infolists\Components\TextEntry::make('categories_count')
                            ->label('Categorías Configuradas')
                            ->state(fn($record) => $record->categories()->active()->count())
                            ->badge()
                            ->color(fn($record) => $record->hasCustomCategories() ? 'success' : 'gray'),

                        Infolists\Components\TextEntry::make('founded_date')
                            ->label('Fecha de Fundación')
                            ->date(),
                    ])->columns(4),

                Infolists\Components\Section::make('Configuración de Categorías')
                    ->schema([
                        Infolists\Components\TextEntry::make('categories_status')
                            ->label('Estado de Categorías')
                            ->state(function($record) {
                                if (!$record->hasCustomCategories()) {
                                    return 'Sin configurar';
                                }

                                $validation = app(\App\Services\LeagueConfigurationService::class)
                                    ->validateCategoryConfiguration($record);

                                return $validation['valid'] ? 'Configuración válida' : 'Con errores';
                            })
                            ->badge()
                            ->color(function($record) {
                                if (!$record->hasCustomCategories()) {
                                    return 'gray';
                                }

                                $validation = app(\App\Services\LeagueConfigurationService::class)
                                    ->validateCategoryConfiguration($record);

                                return $validation['valid'] ? 'success' : 'danger';
                            }),

                        Infolists\Components\TextEntry::make('age_coverage')
                            ->label('Cobertura de Edad')
                            ->state(function($record) {
                                if (!$record->hasCustomCategories()) {
                                    return 'N/A';
                                }

                                $categories = $record->getActiveCategories();
                                if ($categories->isEmpty()) {
                                    return 'N/A';
                                }

                                return $categories->min('min_age') . '-' . $categories->max('max_age') . ' años';
                            }),

                        Infolists\Components\TextEntry::make('category_distribution')
                            ->label('Distribución por Categorías')
                            ->state(function($record) {
                                if (!$record->hasCustomCategories()) {
                                    return 'N/A';
                                }

                                $stats = $record->getCategoryStats();
                                $total = array_sum($stats);

                                if ($total === 0) {
                                    return 'Sin jugadoras asignadas';
                                }

                                $distribution = [];
                                foreach ($stats as $category => $count) {
                                    if ($count > 0) {
                                        $percentage = round(($count / $total) * 100, 1);
                                        $distribution[] = "{$category}: {$count} ({$percentage}%)";
                                    }
                                }

                                return implode(', ', array_slice($distribution, 0, 3)) .
                                       (count($distribution) > 3 ? '...' : '');
                            })
                            ->tooltip(function($record) {
                                if (!$record->hasCustomCategories()) {
                                    return null;
                                }

                                $stats = $record->getCategoryStats();
                                $total = array_sum($stats);

                                if ($total === 0) {
                                    return 'No hay jugadoras asignadas a categorías';
                                }

                                $distribution = [];
                                foreach ($stats as $category => $count) {
                                    if ($count > 0) {
                                        $percentage = round(($count / $total) * 100, 1);
                                        $distribution[] = "{$category}: {$count} jugadoras ({$percentage}%)";
                                    }
                                }

                                return implode("\n", $distribution);
                            }),
                    ])->columns(3)
                    ->visible(fn($record) => $record->hasCustomCategories()),
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
            'index' => Pages\ListLeagues::route('/'),
            'create' => Pages\CreateLeague::route('/create'),
            'view' => Pages\ViewLeague::route('/{record}'),
            'edit' => Pages\EditLeague::route('/{record}/edit'),
            'configurations' => Pages\ManageLeagueConfigurations::route('/{record}/configurations'),
        ];
    }

    private static function calculateCategoryImpact(League $league): array
    {
        $players = Player::whereHas('currentClub', function ($query) use ($league) {
            $query->where('league_id', $league->id);
        })->with('currentClub', 'user')->get();

        $impact = [
            'total_players' => $players->count(),
            'affected_players' => [],
            'category_changes' => [],
            'summary' => [
                'no_change' => 0,
                'category_change' => 0,
                'new_category' => 0,
                'no_category' => 0,
            ],
        ];

        if (!$league->hasCustomCategories()) {
            return $impact;
        }

        $customCategories = $league->getActiveCategories();

        foreach ($players as $player) {
            $currentAge = $player->age;
            $gender = $player->user->gender ?? 'female';

            // Categoría tradicional actual
            $traditionalCategory = PlayerCategory::getForAge($currentAge, $gender);

            // Buscar categoría personalizada que corresponde
            $customCategory = $customCategories->first(function ($category) use ($currentAge) {
                return $currentAge >= $category->min_age && $currentAge <= $category->max_age;
            });

            $changeType = 'no_change';
            $changeDescription = '';

            if (!$customCategory) {
                $changeType = 'no_category';
                $changeDescription = 'No tiene categoría en el sistema personalizado';
            } elseif ($customCategory->code !== $traditionalCategory->value) {
                $changeType = 'category_change';
                $changeDescription = "Cambio de {$traditionalCategory->value} a {$customCategory->code}";
            } else {
                $changeType = 'no_change';
                $changeDescription = 'Mantiene la misma categoría';
            }

            $impact['summary'][$changeType]++;

            if ($changeType !== 'no_change') {
                $impact['affected_players'][] = [
                    'player' => $player,
                    'change_type' => $changeType,
                    'description' => $changeDescription,
                    'traditional_category' => $traditionalCategory->value,
                    'new_category' => $customCategory?->code,
                ];
            }
        }

        return $impact;
    }
}
