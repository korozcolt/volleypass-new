<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use App\Models\Club;
use App\Models\Player;
use App\Enums\PlayerPosition;
use App\Rules\NoAccentsEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ManagePlayers extends ManageRelatedRecords
{
    protected static string $resource = ClubResource::class;

    protected static string $relationship = 'players';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Jugadoras';
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Auth::user()->can('view_club', $ownerRecord);
    }

    /**
     * @return array<string, mixed>
     */
    public static function getDefaultProperties(): array
    {
        return [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('document_number')
                                    ->label('Número de Documento')
                                    ->required()
                                    ->unique(Player::class, 'document_number', ignoreRecord: true),

                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('Fecha de Nacimiento')
                                    ->required()
                                    ->maxDate(now()->subYears(12)),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->rules([new NoAccentsEmail()])
                                    ->unique(Player::class, 'email', ignoreRecord: true),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel(),

                                Forms\Components\Select::make('gender')
                                    ->label('Género')
                                    ->options([
                                        'female' => 'Femenino',
                                        'male' => 'Masculino',
                                    ])
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('Información del Club')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('join_date')
                                    ->label('Fecha de Ingreso')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\Select::make('position')
                                    ->label('Posición')
                                    ->options(PlayerPosition::class),

                                Forms\Components\TextInput::make('jersey_number')
                                    ->label('Número de Camiseta')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(99),

                                Forms\Components\Toggle::make('is_captain')
                                    ->label('Es Capitana')
                                    ->default(false),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('document_number')
                    ->label('Documento')
                    ->searchable(),

                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Fecha de Nacimiento')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label('Posición')
                    ->badge(),

                Tables\Columns\TextColumn::make('jersey_number')
                    ->label('Número')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_captain')
                    ->label('Capitana')
                    ->boolean(),

                Tables\Columns\TextColumn::make('join_date')
                    ->label('Fecha de Ingreso')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('position')
                    ->label('Posición')
                    ->options(PlayerPosition::class),

                Tables\Filters\Filter::make('is_captain')
                    ->label('Solo Capitanas')
                    ->query(fn (Builder $query): Builder => $query->where('is_captain', true)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nueva Jugadora'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionadas'),
                ]),
            ]);
    }
}