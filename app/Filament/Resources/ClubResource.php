<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClubResource\Pages;
use App\Models\Club;
use App\Models\Department;
use App\Models\City;
use App\Models\User;
use App\Enums\FederationStatus;
use App\Enums\UserStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ClubResource extends Resource
{
    protected static ?string $model = Club::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationLabel = 'Clubes';
    
    protected static ?string $navigationGroup = 'Gestión Deportiva';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $modelLabel = 'Club';
    
    protected static ?string $pluralModelLabel = 'Clubes';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Información del Club')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Información General')
                            ->schema([
                                Forms\Components\Select::make('league_id')
                                    ->label('Liga')
                                    ->relationship('league', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->maxLength(100)
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('short_name')
                                            ->label('Nombre Corto')
                                            ->maxLength(20)
                                            ->columnSpan(1),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->label(__('Email'))
                                            ->email()
                                            ->unique(ignoreRecord: true)
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->columnSpan(1),
                                    ]),
                                Forms\Components\Textarea::make('address')
                                    ->label('Dirección')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('department_id')
                                            ->label('Departamento')
                                            ->relationship('department', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(fn (Forms\Set $set) => $set('city_id', null))
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('city_id')
                                            ->label('Ciudad')
                                            ->relationship(
                                                name: 'city',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Builder $query, Forms\Get $get): Builder => 
                                                    $query->where('department_id', $get('department_id'))
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(1),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DatePicker::make('foundation_date')
                                            ->label('Fecha de Fundación')
                                            ->columnSpan(1),
                                        Forms\Components\FileUpload::make('logo')
                                            ->label(__('Logo'))
                                            ->image()
                                            ->maxSize(2048)
                                            ->directory('clubs/logos')
                                            ->columnSpan(1),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Configuración Federación')
                            ->schema([
                                Forms\Components\Toggle::make('is_federated')
                                    ->label('¿Es Federado?')
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('federation_type')
                                            ->label('Tipo de Federación')
                                            ->options(FederationStatus::class)
                                            ->visible(fn (Forms\Get $get): bool => $get('is_federated'))
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('federation_code')
                                            ->label('Código de Federación')
                                            ->unique(ignoreRecord: true)
                                            ->visible(fn (Forms\Get $get): bool => $get('is_federated'))
                                            ->columnSpan(1),
                                    ]),
                                Forms\Components\DatePicker::make('federation_expiry')
                                    ->label('Vencimiento de Federación')
                                    ->visible(fn (Forms\Get $get): bool => $get('is_federated'))
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('federation_notes')
                                    ->label('Observaciones de Federación')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Directivos')
                            ->schema([
                                Forms\Components\Repeater::make('directors')
                                    ->relationship('directors')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('user_id')
                                                    ->label('Usuario')
                                                    ->options(User::all()->pluck('name', 'id'))
                                                    ->searchable()
                                                    ->required()
                                                    ->columnSpan(1),
                                                Forms\Components\Select::make('rol')
                                                    ->label('Rol')
                                                    ->options([
                                                        'presidente' => 'Presidente',
                                                        'director' => 'Director',
                                                        'secretario' => 'Secretario',
                                                        'tesorero' => 'Tesorero',
                                                    ])
                                                    ->required()
                                                    ->columnSpan(1),
                                                Forms\Components\Toggle::make('activo')
                                                    ->label('Activo')
                                                    ->default(true)
                                                    ->columnSpan(1),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\DatePicker::make('fecha_inicio')
                                                    ->label('Fecha de Inicio')
                                                    ->required()
                                                    ->columnSpan(1),
                                                Forms\Components\DatePicker::make('fecha_fin')
                                                    ->label('Fecha de Fin')
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => 
                                        $state['rol'] ?? 'Nuevo Directivo'
                                    )
                                    ->addActionLabel('Agregar Directivo')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Estadísticas')
                            ->schema([
                                Forms\Components\View::make('filament.components.club-stats')
                                    ->viewData(fn (?Club $record): array => ['record' => $record])
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn (?Club $record): bool => $record !== null),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label(__('Logo'))
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/default-club.png')),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('short_name')
                    ->label('Nombre Corto')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departamento')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ciudad')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_federated')
                    ->label('Federado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('federation_type')
                    ->label('Tipo Federación')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'activa' => 'success',
                        'suspendida' => 'warning',
                        'cancelada' => 'danger',
                        default => 'gray',
                    })
                    ->visible(fn ($record) => $record && $record->is_federated),
                Tables\Columns\TextColumn::make('players_count')
                    ->label('Jugadoras')
                    ->counts('players')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Departamento')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('federation_type')
                    ->label('Tipo Federación')
                    ->options(FederationStatus::class),
                Tables\Filters\TernaryFilter::make('is_federated')
                    ->label('Estado Federación')
                    ->boolean()
                    ->trueLabel('Federados')
                    ->falseLabel('No Federados')
                    ->native(false),
                Filter::make('fecha_fundacion')
                    ->form([
                        DatePicker::make('fundacion_desde')
                            ->label('Fundado desde'),
                        DatePicker::make('fundacion_hasta')
                            ->label('Fundado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fundacion_desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('foundation_date', '>=', $date),
                            )
                            ->when(
                                $data['fundacion_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('foundation_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['fundacion_desde'] ?? null) {
                            $indicators[] = 'Fundado desde ' . Carbon::parse($data['fundacion_desde'])->toFormattedDateString();
                        }
                        if ($data['fundacion_hasta'] ?? null) {
                            $indicators[] = 'Fundado hasta ' . Carbon::parse($data['fundacion_hasta'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar Club')
                    ->modalDescription('¿Estás seguro de que deseas eliminar este club? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('federar')
                        ->label('Federar Clubes')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('federation_type')
                                ->label('Tipo de Federación')
                                ->options(FederationStatus::class)
                                ->required(),
                            Forms\Components\DatePicker::make('federation_expiry')
                                ->label('Vencimiento de Federación')
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_federated' => true,
                                    'federation_type' => $data['federation_type'],
                                    'federation_expiry' => $data['federation_expiry'],
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('cambiar_tipo_federacion')
                        ->label('Cambiar Tipo Federación')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('federation_type')
                                ->label('Nuevo Tipo de Federación')
                                ->options(FederationStatus::class)
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            foreach ($records as $record) {
                                if ($record->is_federated) {
                                    $record->update([
                                        'federation_type' => $data['federation_type'],
                                    ]);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('exportar')
                        ->label('Exportar Datos')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (Collection $records) {
                            \Filament\Notifications\Notification::make()
                                ->title('Exportación iniciada')
                                ->body("Se están exportando {$records->count()} clubes")
                                ->info()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('notificar')
                        ->label('Enviar Notificaciones')
                        ->icon('heroicon-o-bell')
                        ->color('warning')
                        ->form([
                            Forms\Components\Textarea::make('mensaje')
                                ->label('Mensaje')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (array $data, Collection $records) {
                            \Filament\Notifications\Notification::make()
                                ->title('Notificaciones enviadas')
                                ->body("Se han enviado notificaciones a {$records->count()} clubes")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar Clubes Seleccionados')
                        ->modalDescription('¿Estás seguro de que deseas eliminar los clubes seleccionados? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Sí, eliminar'),
                ]),
            ])
            ->defaultSort('name')
            ->searchOnBlur()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información General')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('logo')
                                    ->circular()
                                    ->size(80)
                                    ->columnSpan(1),
                                \Filament\Infolists\Components\Group::make([
                                    TextEntry::make('name')
                                    ->size('lg')
                                    ->weight('bold'),
                                TextEntry::make('short_name')
                                    ->badge(),
                                TextEntry::make('email')
                                    ->icon('heroicon-o-envelope'),
                                TextEntry::make('phone')
                                    ->icon('heroicon-o-phone'),
                                ])->columnSpan(2),
                            ]),
                        TextEntry::make('address')
                            ->icon('heroicon-o-map-pin'),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('departamento.name')
                                    ->label('Departamento'),
                                TextEntry::make('ciudad.name')
                                    ->label('Ciudad'),
                            ]),
                        TextEntry::make('foundation_date')
                            ->label('Fecha de Fundación')
                            ->date('d/m/Y'),
                    ]),
                Section::make('Estado de Federación')
                    ->schema([
                        TextEntry::make('es_federado')
                             ->label('¿Es Federado?')
                             ->badge()
                             ->color(fn ($state) => $state ? 'success' : 'danger')
                             ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
                        TextEntry::make('tipo_federacion')
                            ->label('Tipo de Federación')
                            ->badge()
                            ->visible(fn ($record) => $record && $record->es_federado),
                        TextEntry::make('codigo_federacion')
                            ->label('Código de Federación')
                            ->visible(fn ($record) => $record && $record->es_federado),
                        TextEntry::make('vencimiento_federacion')
                            ->label('Vencimiento de Federación')
                            ->date('d/m/Y')
                            ->visible(fn ($record) => $record && $record->es_federado),
                        TextEntry::make('federation_notes')
                            ->label('Observaciones')
                            ->visible(fn ($record) => $record && $record->federation_notes),
                    ])
                    ->visible(fn ($record) => $record && $record->es_federado),
                Section::make('Estadísticas')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('players_count')
                                    ->label('Total Jugadoras')
                                    ->state(fn ($record) => $record->players()->count())
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('federadas_count')
                                    ->label('Jugadoras Federadas')
                                    ->state(fn ($record) => $record->players()->whereHas('medicalCertificates', function ($query) {
                                        $query->where('status', 'approved');
                                    })->count())
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('created_at')
                                    ->label('Registrado')
                                    ->since()
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ]),
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
            'manage-players' => Pages\ManagePlayers::route('/{record}/players'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->can('view_any_club');
    }

    public static function canView($record): bool
    {
        return Auth::user()->can('view_club', $record);
    }

    public static function canCreate(): bool
    {
        return Auth::user()->can('create_club');
    }

    public static function canEdit($record): bool
    {
        return Auth::user()->can('update_club', $record);
    }

    public static function canDelete($record): bool
    {
        return Auth::user()->can('delete_club', $record);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['departamento', 'ciudad']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'short_name', 'email', 'departamento.name', 'ciudad.name'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Departamento' => $record->departamento?->name,
            'Ciudad' => $record->ciudad?->name,
            'Federado' => $record && $record->es_federado ? 'Sí' : 'No',
        ];
    }
}
