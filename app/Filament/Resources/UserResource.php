<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Enums\UserStatus;
use App\Enums\Gender;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Gestión de Usuarios';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static ?string $slug = 'usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('Nombres')
                                    ->required()
                                    ->maxLength(100),

                                Forms\Components\TextInput::make('last_name')
                                    ->label('Apellidos')
                                    ->required()
                                    ->maxLength(100),

                                Forms\Components\TextInput::make('document_number')
                                    ->label('Número de Documento')
                                    ->required()
                                    ->unique(User::class, 'document_number', ignoreRecord: true)
                                    ->maxLength(20),

                                Forms\Components\Select::make('gender')
                                    ->label('Género')
                                    ->options(Gender::class)
                                    ->required()
                                    ->native(false),

                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('Fecha de Nacimiento')
                                    ->required()
                                    ->maxDate(now()->subYears(10)),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(15),
                            ]),
                    ]),

                Forms\Components\Section::make('Información de Contacto')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->unique(User::class, 'email', ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('password')
                                    ->label('Contraseña')
                                    ->password()
                                    ->required(fn($context) => $context === 'create')
                                    ->dehydrated(fn($state) => filled($state))
                                    ->minLength(8),

                                Forms\Components\Textarea::make('address')
                                    ->label('Dirección')
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Forms\Components\Section::make('Ubicación')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('country_id')
                                    ->label('País')
                                    ->relationship('country', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('department_id', null);
                                        $set('city_id', null);
                                    }),

                                Forms\Components\Select::make('department_id')
                                    ->label('Departamento')
                                    ->relationship(
                                        name: 'department',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query, callable $get) =>
                                        $query->when(
                                            $get('country_id'),
                                            fn($q, $countryId) => $q->where('country_id', $countryId)
                                        )
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(fn(callable $set) => $set('city_id', null)),

                                Forms\Components\Select::make('city_id')
                                    ->label('Ciudad')
                                    ->relationship(
                                        name: 'city',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query, callable $get) =>
                                        $query->when(
                                            $get('department_id'),
                                            fn($q, $departmentId) => $q->where('department_id', $departmentId)
                                        )
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Roles y Estado')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('roles')
                                    ->label('Roles')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->native(false)
                                    ->placeholder('Selecciona uno o más roles'),

                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options(UserStatus::class)
                                    ->default('active')
                                    ->required()
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
                            ->maxSize(2048)
                            ->directory('avatars')
                            ->visibility('public')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre Completo')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('document_number')
                    ->label('Documento')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->colors([
                        'danger' => 'SuperAdmin',
                        'primary' => 'LeagueAdmin',
                        'success' => 'ClubDirector',
                        'info' => 'Player',
                        'warning' => 'Coach',
                        'purple' => 'SportsDoctor',
                        'gray' => 'Verifier',
                    ]),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => $state->getColor()),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(UserStatus::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('gender')
                    ->label('Género')
                    ->options(Gender::class),

                Tables\Filters\SelectFilter::make('department')
                    ->label('Departamento')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('verified_email')
                    ->label('Email Verificado')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('email_verified_at')),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información Personal')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\ImageEntry::make('avatar')
                                    ->label('Avatar')
                                    ->circular()
                                    ->size(80),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('name')
                                        ->label('Nombre Completo')
                                        ->weight('bold')
                                        ->size('lg'),

                                    Infolists\Components\TextEntry::make('email')
                                        ->label('Email')
                                        ->icon('heroicon-o-envelope')
                                        ->copyable(),

                                    Infolists\Components\TextEntry::make('document_number')
                                        ->label('Documento')
                                        ->icon('heroicon-o-identification'),

                                    Infolists\Components\TextEntry::make('phone')
                                        ->label('Teléfono')
                                        ->icon('heroicon-o-phone'),
                                ]),
                            ]),
                    ]),

                Infolists\Components\Section::make('Estado y Roles')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => $state->getLabel())
                                    ->color(fn($state) => $state->getColor()),

                                Infolists\Components\TextEntry::make('roles.name')
                                    ->label('Roles')
                                    ->badge()
                                    ->separator(',')
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('age')
                                    ->label('Edad')
                                    ->suffix(' años'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Ubicación')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('country.name')
                                    ->label('País'),

                                Infolists\Components\TextEntry::make('department.name')
                                    ->label('Departamento'),

                                Infolists\Components\TextEntry::make('city.name')
                                    ->label('Ciudad'),
                            ]),

                        Infolists\Components\TextEntry::make('address')
                            ->label('Dirección')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Información Adicional')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notas')
                            ->columnSpanFull()
                            ->placeholder('Sin notas adicionales'),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Creado')
                                    ->dateTime('d/m/Y H:i'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Actualizado')
                                    ->dateTime('d/m/Y H:i'),
                            ]),
                    ])
                    ->collapsible(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 50 ? 'warning' : 'primary';
    }
}
