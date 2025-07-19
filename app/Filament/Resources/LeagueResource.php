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

                Forms\Components\Section::make('Configuración')
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

                Forms\Components\Section::make('Medios')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->directory('leagues/logos'),
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

                Tables\Columns\TextColumn::make('country.name')
                    ->label('País')
                    ->sortable(),

                Tables\Columns\TextColumn::make('clubs_count')
                    ->label('Clubes')
                    ->counts('clubs')
                    ->badge(),

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
                            ->url(fn ($record) => $record->website)
                            ->openUrlInNewTab(),
                    ])->columns(3),

                Infolists\Components\Section::make('Estadísticas')
                    ->schema([
                        Infolists\Components\TextEntry::make('clubs_count')
                            ->label('Total de Clubes')
                            ->state(fn ($record) => $record->clubs()->count()),

                        Infolists\Components\TextEntry::make('players_count')
                            ->label('Total de Jugadoras')
                            ->state(fn ($record) => $record->clubs()->withCount('players')->get()->sum('players_count')),

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
            'index' => Pages\ListLeagues::route('/'),
            'create' => Pages\CreateLeague::route('/create'),
            'view' => Pages\ViewLeague::route('/{record}'),
            'edit' => Pages\EditLeague::route('/{record}/edit'),
        ];
    }
}
