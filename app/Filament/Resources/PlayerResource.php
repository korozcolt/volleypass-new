<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerResource\Pages;
use App\Models\Player;
use App\Models\User;
use App\Models\Club;
use App\Models\Payment;
use App\Enums\PlayerPosition;
use App\Enums\PlayerCategory;
use App\Enums\MedicalStatus;
use App\Enums\UserStatus;
use App\Enums\FederationStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\Tabs::make('Información de Jugadora')
                    ->tabs([
                        // TAB 1: INFORMACIÓN PERSONAL
                        Forms\Components\Tabs\Tab::make('Información Personal')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Section::make('Datos Básicos')
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->label('Usuario')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('document_number')
                                                    ->required()
                                                    ->maxLength(20),
                                            ]),

                                        Forms\Components\Select::make('current_club_id')
                                            ->label('Club Actual')
                                            ->relationship('currentClub', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        Forms\Components\TextInput::make('jersey_number')
                                            ->label('Número de Camiseta')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(99),
                                    ])
                                    ->columns(3),

                                Forms\Components\Section::make('Información Deportiva')
                                    ->schema([
                                        Forms\Components\Select::make('position')
                                            ->label('Posición')
                                            ->options(PlayerPosition::class)
                                            ->required(),

                                        Forms\Components\Select::make('category')
                                            ->label('Categoría')
                                            ->options(PlayerCategory::class)
                                            ->required(),

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

                                        Forms\Components\Select::make('dominant_hand')
                                            ->label('Mano Dominante')
                                            ->options([
                                                'right' => 'Derecha',
                                                'left' => 'Izquierda',
                                                'both' => 'Ambidiestra',
                                            ]),

                                        Forms\Components\DatePicker::make('debut_date')
                                            ->label('Fecha de Debut'),
                                    ])
                                    ->columns(3),
                            ]),

                        // TAB 2: FEDERACIÓN (PRINCIPAL)
                        Forms\Components\Tabs\Tab::make('Federación')
                            ->icon('heroicon-o-check-badge')
                            ->badge(fn($record) => $record?->federation_status?->getLabel())
                            ->badgeColor(fn($record) => $record?->federation_status?->getColor() ?? 'gray')
                            ->schema([
                                Forms\Components\Section::make('Estado de Federación')
                                    ->description('Gestión del estado federativo de la jugadora')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('federation_status')
                                                    ->label('Estado de Federación')
                                                    ->options(FederationStatus::class)
                                                    ->default(FederationStatus::NotFederated)
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                        if ($state === FederationStatus::Federated->value) {
                                                            $set('federation_date', now()->format('Y-m-d'));
                                                            $set('federation_expires_at', now()->addYear()->format('Y-m-d'));
                                                        }
                                                    }),

                                                Forms\Components\DatePicker::make('federation_date')
                                                    ->label('Fecha de Federación')
                                                    ->visible(
                                                        fn(Forms\Get $get) =>
                                                        in_array($get('federation_status'), [
                                                            FederationStatus::Federated->value,
                                                            FederationStatus::Expired->value
                                                        ])
                                                    ),

                                                Forms\Components\DatePicker::make('federation_expires_at')
                                                    ->label('Fecha de Vencimiento')
                                                    ->visible(
                                                        fn(Forms\Get $get) =>
                                                        in_array($get('federation_status'), [
                                                            FederationStatus::Federated->value,
                                                            FederationStatus::Expired->value
                                                        ])
                                                    ),
                                            ]),

                                        Forms\Components\Select::make('federation_payment_id')
                                            ->label('Pago de Federación')
                                            ->relationship(
                                                'federationPayment',
                                                'reference_number',
                                                fn(Builder $query) => $query->where('type', PaymentType::Federation)
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->visible(
                                                fn(Forms\Get $get) =>
                                                $get('federation_status') === FederationStatus::Federated->value
                                            ),

                                        Forms\Components\Textarea::make('federation_notes')
                                            ->label('Notas de Federación')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Acciones de Federación')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('federate')
                                                ->label('Federar Jugadora')
                                                ->icon('heroicon-o-check-badge')
                                                ->color('success')
                                                ->visible(
                                                    fn($record) =>
                                                    $record && !$record->isFederated()
                                                )
                                                ->requiresConfirmation()
                                                ->form([
                                                    Forms\Components\Select::make('payment_id')
                                                        ->label('Seleccionar Pago')
                                                        ->options(function ($record) {
                                                            return Payment::where('club_id', $record->current_club_id)
                                                                ->where('type', PaymentType::Federation)
                                                                ->where('status', PaymentStatus::Verified)
                                                                ->pluck('reference_number', 'id');
                                                        })
                                                        ->required(),
                                                ])
                                                ->action(function ($record, array $data) {
                                                    $payment = Payment::find($data['payment_id']);
                                                    $record->federateWithPayment($payment);
                                                }),

                                            Forms\Components\Actions\Action::make('suspend')
                                                ->label('Suspender Federación')
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->color('warning')
                                                ->visible(
                                                    fn($record) =>
                                                    $record && $record->isFederated()
                                                )
                                                ->requiresConfirmation()
                                                ->form([
                                                    Forms\Components\Textarea::make('reason')
                                                        ->label('Motivo de Suspensión')
                                                        ->required(),
                                                ])
                                                ->action(function ($record, array $data) {
                                                    $record->updateFederationStatus(
                                                        FederationStatus::Suspended,
                                                        $data['reason']
                                                    );
                                                }),
                                        ])
                                    ])
                                    ->visible(fn($record) => $record !== null),
                            ]),

                        // TAB 3: ESTADO MÉDICO
                        Forms\Components\Tabs\Tab::make('Estado Médico')
                            ->icon('heroicon-o-heart')
                            ->badge(fn($record) => $record?->medical_status?->getLabel())
                            ->badgeColor(fn($record) => $record?->medical_status?->getColor() ?? 'gray')
                            ->schema([
                                Forms\Components\Section::make('Estado Médico Actual')
                                    ->schema([
                                        Forms\Components\Select::make('medical_status')
                                            ->label('Estado Médico')
                                            ->options(MedicalStatus::class)
                                            ->default(MedicalStatus::Fit)
                                            ->required(),

                                        Forms\Components\Toggle::make('is_eligible')
                                            ->label('Elegible para Jugar')
                                            ->default(false),

                                        Forms\Components\DatePicker::make('eligibility_checked_at')
                                            ->label('Fecha de Verificación'),

                                        Forms\Components\Select::make('eligibility_checked_by')
                                            ->label('Verificado por')
                                            ->relationship('eligibilityChecker', 'name')
                                            ->searchable(),
                                    ])
                                    ->columns(2),
                            ]),

                        // TAB 4: DOCUMENTOS
                        Forms\Components\Tabs\Tab::make('Documentos')
                            ->icon('heroicon-o-document-text')
                            ->badge(fn($record) => $record?->documents?->count() ?? 0)
                            ->badgeColor('primary')
                            ->schema([
                                Forms\Components\Section::make('Gestión de Documentos')
                                    ->description('Administra los documentos requeridos para la jugadora')
                                    ->schema([
                                        Forms\Components\Repeater::make('documents')
                                            ->relationship('documents')
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Select::make('document_type')
                                                            ->label('Tipo de Documento')
                                                            ->options(\App\Enums\DocumentType::class)
                                                            ->required(),

                                                        Forms\Components\FileUpload::make('file_path')
                                                            ->label('Archivo')
                                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                                            ->maxSize(5120) // 5MB
                                                            ->required(),

                                                        Forms\Components\Select::make('status')
                                                            ->label('Estado')
                                                            ->options(\App\Enums\DocumentStatus::class)
                                                            ->default('pending')
                                                            ->required(),
                                                    ]),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\DatePicker::make('issued_date')
                                                            ->label('Fecha de Emisión'),

                                                        Forms\Components\DatePicker::make('expires_at')
                                                            ->label('Fecha de Vencimiento'),
                                                    ]),

                                                Forms\Components\Textarea::make('review_notes')
                                                    ->label('Notas de Revisión')
                                                    ->rows(2)
                                                    ->columnSpanFull(),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['document_type'] ?? 'Nuevo Documento')
                                            ->addActionLabel('Agregar Documento')
                                            ->reorderableWithButtons()
                                            ->cloneable(),
                                    ]),

                                Forms\Components\Section::make('Acciones de Documentos')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('approve_all')
                                                ->label('Aprobar Todos')
                                                ->icon('heroicon-o-check-circle')
                                                ->color('success')
                                                ->visible(fn($record) => $record && $record->documents()->where('status', 'pending')->exists())
                                                ->requiresConfirmation()
                                                ->action(function ($record) {
                                                    $record->documents()->where('status', 'pending')->update(['status' => 'approved']);
                                                }),

                                            Forms\Components\Actions\Action::make('request_missing')
                                                ->label('Solicitar Faltantes')
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->color('warning')
                                                ->visible(fn($record) => $record !== null)
                                                ->action(function ($record) {
                                                    // Lógica para solicitar documentos faltantes
                                                }),
                                        ])
                                    ])
                                    ->visible(fn($record) => $record !== null),
                            ]),

                        // TAB 5: HISTORIAL
                        Forms\Components\Tabs\Tab::make('Historial')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Forms\Components\Section::make('Historial de Actividades')
                                    ->description('Registro completo de cambios y actividades de la jugadora')
                                    ->schema([
                                        Forms\Components\Placeholder::make('activity_log')
                                            ->label('')
                                            ->content(function ($record) {
                                                if (!$record) {
                                                    return 'No hay historial disponible para jugadoras nuevas.';
                                                }

                                                $activities = $record->activities()
                                                    ->with('causer')
                                                    ->latest()
                                                    ->limit(50)
                                                    ->get();

                                                if ($activities->isEmpty()) {
                                                    return 'No se han registrado actividades para esta jugadora.';
                                                }

                                                $html = '<div class="space-y-3">';
                                                foreach ($activities as $activity) {
                                                    $date = $activity->created_at->format('d/m/Y H:i');
                                                    $user = $activity->causer?->name ?? 'Sistema';
                                                    $description = $activity->description;
                                                    
                                                    $changes = '';
                                                    if ($activity->properties->has('attributes') && $activity->properties->has('old')) {
                                                        $attributes = $activity->properties->get('attributes');
                                                        $old = $activity->properties->get('old');
                                                        
                                                        $changesList = [];
                                                        foreach ($attributes as $key => $newValue) {
                                                            if (isset($old[$key]) && $old[$key] != $newValue) {
                                                                $changesList[] = "<strong>{$key}:</strong> {$old[$key]} → {$newValue}";
                                                            }
                                                        }
                                                        
                                                        if (!empty($changesList)) {
                                                            $changes = '<div class="mt-1 text-sm text-gray-600"><strong>Cambios:</strong> ' . implode(', ', $changesList) . '</div>';
                                                        }
                                                    }
                                                    
                                                    $html .= '
                                                        <div class="border-l-4 border-blue-400 bg-blue-50 p-3 rounded-r">
                                                            <div class="flex justify-between items-start">
                                                                <div class="font-medium text-blue-900">' . $description . '</div>
                                                                <div class="text-xs text-blue-600">' . $date . '</div>
                                                            </div>
                                                            <div class="text-sm text-blue-700 mt-1">Por: ' . $user . '</div>
                                                            ' . $changes . '
                                                        </div>
                                                    ';
                                                }
                                                $html .= '</div>';
                                                
                                                return new \Illuminate\Support\HtmlString($html);
                                            })
                                    ]),

                                Forms\Components\Section::make('Historial de Transferencias')
                                    ->schema([
                                        Forms\Components\Placeholder::make('transfers_history')
                                            ->label('')
                                            ->content(function ($record) {
                                                if (!$record) {
                                                    return 'No hay historial de transferencias disponible.';
                                                }

                                                $transfers = $record->transfers()
                                                    ->with(['fromClub', 'toClub', 'requestedBy', 'approvedBy'])
                                                    ->latest()
                                                    ->get();

                                                if ($transfers->isEmpty()) {
                                                    return 'No se han registrado transferencias para esta jugadora.';
                                                }

                                                $html = '<div class="space-y-3">';
                                                foreach ($transfers as $transfer) {
                                                    $date = $transfer->transfer_date->format('d/m/Y');
                                                    $status = $transfer->status->getLabel();
                                                    $statusColor = $transfer->status->getColor();
                                                    $fromClub = $transfer->fromClub?->name ?? 'Club anterior';
                                                    $toClub = $transfer->toClub?->name ?? 'Club destino';
                                                    
                                                    $html .= '
                                                        <div class="border border-gray-200 bg-gray-50 p-3 rounded">
                                                            <div class="flex justify-between items-start">
                                                                <div class="font-medium">Transferencia: ' . $fromClub . ' → ' . $toClub . '</div>
                                                                <span class="px-2 py-1 text-xs rounded-full bg-' . $statusColor . '-100 text-' . $statusColor . '-800">' . $status . '</span>
                                                            </div>
                                                            <div class="text-sm text-gray-600 mt-1">Fecha: ' . $date . '</div>
                                                            ' . ($transfer->reason ? '<div class="text-sm text-gray-600">Motivo: ' . $transfer->reason . '</div>' : '') . '
                                                        </div>
                                                    ';
                                                }
                                                $html .= '</div>';
                                                
                                                return new \Illuminate\Support\HtmlString($html);
                                            })
                                    ])
                                    ->visible(fn($record) => $record && $record->transfers()->exists()),
                            ]),

                        // TAB 6: ESTADO GENERAL
                        Forms\Components\Tabs\Tab::make('Estado General')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('Estado y Configuración')
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('Estado')
                                            ->options(UserStatus::class)
                                            ->default(UserStatus::Active)
                                            ->required(),

                                        Forms\Components\DatePicker::make('retirement_date')
                                            ->label('Fecha de Retiro')
                                            ->visible(
                                                fn(Forms\Get $get) =>
                                                $get('status') === UserStatus::Inactive->value
                                            ),

                                        Forms\Components\Textarea::make('notes')
                                            ->label('Notas Generales')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
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
                            foreach ($records as $record) {
                                if (!$record->isFederated()) {
                                    $record->federateWithPayment($payment);
                                }
                            }
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
}
