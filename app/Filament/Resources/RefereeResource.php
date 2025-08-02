<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefereeResource\Pages;
use App\Models\Referee;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RefereeResource extends Resource
{
    protected static ?string $model = Referee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Árbitros';

    protected static ?string $modelLabel = 'Árbitro';

    protected static ?string $pluralModelLabel = 'Árbitros';

    protected static ?string $navigationGroup = 'Gestión de Usuarios';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Usuario')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->unique(User::class, 'email')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('password')
                                    ->label('Contraseña')
                                    ->password()
                                    ->required()
                                    ->minLength(8)
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(20),

                                Forms\Components\Select::make('gender')
                                    ->label('Género')
                                    ->options(Gender::class)
                                    ->required(),

                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('Fecha de Nacimiento')
                                    ->maxDate(now()->subYears(16)),

                                Forms\Components\TextInput::make('document_number')
                                    ->label('Número de Documento')
                                    ->maxLength(20),
                            ])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Información del Árbitro')
                    ->schema([
                        Forms\Components\TextInput::make('license_number')
                            ->label('Número de Licencia')
                            ->required()
                            ->unique(Referee::class, 'license_number', ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'regional' => 'Regional',
                                'nacional' => 'Nacional',
                                'internacional' => 'Internacional',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('experience_years')
                            ->label('Años de Experiencia')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(50)
                            ->default(0),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(UserStatus::class)
                            ->required()
                            ->default(UserStatus::Active),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->maxLength(1000)
                            ->rows(3),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Hidden::make('created_by')
                            ->default(Auth::user()?->id),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('license_number')
                    ->label('Licencia')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.phone')
                    ->label('Teléfono')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'secondary' => 'regional',
                        'warning' => 'nacional',
                        'success' => 'internacional',
                    ]),

                Tables\Columns\TextColumn::make('experience_years')
                    ->label('Experiencia')
                    ->suffix(' años')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => UserStatus::Active,
                        'warning' => UserStatus::Pending,
                        'danger' => UserStatus::Inactive,
                        'gray' => UserStatus::Suspended,
                    ]),

                Tables\Columns\TextColumn::make('matches_count')
                    ->label('Partidos')
                    ->counts('matches')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'regional' => 'Regional',
                        'nacional' => 'Nacional',
                        'internacional' => 'Internacional',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(UserStatus::class),

                Tables\Filters\Filter::make('experience')
                    ->form([
                        Forms\Components\TextInput::make('min_experience')
                            ->label('Experiencia mínima (años)')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_experience')
                            ->label('Experiencia máxima (años)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_experience'],
                                fn (Builder $query, $years): Builder => $query->where('experience_years', '>=', $years),
                            )
                            ->when(
                                $data['max_experience'],
                                fn (Builder $query, $years): Builder => $query->where('experience_years', '<=', $years),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Nombre Completo'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Correo Electrónico'),
                        Infolists\Components\TextEntry::make('user.phone')
                            ->label('Teléfono'),
                        Infolists\Components\TextEntry::make('user.gender')
                            ->label('Género'),
                        Infolists\Components\TextEntry::make('user.birth_date')
                            ->label('Fecha de Nacimiento')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('user.document_number')
                            ->label('Documento'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Información Profesional')
                    ->schema([
                        Infolists\Components\TextEntry::make('license_number')
                            ->label('Número de Licencia'),
                        Infolists\Components\TextEntry::make('category')
                            ->label('Categoría')
                            ->badge(),
                        Infolists\Components\TextEntry::make('experience_years')
                            ->label('Años de Experiencia')
                            ->suffix(' años'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),
                        Infolists\Components\TextEntry::make('matches_count')
                            ->label('Partidos Dirigidos')
                            ->getStateUsing(fn (Referee $record): int => $record->matches->count()),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Notas')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notas')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Información del Sistema')
                    ->schema([
                        Infolists\Components\TextEntry::make('creator.name')
                            ->label('Creado por'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2)
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
            'index' => Pages\ListReferees::route('/'),
            'create' => Pages\CreateReferee::route('/create'),
            'view' => Pages\ViewReferee::route('/{record}'),
            'edit' => Pages\EditReferee::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['Super Admin', 'Admin', 'League Admin']);
    }
}