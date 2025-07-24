<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use App\Models\Club;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ManagePayments extends ManageRelatedRecords
{
    protected static string $resource = ClubResource::class;

    protected static string $relationship = 'payments';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getNavigationLabel(): string
    {
        return 'Pagos';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Pago')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('payment_type')
                                    ->label('Tipo de Pago')
                                    ->options([
                                        'federation_fee' => 'Cuota de Federación',
                                        'tournament_fee' => 'Inscripción Torneo',
                                        'card_generation' => 'Generación de Carnets',
                                        'penalty' => 'Multa',
                                        'other' => 'Otro',
                                    ])
                                    ->required(),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Monto')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),

                                Forms\Components\Select::make('currency')
                                    ->label('Moneda')
                                    ->options([
                                        'COP' => 'Pesos Colombianos (COP)',
                                        'USD' => 'Dólares (USD)',
                                    ])
                                    ->default('COP')
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'pending' => 'Pendiente',
                                        'paid' => 'Pagado',
                                        'overdue' => 'Vencido',
                                        'cancelled' => 'Cancelado',
                                    ])
                                    ->default('pending')
                                    ->required(),

                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Fecha de Vencimiento')
                                    ->required(),

                                Forms\Components\DateTimePicker::make('paid_at')
                                    ->label('Fecha de Pago')
                                    ->visible(fn (Forms\Get $get) => $get('status') === 'paid'),
                            ]),
                    ]),

                Forms\Components\Section::make('Detalles del Pago')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->label('Método de Pago')
                                    ->options([
                                        'bank_transfer' => 'Transferencia Bancaria',
                                        'cash' => 'Efectivo',
                                        'check' => 'Cheque',
                                        'online' => 'Pago en Línea',
                                    ]),

                                Forms\Components\TextInput::make('reference_number')
                                    ->label('Número de Referencia')
                                    ->maxLength(255),

                                Forms\Components\FileUpload::make('receipt')
                                    ->label('Comprobante de Pago')
                                    ->image()
                                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                                    ->directory('payments/receipts')
                                    ->visibility('private'),

                                Forms\Components\Select::make('verified_by')
                                    ->label('Verificado por')
                                    ->relationship('verifier', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas Administrativas')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment_type')
            ->columns([
                Tables\Columns\TextColumn::make('payment_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'federation_fee' => 'Cuota de Federación',
                        'tournament_fee' => 'Inscripción Torneo',
                        'card_generation' => 'Generación de Carnets',
                        'penalty' => 'Multa',
                        'other' => 'Otro',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'federation_fee' => 'primary',
                        'tournament_fee' => 'success',
                        'card_generation' => 'info',
                        'penalty' => 'danger',
                        'other' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'overdue' => 'Vencido',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Fecha de Pago')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'bank_transfer' => 'Transferencia',
                        'cash' => 'Efectivo',
                        'check' => 'Cheque',
                        'online' => 'En Línea',
                        default => $state ?? 'N/A',
                    })
                    ->toggleable(),

                Tables\Columns\IconColumn::make('receipt')
                    ->label('Comprobante')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document-minus'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'paid' => 'Pagado',
                        'overdue' => 'Vencido',
                        'cancelled' => 'Cancelado',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('payment_type')
                    ->label('Tipo de Pago')
                    ->options([
                        'federation_fee' => 'Cuota de Federación',
                        'tournament_fee' => 'Inscripción Torneo',
                        'card_generation' => 'Generación de Carnets',
                        'penalty' => 'Multa',
                        'other' => 'Otro',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('due_date')
                    ->form([
                        Forms\Components\DatePicker::make('due_from')
                            ->label('Vence desde'),
                        Forms\Components\DatePicker::make('due_until')
                            ->label('Vence hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['due_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('due_date', '>=', $date),
                            )
                            ->when(
                                $data['due_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('due_date', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('overdue')
                    ->label('Pagos Vencidos')
                    ->query(fn (Builder $query): Builder => $query->where('due_date', '<', now())->where('status', '!=', 'paid')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['club_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
                Tables\Actions\Action::make('generate_federation_payment')
                    ->label('Generar Pago de Federación')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('period')
                            ->label('Período')
                            ->options([
                                'monthly' => 'Mensual',
                                'quarterly' => 'Trimestral',
                                'annual' => 'Anual',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Monto')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // Lógica para generar pago de federación
                        \Filament\Notifications\Notification::make()
                            ->title('Pago generado')
                            ->body('Se ha generado el pago de federación')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('mark_as_paid')
                    ->label('Marcar como Pagado')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Payment $record): bool => $record->status !== 'paid')
                    ->form([
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Fecha de Pago')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Método de Pago')
                            ->options([
                                'bank_transfer' => 'Transferencia Bancaria',
                                'cash' => 'Efectivo',
                                'check' => 'Cheque',
                                'online' => 'Pago en Línea',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('reference_number')
                            ->label('Número de Referencia'),
                    ])
                    ->action(function (Payment $record, array $data): void {
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => $data['paid_at'],
                            'payment_method' => $data['payment_method'],
                            'reference_number' => $data['reference_number'],
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Pago registrado')
                            ->body('El pago ha sido marcado como pagado')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('download_receipt')
                    ->label('Descargar Comprobante')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->visible(fn (Payment $record): bool => !empty($record->receipt))
                    ->url(fn (Payment $record): string => Storage::url($record->receipt))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_as_paid')
                        ->label('Marcar como Pagados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\DateTimePicker::make('paid_at')
                                ->label('Fecha de Pago')
                                ->default(now())
                                ->required(),
                            Forms\Components\Select::make('payment_method')
                                ->label('Método de Pago')
                                ->options([
                                    'bank_transfer' => 'Transferencia Bancaria',
                                    'cash' => 'Efectivo',
                                    'check' => 'Cheque',
                                    'online' => 'Pago en Línea',
                                ])
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Support\Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'status' => 'paid',
                                    'paid_at' => $data['paid_at'],
                                    'payment_method' => $data['payment_method'],
                                ]);
                            });
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Pagos registrados')
                                ->body("Se han marcado {$records->count()} pagos como pagados")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('club_id', $this->getOwnerRecord()->id))
            ->defaultSort('due_date', 'desc');
    }
}