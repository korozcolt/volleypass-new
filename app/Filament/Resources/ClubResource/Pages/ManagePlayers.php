<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use App\Models\Club;
use App\Models\Player;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ManagePlayers extends ManageRelatedRecords
{
    protected static string $resource = ClubResource::class;

    protected static string $relationship = 'players';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Jugadoras';
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
                                    ->email()
                                    ->unique(Player::class, 'email', ignoreRecord: true),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel(),

                                Forms\Components\Select::make('position')
                                    ->label('Posición')
                                    ->options([
                                        'setter' => 'Armadora',
                                        'outside_hitter' => 'Atacante Exterior',
                                        'middle_blocker' => 'Central',
                                        'opposite' => 'Opuesta',
                                        'libero' => 'Líbero',
                                        'defensive_specialist' => 'Especialista Defensiva',
                                    ]),
                            ]),
                    ]),

                Forms\Components\Section::make('Información del Club')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('joined_at')
                                    ->label('Fecha de Ingreso')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'active' => 'Activa',
                                        'inactive' => 'Inactiva',
                                        'suspended' => 'Suspendida',
                                        'transferred' => 'Transferida',
                                    ])
                                    ->default('active')
                                    ->required(),

                                Forms\Components\Toggle::make('is_captain')
                                    ->label('Es Capitana')
                                    ->default(false),

                                Forms\Components\Toggle::make('is_vice_captain')
                                    ->label('Es Vice-capitana')
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

                Tables\Columns\TextColumn::make('position')
                    ->label('Posición')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'setter' => 'Armadora',
                        'outside_hitter' => 'Atacante Exterior',
                        'middle_blocker' => 'Central',
                        'opposite' => 'Opuesta',
                        'libero' => 'Líbero',
                        'defensive_specialist' => 'Especialista Defensiva',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_captain')
                    ->label('Capitana')
                    ->boolean(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                        'transferred' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('joined_at')
                    ->label('Ingreso')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('federation_status')
                    ->label('Federación')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'federated' => 'success',
                        'pending' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activa',
                        'inactive' => 'Inactiva',
                        'suspended' => 'Suspendida',
                        'transferred' => 'Transferida',
                    ]),

                Tables\Filters\SelectFilter::make('position')
                    ->label('Posición')
                    ->options([
                        'setter' => 'Armadora',
                        'outside_hitter' => 'Atacante Exterior',
                        'middle_blocker' => 'Central',
                        'opposite' => 'Opuesta',
                        'libero' => 'Líbero',
                        'defensive_specialist' => 'Especialista Defensiva',
                    ]),

                Tables\Filters\TernaryFilter::make('is_captain')
                    ->label('Es Capitana'),

                Tables\Filters\SelectFilter::make('federation_status')
                    ->label('Estado de Federación')
                    ->options([
                        'federated' => 'Federada',
                        'pending' => 'Pendiente',
                        'expired' => 'Expirada',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['current_club_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
                Tables\Actions\Action::make('import_players')
                    ->label('Importar Jugadoras')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Archivo Excel/CSV')
                            ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // Lógica de importación
                        \Filament\Notifications\Notification::make()
                            ->title('Importación iniciada')
                            ->body('Se está procesando el archivo de jugadoras')
                            ->info()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('generate_card')
                    ->label('Generar Carnet')
                    ->icon('heroicon-o-identification')
                    ->color('success')
                    ->action(function (Player $record) {
                        // Lógica para generar carnet individual
                        \Filament\Notifications\Notification::make()
                            ->title('Carnet generado')
                            ->body("Se ha generado el carnet para {$record->name}")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('generate_cards')
                        ->label('Generar Carnets')
                        ->icon('heroicon-o-identification')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            // Lógica para generar carnets masivos
                            \Filament\Notifications\Notification::make()
                                ->title('Carnets generados')
                                ->body("Se han generado {$records->count()} carnets")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('federate')
                        ->label('Federar Jugadoras')
                        ->icon('heroicon-o-check-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            // Lógica para federar jugadoras
                            \Filament\Notifications\Notification::make()
                                ->title('Jugadoras federadas')
                                ->body("Se han federado {$records->count()} jugadoras")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('current_club_id', $this->getOwnerRecord()->id))
            ->defaultSort('name');
    }
}