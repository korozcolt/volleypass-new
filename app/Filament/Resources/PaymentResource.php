<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\User;
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

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Pagos';
    protected static ?string $modelLabel = 'Pago';
    protected static ?string $pluralModelLabel = 'Pagos';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Pago')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label('Tipo de Pago')
                            ->options(PaymentType::class)
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Monto')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        Forms\Components\TextInput::make('currency')
                            ->label('Moneda')
                            ->default('COP')
                            ->maxLength(3)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Estado y Fechas')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(PaymentStatus::class)
                            ->default(PaymentStatus::Pending)
                            ->required(),

                        Forms\Components\DateTimePicker::make('payment_date')
                            ->label('Fecha de Pago'),

                        Forms\Components\DateTimePicker::make('due_date')
                            ->label('Fecha de Vencimiento'),

                        Forms\Components\DateTimePicker::make('confirmed_at')
                            ->label('Fecha de Confirmación'),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles de Transacción')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('ID de Transacción')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('reference')
                            ->label('Referencia')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('Método de Pago')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('gateway')
                            ->label('Pasarela de Pago')
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3),

                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor'),
                    ]),

                Forms\Components\Section::make('Comprobantes')
                    ->schema([
                        Forms\Components\FileUpload::make('receipt')
                            ->label('Comprobante de Pago')
                            ->multiple()
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->maxSize(5120)
                            ->directory('payments/receipts'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Referencia')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método')
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Fecha de Pago')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Vencimiento')
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_receipt')
                    ->label('Comprobante')
                    ->boolean()
                    ->state(fn ($record) => !empty($record->receipt)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(PaymentStatus::class),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(PaymentType::class),

                Tables\Filters\Filter::make('payment_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === PaymentStatus::Pending)
                    ->action(function ($record) {
                        $record->update([
                            'status' => PaymentStatus::Paid,
                            'confirmed_at' => now(),
                        ]);
                    }),
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
                Infolists\Components\Section::make('Información del Pago')
                    ->schema([
                        Infolists\Components\TextEntry::make('reference')
                            ->label('Referencia'),

                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Usuario'),

                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipo')
                            ->badge(),

                        Infolists\Components\TextEntry::make('amount')
                            ->label('Monto')
                            ->money('COP'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),

                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Método de Pago'),
                    ])->columns(2),

                Infolists\Components\Section::make('Fechas')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_date')
                            ->label('Fecha de Pago')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('due_date')
                            ->label('Fecha de Vencimiento')
                            ->date(),

                        Infolists\Components\TextEntry::make('confirmed_at')
                            ->label('Fecha de Confirmación')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime(),
                    ])->columns(2),

                Infolists\Components\Section::make('Detalles de Transacción')
                    ->schema([
                        Infolists\Components\TextEntry::make('transaction_id')
                            ->label('ID de Transacción')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('gateway')
                            ->label('Pasarela de Pago'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),

                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notas'),
                    ])->columns(2),

                Infolists\Components\Section::make('Comprobantes')
                    ->schema([
                        Infolists\Components\TextEntry::make('receipt')
                            ->label('Comprobantes'),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
