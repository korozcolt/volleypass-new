<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Gestión Financiera';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Pagos';

    protected static ?string $modelLabel = 'Pago';

    protected static ?string $pluralModelLabel = 'Pagos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Pago')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label(__('Type'))
                            ->options(PaymentType::class)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('reference_number', 'PAY-' . strtoupper(uniqid()))),
                        
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options(PaymentStatus::class)
                            ->required()
                            ->default(PaymentStatus::Pending),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label(__('Amount'))
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('currency')
                            ->label(__('Currency'))
                            ->required()
                            ->default('USD')
                            ->maxLength(3),
                        
                        Forms\Components\TextInput::make('reference_number')
                            ->label(__('Reference number'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default('PAY-' . strtoupper(uniqid())),
                    ])->columns(2),
                
                Forms\Components\Section::make('Relaciones')
                    ->schema([
                        Forms\Components\Select::make('club_id')
                            ->label(__('Club'))
                            ->relationship('club', 'name')
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('league_id')
                            ->label(__('League'))
                            ->relationship('league', 'name')
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('user_id')
                            ->label(__('User'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('player_id')
                            ->label(__('Player'))
                            ->relationship('player', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name ?? 'Player #' . $record->id)
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Detalles del Pago')
                    ->schema([
                        Forms\Components\TextInput::make('payment_method')
                            ->label(__('Payment method'))
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('transaction_id')
                            ->label(__('Transaction ID'))
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('gateway')
                            ->label(__('Gateway'))
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('month_year')
                            ->label(__('Month/Year'))
                            ->placeholder('YYYY-MM')
                            ->maxLength(7),
                        
                        Forms\Components\Toggle::make('is_recurring')
                            ->label(__('Recurring payment')),
                        
                        Forms\Components\FileUpload::make('receipt_url')
                            ->label(__('Receipt'))
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->directory('payment-receipts'),
                        
                        Forms\Components\FileUpload::make('payment_proof')
                            ->label(__('Payment proof'))
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->directory('payment-proofs'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('payment_date')
                            ->label(__('Payment date'))
                            ->default(now()),
                        
                        Forms\Components\DateTimePicker::make('due_date')
                            ->label(__('Due date')),
                        
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label(__('Paid at')),
                        
                        Forms\Components\DateTimePicker::make('verified_at')
                            ->label(__('Verified at')),
                        
                        Forms\Components\DateTimePicker::make('confirmed_at')
                            ->label(__('Confirmed at')),
                    ])->columns(2),
                
                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        
                        Forms\Components\KeyValue::make('metadata')
                            ->label(__('Metadata'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Referencia')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (PaymentType $state): string => match ($state) {
                        PaymentType::Federation => 'info',
                        PaymentType::Registration => 'success',
                        PaymentType::Tournament => 'warning',
                        PaymentType::Transfer => 'danger',
                        PaymentType::Fine => 'gray',
                        PaymentType::MonthlyFee => 'primary',
                        PaymentType::ClubToLeague => 'secondary',
                        PaymentType::PlayerToClub => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => match ($state) {
                        PaymentStatus::Pending => 'warning',
                        PaymentStatus::Verified => 'success',
                        PaymentStatus::Rejected => 'danger',
                        PaymentStatus::Paid => 'success',
                        PaymentStatus::Overdue => 'danger',
                        PaymentStatus::Cancelled => 'gray',
                        PaymentStatus::Refunded => 'info',
                        PaymentStatus::UnderVerification => 'warning',
                        PaymentStatus::Completed => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD')
                    ->sortable()
                    ->weight(FontWeight::Bold),
                
                Tables\Columns\TextColumn::make('club.name')
                    ->label('Club')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('player.user.name')
                    ->label('Jugadora')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('league.name')
                    ->label('Liga')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('month_year')
                    ->label('Mes/Año')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_recurring')
                    ->label('Recurrente')
                    ->boolean()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Fecha de Pago')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Fecha de Vencimiento')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() && !in_array($record->status, [PaymentStatus::Paid, PaymentStatus::Completed]) ? 'danger' : null),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método de Pago')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('ID de Transacción')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(PaymentType::class)
                    ->multiple(),
                
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(PaymentStatus::class)
                    ->multiple(),
                
                SelectFilter::make('club')
                    ->label('Club')
                    ->relationship('club', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('league')
                    ->label('Liga')
                    ->relationship('league', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->where('due_date', '<', now())->whereNotIn('status', [PaymentStatus::Paid, PaymentStatus::Completed]))
                    ->label('Pagos Vencidos'),
                
                Tables\Filters\Filter::make('this_month')
                    ->query(fn (Builder $query): Builder => $query->where('month_year', now()->format('Y-m')))
                    ->label('Este Mes'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('verify')
                    ->label('Verificar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Payment $record) => $record->verify(Auth::user()))
                    ->visible(fn (Payment $record) => $record->status === PaymentStatus::Pending),
                
                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->required()
                            ->label('Motivo del Rechazo'),
                    ])
                    ->action(function (Payment $record, array $data) {
                        $record->reject(Auth::user(), $data['rejection_reason']);
                    })
                    ->visible(fn (Payment $record) => $record->status === PaymentStatus::Pending),
                
                Tables\Actions\Action::make('mark_paid')
                    ->label('Marcar como Pagado')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Payment $record) {
                        $record->update([
                            'status' => PaymentStatus::Paid,
                            'paid_at' => now(),
                        ]);
                    })
                    ->visible(fn (Payment $record) => in_array($record->status, [PaymentStatus::Verified, PaymentStatus::UnderVerification])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('mark_verified')
                        ->label('Marcar como Verificados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(fn (Payment $record) => $record->verify(Auth::user()));
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
