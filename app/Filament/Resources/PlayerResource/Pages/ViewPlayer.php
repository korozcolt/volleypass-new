<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use App\Enums\FederationStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Payment;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPlayer extends ViewRecord
{
    protected static string $resource = PlayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('federate')
                ->label('Federar Jugadora')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn () => !$this->record->isFederated())
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Select::make('payment_id')
                        ->label('Seleccionar Pago')
                        ->options(function () {
                            return Payment::where('club_id', $this->record->current_club_id)
                                ->where('type', PaymentType::Federation)
                                ->where('status', PaymentStatus::Verified)
                                ->pluck('reference_number', 'id');
                        })
                        ->required(),
                ])
                ->action(function (array $data) {
                    $payment = Payment::find($data['payment_id']);
                    $this->record->federateWithPayment($payment);

                    $this->refreshFormData([
                        'federation_status',
                        'federation_date',
                        'federation_expires_at',
                        'federation_payment_id'
                    ]);
                }),

            Actions\Action::make('suspend_federation')
                ->label('Suspender Federación')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->visible(fn () => $this->record->isFederated())
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('Motivo de Suspensión')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->updateFederationStatus(
                        FederationStatus::Suspended,
                        $data['reason']
                    );

                    $this->refreshFormData(['federation_status']);
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información Personal')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.full_name')
                                    ->label('Nombre Completo'),

                                Infolists\Components\TextEntry::make('user.document_number')
                                    ->label('Documento'),

                                Infolists\Components\TextEntry::make('user.email')
                                    ->label('Email'),
                            ]),

                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('currentClub.name')
                                    ->label('Club Actual'),

                                Infolists\Components\TextEntry::make('jersey_number')
                                    ->label('Número de Camiseta'),

                                Infolists\Components\TextEntry::make('position')
                                    ->label('Posición')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('category')
                                    ->label('Categoría')
                                    ->badge(),
                            ]),
                    ]),

                Infolists\Components\Section::make('Estado de Federación')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('federation_status')
                                    ->label('Estado')
                                    ->badge()
                                    ->color(fn ($record) => $record->federation_status->getColor()),

                                Infolists\Components\TextEntry::make('federation_date')
                                    ->label('Fecha de Federación')
                                    ->date('d/m/Y')
                                    ->placeholder('No federada'),

                                Infolists\Components\TextEntry::make('federation_expires_at')
                                    ->label('Fecha de Vencimiento')
                                    ->date('d/m/Y')
                                    ->color(fn ($record) =>
                                        $record->federation_expires_at && $record->federation_expires_at->isPast()
                                            ? 'danger'
                                            : 'success'
                                    )
                                    ->placeholder('No aplica'),
                            ]),

                        Infolists\Components\TextEntry::make('federationPayment.reference_number')
                            ->label('Pago de Federación')
                            ->placeholder('Sin pago asociado'),

                        Infolists\Components\TextEntry::make('federation_notes')
                            ->label('Notas de Federación')
                            ->placeholder('Sin notas')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Estado Médico y Elegibilidad')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('medical_status')
                                    ->label('Estado Médico')
                                    ->badge()
                                    ->color(fn ($record) => $record->medical_status->getColor()),

                                Infolists\Components\IconEntry::make('is_eligible')
                                    ->label('Elegible para Jugar')
                                    ->boolean(),

                                Infolists\Components\TextEntry::make('eligibility_checked_at')
                                    ->label('Verificado el')
                                    ->date('d/m/Y')
                                    ->placeholder('No verificado'),

                                Infolists\Components\TextEntry::make('eligibilityChecker.name')
                                    ->label('Verificado por')
                                    ->placeholder('No verificado'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Información Física')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('height')
                                    ->label('Altura')
                                    ->suffix(' m'),

                                Infolists\Components\TextEntry::make('weight')
                                    ->label('Peso')
                                    ->suffix(' kg'),

                                Infolists\Components\TextEntry::make('bmi')
                                    ->label('IMC')
                                    ->state(fn ($record) => $record->bmi ? number_format($record->bmi, 1) : 'N/A'),

                                Infolists\Components\TextEntry::make('dominant_hand')
                                    ->label('Mano Dominante')
                                    ->formatStateUsing(fn ($state) => match($state) {
                                        'right' => 'Derecha',
                                        'left' => 'Izquierda',
                                        'both' => 'Ambidiestra',
                                        default => 'No especificada'
                                    }),
                            ]),
                    ]),

                Infolists\Components\Section::make('Fechas Importantes')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('debut_date')
                                    ->label('Fecha de Debut')
                                    ->date('d/m/Y')
                                    ->placeholder('No registrada'),

                                Infolists\Components\TextEntry::make('retirement_date')
                                    ->label('Fecha de Retiro')
                                    ->date('d/m/Y')
                                    ->placeholder('Activa')
                                    ->visible(fn ($record) => $record->retirement_date !== null),

                                Infolists\Components\TextEntry::make('years_playing')
                                    ->label('Años Jugando')
                                    ->state(fn ($record) => $record->years_playing ? $record->years_playing . ' años' : 'N/A'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Notas Adicionales')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notas Generales')
                            ->placeholder('Sin notas adicionales')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->notes)),
            ]);
    }
}
