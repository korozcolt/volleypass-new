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
use Illuminate\Database\Eloquent\Builder;

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

                        Forms\Components\Select::make('league_id')
                            ->label('Liga')
                            ->relationship('league', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Dirección y Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255),

                        Forms\Components\Select::make('country_id')
                            ->label('País')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('department_id')
                            ->label('Departamento')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('city_id')
                            ->label('Ciudad')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('website')
                            ->label('Sitio Web')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(UserStatus::class)
                            ->default(UserStatus::Active)
                            ->required(),

                        Forms\Components\Select::make('director_id')
                            ->label('Director')
                            ->relationship('director', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('founded_date')
                            ->label('Fecha de Fundación'),

                        Forms\Components\TextInput::make('colors')
                            ->label('Colores del Club')
                            ->maxLength(100),
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

                Forms\Components\Section::make('Medios')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->directory('clubs/logos'),

                        Forms\Components\FileUpload::make('photos')
                            ->label('Fotos')
                            ->image()
                            ->multiple()
                            ->maxSize(2048)
                            ->directory('clubs/photos'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('short_name')
                    ->label('Nombre Corto')
                    ->searchable(),

                Tables\Columns\TextColumn::make('league.name')
                    ->label('Liga')
                    ->sortable(),

                Tables\Columns\TextColumn::make('director.name')
                    ->label('Director')
                    ->sortable(),

                Tables\Columns\TextColumn::make('players_count')
                    ->label('Jugadoras')
                    ->counts('players')
                    ->badge(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ciudad')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('founded_date')
                    ->label('Fundado')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(UserStatus::class),

                Tables\Filters\SelectFilter::make('league')
                    ->label('Liga')
                    ->relationship('league', 'name'),

                Tables\Filters\SelectFilter::make('city')
                    ->label('Ciudad')
                    ->relationship('city', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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

                        Infolists\Components\TextEntry::make('founded_date')
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
        ];
    }
}
