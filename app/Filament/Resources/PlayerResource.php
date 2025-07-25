<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerResource\Pages;
use App\Filament\Resources\PlayerResource\RelationManagers;
use App\Models\Player;
use App\Models\User;
use App\Models\Club;
use App\Models\League;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use App\Models\Payment;
use App\Enums\PlayerPosition;
use App\Enums\PlayerCategory;
use App\Enums\MedicalStatus;
use App\Enums\UserStatus;
use App\Enums\FederationStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\Gender;
use App\Services\FederationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Forms\Get;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Jugadoras';

    protected static ?string $modelLabel = 'Jugadora';

    protected static ?string $pluralModelLabel = 'Jugadoras';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Gestión de Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Información de la Jugadora')
                    ->tabs([
                        // TAB 1: DATOS PERSONALES
                        Tabs\Tab::make('Datos Personales')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Información Personal')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('user.first_name')
                                                    ->label('Nombres')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->rules(['regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/']),
                                                
                                                Forms\Components\TextInput::make('user.last_name')
                                                    ->label('Apellidos')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->rules(['regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/']),
                                            ]),
                                        
                                        Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('user.document_type')
                                                    ->label('Tipo de Documento')
                                                    ->options([
                                                        'CC' => 'Cédula de Ciudadanía',
                                                        'TI' => 'Tarjeta de Identidad',
                                                        'CE' => 'Cédula de Extranjería',
                                                        'PP' => 'Pasaporte',
                                                        'RC' => 'Registro Civil'
                                                    ])
                                                    ->required()
                                                    ->reactive(),
                                                
                                                Forms\Components\TextInput::make('user.document_number')
                                                    ->label('Número de Documento')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(20)
                                                    ->rules(['regex:/^[0-9]+$/']),
                                                
                                                Forms\Components\DatePicker::make('user.birth_date')
                                                    ->label('Fecha de Nacimiento')
                                                    ->required()
                                                    ->maxDate(now()->subYears(8))
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        if ($state) {
                                                            $age = now()->diffInYears($state);
                                                            $category = self::calculateCategory($age);
                                                            $set('category', $category);
                                                        }
                                                    }),
                                            ]),
                                        
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('user.gender')
                                                    ->label('Género')
                                                    ->options(Gender::class)
                                                    ->required(),
                                                
                                                Forms\Components\Select::make('category')
                                                    ->label('Categoría')
                                                    ->options(PlayerCategory::class)
                                                    ->required()
                                                    ->disabled()
                                                    ->dehydrated(),
                                            ]),
                                    ]),
                                
                                Section::make('Información de Contacto')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('user.email')
                                                    ->label('Correo Electrónico')
                                                    ->email()
                                                    ->required()
                                                    ->unique(ignoreRecord: true),
                                                
                                                Forms\Components\TextInput::make('user.phone')
                                                    ->label('Teléfono Principal')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(15),
                                            ]),
                                        
                                        Forms\Components\TextInput::make('user.phone_secondary')
                                            ->label('Teléfono Secundario')
                                            ->tel()
                                            ->maxLength(15),
                                        
                                        Forms\Components\Textarea::make('user.address')
                                            ->label('Dirección')
                                            ->maxLength(255)
                                            ->rows(2),
                                    ]),
                                
                                Section::make('Ubicación Geográfica')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('user.country_id')
                                                    ->label('País')
                                                    ->relationship('user.country', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->reactive(),
                                                
                                                Forms\Components\Select::make('user.department_id')
                                                    ->label('Departamento')
                                                    ->relationship('user.department', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->reactive(),
                                                
                                                Forms\Components\Select::make('user.city_id')
                                                    ->label('Ciudad')
                                                    ->relationship('user.city', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),
                                            ]),
                                    ]),
                                
                                Section::make('Información Deportiva')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('position')
                                                    ->label('Posición')
                                                    ->options(PlayerPosition::class)
                                                    ->required(),
                                                
                                                Forms\Components\TextInput::make('jersey_number')
                                                    ->label('Número de Camiseta')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->maxValue(99),
                                                
                                                Forms\Components\Select::make('dominant_hand')
                                                    ->label('Mano Dominante')
                                                    ->options([
                                                        'right' => 'Derecha',
                                                        'left' => 'Izquierda',
                                                        'both' => 'Ambidiestra',
                                                    ]),
                                            ]),
                                        
                                        Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('height')
                                                    ->label('Altura (m)')
                                                    ->numeric()
                                                    ->step(0.01)
                                                    ->minValue(1.40)
                                                    ->maxValue(2.20),
                                                
                                                Forms\Components\TextInput::make('weight')
                                                    ->label('Peso (kg)')
                                                    ->numeric()
                                                    ->step(0.1)
                                                    ->minValue(40)
                                                    ->maxValue(120),
                                                
                                                Forms\Components\DatePicker::make('debut_date')
                                                    ->label('Fecha de Debut'),
                                            ]),
                                    ]),
                            ]),

                        // TAB 2: FEDERACIÓN
                        Tabs\Tab::make('Federación')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Section::make('Información Federativa')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('user.league_id')
                                                    ->label('Liga')
                                                    ->relationship('user.league', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        $set('user.club_id', null);
                                                    }),
                                                
                                                Forms\Components\Select::make('user.club_id')
                                                    ->label('Club')
                                                    ->relationship(
                                                        'user.club',
                                                        'name',
                                                        fn (Builder $query, callable $get) => $query->where('league_id', $get('user.league_id'))
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->disabled(fn (callable $get) => !$get('user.league_id')),
                                            ]),
                                        
                                        Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('federation_number')
                                                    ->label('Número de Federación')
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(20)
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->helperText('Se genera automáticamente al federar'),
                                                
                                                Forms\Components\DatePicker::make('federation_date')
                                                    ->label('Fecha de Federación')
                                                    ->disabled()
                                                    ->dehydrated(),
                                                
                                                Forms\Components\Select::make('user.status')
                                                    ->label('Estado')
                                                    ->options(UserStatus::class)
                                                    ->required()
                                                    ->default(UserStatus::Active),
                                            ]),
                                        
                                        Forms\Components\Select::make('federation_status')
                                            ->label('Estado de Federación')
                                            ->options(FederationStatus::class)
                                            ->required()
                                            ->default(FederationStatus::NotFederated)
                                            ->disabled()
                                            ->dehydrated()
                                            ->helperText('Se actualiza automáticamente según el proceso de federación'),
                                    ]),
                                
                                Section::make('Estado Médico')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('medical_status')
                                                    ->label('Estado Médico')
                                                    ->options(MedicalStatus::class)
                                                    ->required()
                                                    ->default(MedicalStatus::Fit),
                                                
                                                Forms\Components\DatePicker::make('medical_certificate_expiry')
                                                    ->label('Vencimiento Certificado Médico')
                                                    ->minDate(now())
                                                    ->required(fn (callable $get) => $get('medical_status') === MedicalStatus::Fit->value),
                                            ]),
                                        
                                        Forms\Components\Textarea::make('medical_observations')
                                             ->label('Observaciones Médicas')
                                             ->maxLength(500)
                                             ->rows(3),
                                    ]),
                             ]),

                        // TAB 3: DOCUMENTOS
                        Tabs\Tab::make('Documentos')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Documentos Requeridos')
                                    ->schema([
                                        Forms\Components\FileUpload::make('identity_document')
                                            ->label('Documento de Identidad')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->maxSize(5120)
                                            ->directory('documents/identity')
                                            ->visibility('private')
                                            ->required(),
                                        
                                        Forms\Components\FileUpload::make('medical_certificate')
                                            ->label('Certificado Médico')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->maxSize(5120)
                                            ->directory('documents/medical')
                                            ->visibility('private')
                                            ->required(),
                                        
                                        Forms\Components\FileUpload::make('photo')
                                            ->label('Fotografía')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                            ->maxSize(2048)
                                            ->directory('documents/photos')
                                            ->visibility('private')
                                            ->image()
                                            ->imageEditor()
                                            ->required(),
                                    ]),
                                
                                Section::make('Documentos Adicionales')
                                    ->schema([
                                        Forms\Components\FileUpload::make('additional_documents')
                                            ->label('Documentos Adicionales')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->maxSize(5120)
                                            ->directory('documents/additional')
                                            ->visibility('private')
                                            ->multiple()
                                            ->maxFiles(5),
                                        
                                        Forms\Components\Textarea::make('document_notes')
                                            ->label('Notas sobre Documentos')
                                            ->maxLength(500)
                                            ->rows(3),
                                    ]),
                            ]),

                        // TAB 4: HISTORIAL
                        Tabs\Tab::make('Historial')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make('Actividad Reciente')
                                    ->schema([
                                        Forms\Components\Placeholder::make('activity_log')
                                            ->label('')
                                            ->content(function ($record) {
                                                if (!$record) return 'No hay actividad registrada.';
                                                
                                                return view('filament.components.activity-log', [
                                                    'activities' => $record->activities()->latest()->take(10)->get()
                                                ]);
                                            }),
                                    ]),
                                
                                Section::make('Historial de Transferencias')
                                    ->schema([
                                        Forms\Components\Placeholder::make('transfer_history')
                                            ->label('')
                                            ->content(function ($record) {
                                                if (!$record) return 'No hay transferencias registradas.';
                                                
                                                return view('filament.components.transfer-history', [
                                                    'transfers' => $record->transfers()->latest()->get()
                                                ]);
                                            }),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Nombre Completo')
                    ->searchable(['users.first_name', 'users.last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.document_number')
                    ->label('Documento')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('currentClub.name')
                    ->label('Club')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jersey_number')
                    ->label('#')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label('Posición')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->toggleable(),

                // COLUMNA PRINCIPAL: ESTADO DE FEDERACIÓN
                Tables\Columns\TextColumn::make('federation_status')
                    ->label('Federación')
                    ->badge()
                    ->color(fn($record) => $record?->federation_status?->getColor() ?? 'gray')
                    ->icon(fn($record) => $record?->federation_status?->getIcon() ?? 'heroicon-o-minus-circle')
                    ->sortable(),

                Tables\Columns\TextColumn::make('federation_expires_at')
                    ->label('Vence')
                    ->date('d/m/Y')
                    ->color(
                        fn($record) =>
                        $record && $record->federation_expires_at && $record->federation_expires_at->isPast()
                            ? 'danger'
                            : 'success'
                    )
                    ->placeholder('No aplica')
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('medical_status')
                    ->label('Estado Médico')
                    ->badge()
                    ->color(fn($record) => $record?->medical_status?->getColor() ?? 'gray')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_eligible')
                    ->label('Elegible')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrada')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('federation_status')
                    ->label('Estado de Federación')
                    ->options(FederationStatus::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('current_club_id')
                    ->label('Club')
                    ->relationship('currentClub', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('position')
                    ->label('Posición')
                    ->options(PlayerPosition::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(PlayerCategory::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('medical_status')
                    ->label('Estado Médico')
                    ->options(MedicalStatus::class)
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_eligible')
                    ->label('Elegible para Jugar'),

                Tables\Filters\Filter::make('federation_expires_soon')
                    ->label('Federación por Vencer')
                    ->query(
                        fn(Builder $query) =>
                        $query->where('federation_expires_at', '<=', now()->addDays(30))
                            ->where('federation_expires_at', '>', now())
                    ),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('federate')
                    ->label('Federar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn($record) => !$record->isFederated())
                    ->url(fn($record) => PlayerResource::getUrl('edit', ['record' => $record, 'activeTab' => 'federacion'])),

                Tables\Actions\Action::make('view_card')
                    ->label('Ver Carnet')
                    ->icon('heroicon-o-identification')
                    ->color('info')
                    ->visible(fn($record) => $record->current_card !== null)
                    ->url(fn($record) => route('player.card', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('bulk_federate')
                        ->label('Federar Seleccionadas')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Select::make('club_id')
                                ->label('Club')
                                ->relationship('currentClub', 'name')
                                ->required(),
                            Forms\Components\Select::make('payment_id')
                                ->label('Pago de Federación')
                                ->options(
                                    fn(Forms\Get $get) =>
                                    Payment::where('club_id', $get('club_id'))
                                        ->where('type', PaymentType::Federation)
                                        ->where('status', PaymentStatus::Verified)
                                        ->pluck('reference_number', 'id')
                                )
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $payment = Payment::find($data['payment_id']);
                            $federationService = app(FederationService::class);
                            $federatedCount = 0;
                            
                            foreach ($records as $record) {
                                if (!$record->isFederated()) {
                                    try {
                                        $federationService->federatePlayer($record, $payment);
                                        $federatedCount++;
                                    } catch (\Exception $e) {
                                        // Log error but continue
                                    }
                                }
                            }
                            
                            Notification::make()
                                ->title('Federación Masiva Completada')
                                ->body("Se federaron {$federatedCount} jugadoras exitosamente.")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPlayers::route('/'),
            'create' => Pages\CreatePlayer::route('/create'),
            'view' => Pages\ViewPlayer::route('/{record}'),
            'edit' => Pages\EditPlayer::route('/{record}/edit'),
            'manage-transfers' => Pages\ManageTransfers::route('/{record}/transfers'),
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
        return static::getModel()::count() > 100 ? 'warning' : 'primary';
    }

    /**
     * Calcular la categoría del jugador basado en la edad
     */
    private static function calculateCategory(int $age): PlayerCategory
    {
        return match (true) {
            $age >= 8 && $age <= 10 => PlayerCategory::Mini,
            $age >= 11 && $age <= 12 => PlayerCategory::Pre_Mini,
            $age >= 13 && $age <= 14 => PlayerCategory::Infantil,
            $age >= 15 && $age <= 16 => PlayerCategory::Cadete,
            $age >= 17 && $age <= 20 => PlayerCategory::Juvenil,
            $age >= 21 && $age <= 34 => PlayerCategory::Mayores,
            $age >= 35 => PlayerCategory::Masters,
            default => PlayerCategory::Mayores
        };
    }
}
