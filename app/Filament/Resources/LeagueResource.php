<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeagueResource\Pages;
use App\Models\League;
use App\Models\Country;
use App\Enums\UserStatus;
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

class LeagueResource extends Resource
{
    protected static ?string $model = League::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Ligas';
    protected static ?string $modelLabel = 'Liga';
    protected static ?string $pluralModelLabel = 'Ligas';
    protected static ?string $navigationGroup = 'Configuración';
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
                                    ->schema([
                                        Forms\Components\ViewField::make('categories_management')
                                            ->label('')
                                            ->view('filament.forms.components.league-categories-manager')
                                            ->viewData(fn($record) => ['league' => $record]),
                                    ])
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
}
