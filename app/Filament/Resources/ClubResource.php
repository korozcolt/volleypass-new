<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClubResource\Pages;
use App\Models\Club;
use App\Models\League;
use App\Models\User;
use App\Enums\UserStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ClubResource extends Resource
{
    protected static ?string $model = Club::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Clubes';
    protected static ?string $modelLabel = 'Club';
    protected static ?string $pluralModelLabel = 'Clubes';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Información General')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                if ($operation !== 'create') {
                                                    return;
                                                }
                                                $set('short_name', str($state)->limit(10)->toString());
                                            }),

                                        Forms\Components\TextInput::make('short_name')
                                            ->label('Nombre Corto')
                                            ->maxLength(50)
                                            ->required(),

                                        Forms\Components\Select::make('league_id')
                                            ->label('Liga')
                                            ->relationship('league', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        Forms\Components\Select::make('director_id')
                                            ->label('Director')
                                            ->relationship('director', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required(),
                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->required(),
                                            ]),

                                        Forms\Components\Select::make('status')
                                            ->label('Estado')
                                            ->options(UserStatus::class)
                                            ->required()
                                            ->default('active'),

                                        Forms\Components\DatePicker::make('foundation_date')
                                            ->label('Fecha de Fundación')
                                            ->maxDate(now()),
                                    ]),

                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('colors')
                                            ->label('Colores del Club'),

                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                if (filter_var($state, FILTER_VALIDATE_EMAIL)) {
                                                    $set('email_verified', true);
                                                }
                                            }),

                                        Forms\Components\TextInput::make('phone')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->maxLength(20),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('website')
                                            ->label('Sitio Web')
                                            ->url()
                                            ->maxLength(255)
                                            ->prefix('https://'),

                                        Forms\Components\TextInput::make('address')
                                            ->label('Dirección')
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('country_id')
                                            ->label('País')
                                            ->relationship('country', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live(),

                                        Forms\Components\Select::make('department_id')
                                            ->label('Departamento')
                                            ->relationship('department', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live(),

                                        Forms\Components\Select::make('city_id')
                                            ->label('Ciudad')
                                            ->relationship('city', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Configuración')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('federation_type')
                                            ->label('Tipo de Federación')
                                            ->options([
                                                'national' => 'Nacional',
                                                'regional' => 'Regional',
                                                'local' => 'Local',
                                                'none' => 'Sin Federación',
                                            ])
                                            ->default('local')
                                            ->live(),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Club Activo')
                                            ->default(true),

                                        Forms\Components\Toggle::make('accepts_transfers')
                                            ->label('Acepta Transferencias')
                                            ->default(true),

                                        Forms\Components\Toggle::make('auto_approve_players')
                                            ->label('Auto-aprobar Jugadoras')
                                            ->default(false),
                                    ]),

                                Forms\Components\KeyValue::make('settings')
                                    ->label('Configuraciones Adicionales')
                                    ->keyLabel('Clave')
                                    ->valueLabel('Valor')
                                    ->addActionLabel('Agregar configuración'),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Notas Administrativas')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Directivos')
                            ->schema([
                                Forms\Components\Repeater::make('club_directors')
                                    ->label('Directivos del Club')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('user_id')
                                                    ->label('Persona')
                                                    ->relationship('user', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),

                                                Forms\Components\Select::make('role')
                                                    ->label('Cargo')
                                                    ->options([
                                                        'president' => 'Presidente',
                                                        'vice_president' => 'Vicepresidente',
                                                        'secretary' => 'Secretario',
                                                        'treasurer' => 'Tesorero',
                                                        'director' => 'Director',
                                                        'coach' => 'Entrenador',
                                                    ])
                                                    ->required(),

                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Activo')
                                                    ->default(true),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\DatePicker::make('start_date')
                                                    ->label('Fecha de Inicio')
                                                    ->default(now())
                                                    ->required(),

                                                Forms\Components\DatePicker::make('end_date')
                                                    ->label('Fecha de Fin')
                                                    ->after('start_date'),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['role'] ?? null)
                                    ->addActionLabel('Agregar Directivo')
                                    ->defaultItems(0),
                            ]),

                        Forms\Components\Tabs\Tab::make('Medios')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\FileUpload::make('logo')
                                            ->label('Logo del Club')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->directory('clubs/logos')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('1:1')
                                            ->imageResizeTargetWidth('200')
                                            ->imageResizeTargetHeight('200'),

                                        Forms\Components\FileUpload::make('photos')
                                            ->label('Fotos del Club')
                                            ->image()
                                            ->multiple()
                                            ->maxSize(2048)
                                            ->maxFiles(10)
                                            ->directory('clubs/photos')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')
                                            ->imageResizeTargetWidth('800')
                                            ->imageResizeTargetHeight('600'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/default-club-logo.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                Tables\Columns\TextColumn::make('short_name')
                    ->label('Siglas')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('league.name')
                    ->label('Liga')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('federation_type')
                    ->label('Federación')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'national' => 'success',
                        'regional' => 'warning',
                        'local' => 'info',
                        'none' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'national' => 'Nacional',
                        'regional' => 'Regional',
                        'local' => 'Local',
                        'none' => 'Sin Federación',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('players_count')
                    ->label('Jugadoras')
                    ->counts('players')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 50 => 'success',
                        $state >= 20 => 'warning',
                        $state > 0 => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('director.name')
                    ->label('Director')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ciudad')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
                        'suspended' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('foundation_date')
                    ->label('Fundado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(UserStatus::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('league')
                    ->label('Liga')
                    ->relationship('league', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('federation_type')
                    ->label('Tipo de Federación')
                    ->options([
                        'national' => 'Nacional',
                        'regional' => 'Regional',
                        'local' => 'Local',
                        'none' => 'Sin Federación',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('department')
                    ->label('Departamento')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('players_count')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('players_from')
                                    ->label('Jugadoras desde')
                                    ->numeric()
                                    ->placeholder('0'),
                                Forms\Components\TextInput::make('players_until')
                                    ->label('Jugadoras hasta')
                                    ->numeric()
                                    ->placeholder('100'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['players_from'],
                                fn (Builder $query, $value): Builder => $query->withCount('players')->having('players_count', '>=', $value),
                            )
                            ->when(
                                $data['players_until'],
                                fn (Builder $query, $value): Builder => $query->withCount('players')->having('players_count', '<=', $value),
                            );
                    }),

                Tables\Filters\Filter::make('foundation_date')
                    ->form([
                        Forms\Components\DatePicker::make('founded_from')
                            ->label('Fundado desde'),
                        Forms\Components\DatePicker::make('founded_until')
                            ->label('Fundado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['founded_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('foundation_date', '>=', $date),
                            )
                            ->when(
                                $data['founded_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('foundation_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('view_players')
                        ->label('Ver Jugadoras')
                        ->icon('heroicon-o-users')
                        ->url(fn ($record) => route('filament.admin.resources.players.index', ['tableFilters[club][value]' => $record->id])),
                    Tables\Actions\Action::make('generate_cards')
                        ->label('Generar Carnets')
                        ->icon('heroicon-o-identification')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            // Lógica para generar carnets masivos
                            \Filament\Notifications\Notification::make()
                                ->title('Carnets generados')
                                ->body("Se han generado los carnets para {$record->name}")
                                ->success()
                                ->send();
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('federate')
                        ->label('Federar Clubes')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Select::make('federation_type')
                                ->label('Tipo de Federación')
                                ->options([
                                    'national' => 'Nacional',
                                    'regional' => 'Regional',
                                    'local' => 'Local',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['federation_type' => $data['federation_type']]);
                            });
                            \Filament\Notifications\Notification::make()
                                ->title('Clubes federados')
                                ->body("Se han federado {$records->count()} clubes")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Exportar Datos')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (Collection $records) {
                            // Lógica de exportación
                            \Filament\Notifications\Notification::make()
                                ->title('Exportación iniciada')
                                ->body("Se están exportando {$records->count()} clubes")
                                ->info()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('name')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información General')
                    ->schema([
                        Infolists\Components\ImageEntry::make('logo')
                            ->label('Logo')
                            ->circular()
                            ->size(80),

                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre'),

                        Infolists\Components\TextEntry::make('short_name')
                            ->label('Nombre Corto'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),

                        Infolists\Components\TextEntry::make('league.name')
                            ->label('Liga'),

                        Infolists\Components\TextEntry::make('director.name')
                            ->label('Director'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),

                        Infolists\Components\TextEntry::make('colors')
                            ->label('Colores'),
                    ])->columns(2),

                Infolists\Components\Section::make('Ubicación')
                    ->schema([
                        Infolists\Components\TextEntry::make('address')
                            ->label('Dirección'),

                        Infolists\Components\TextEntry::make('city.name')
                            ->label('Ciudad'),

                        Infolists\Components\TextEntry::make('department.name')
                            ->label('Departamento'),

                        Infolists\Components\TextEntry::make('country.name')
                            ->label('País'),
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
                        Infolists\Components\TextEntry::make('players_count')
                            ->label('Total de Jugadoras')
                            ->state(fn($record) => $record->players()->count()),

                        Infolists\Components\TextEntry::make('teams_count')
                            ->label('Total de Equipos')
                            ->state(fn($record) => \App\Models\Team::where('club_id', $record->id)->count()),

                        Infolists\Components\TextEntry::make('foundation_date')
                            ->label('Fecha de Fundación')
                            ->date(),
                    ])->columns(3),
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
            'index' => Pages\ListClubs::route('/'),
            'create' => Pages\CreateClub::route('/create'),
            'view' => Pages\ViewClub::route('/{record}'),
            'edit' => Pages\EditClub::route('/{record}/edit'),
            'players' => Pages\ManagePlayers::route('/{record}/players'),
            'payments' => Pages\ManagePayments::route('/{record}/payments'),
            'documents' => Pages\ManageDocuments::route('/{record}/documents'),
            'activity' => Pages\ManageActivity::route('/{record}/activity'),
        ];
    }
}
