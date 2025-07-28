<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalCertificateResource\Pages;
use App\Models\MedicalCertificate;
use App\Models\Player;
use App\Models\SportsDoctor;
use App\Enums\MedicalStatus;
use App\Enums\DocumentStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class MedicalCertificateResource extends Resource
{
    protected static ?string $model = MedicalCertificate::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Certificados Médicos';
    protected static ?string $modelLabel = 'Certificado Médico';
    protected static ?string $pluralModelLabel = 'Certificados Médicos';
    protected static ?string $navigationGroup = 'Gestión Médica y Documentos';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Certificado')
                    ->schema([
                        Forms\Components\Select::make('player_id')
                            ->label('Jugadora')
                            ->options(\App\Models\Player::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('doctor_id')
                            ->label('Médico Deportivo')
                            ->options(\App\Models\SportsDoctor::with('user')->get()->pluck('user.name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('medical_status')
                            ->label('Estado Médico')
                            ->options(MedicalStatus::class)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Estado del Documento')
                            ->options(DocumentStatus::class)
                            ->default(DocumentStatus::Pending)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Fechas y Validez')
                    ->schema([
                        Forms\Components\DatePicker::make('examination_date')
                            ->label('Fecha de Examen')
                            ->required(),

                        Forms\Components\DatePicker::make('issued_date')
                            ->label('Fecha de Emisión')
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->required()
                            ->after('issued_date'),

                        Forms\Components\DatePicker::make('next_examination_date')
                            ->label('Próximo Examen Recomendado'),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles Médicos')
                    ->schema([
                        Forms\Components\Textarea::make('observations')
                            ->label('Observaciones Médicas')
                            ->rows(4),

                        Forms\Components\Textarea::make('restrictions')
                            ->label('Restricciones')
                            ->rows(3)
                            ->helperText('Restricciones específicas para la actividad deportiva'),

                        Forms\Components\Textarea::make('recommendations')
                            ->label('Recomendaciones')
                            ->rows(3),

                        Forms\Components\KeyValue::make('vital_signs')
                            ->label('Signos Vitales')
                            ->keyLabel('Signo')
                            ->valueLabel('Valor'),
                    ]),

                Forms\Components\Section::make('Documentos')
                    ->schema([
                        Forms\Components\FileUpload::make('certificate')
                            ->label('Certificado Médico')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(5120)
                            ->directory('medical-certificates'),

                        Forms\Components\FileUpload::make('attachments')
                            ->label('Documentos Adicionales')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(5120)
                            ->directory('medical-certificates/attachments'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('player.user.name')
                    ->label('Jugadora')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->label('Médico')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('medical_status')
                    ->label('Estado Médico')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('examination_date')
                    ->label('Fecha Examen')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at->isPast() ? 'danger' : 'success'),

                Tables\Columns\IconColumn::make('has_certificate')
                    ->label('Certificado')
                    ->boolean()
                    ->state(fn ($record) => !empty($record->certificate)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('medical_status')
                    ->label('Estado Médico')
                    ->options(MedicalStatus::class),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(DocumentStatus::class),

                Tables\Filters\Filter::make('expires_soon')
                    ->label('Expiran Pronto')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<=', now()->addDays(30))),

                Tables\Filters\Filter::make('expired')
                    ->label('Expirados')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === DocumentStatus::Pending)
                    ->action(function ($record) {
                        $record->update(['status' => DocumentStatus::Approved]);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === DocumentStatus::Pending)
                    ->action(function ($record) {
                        $record->update(['status' => DocumentStatus::Rejected]);
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
                Infolists\Components\Section::make('Información General')
                    ->schema([
                        Infolists\Components\TextEntry::make('player.user.name')
                            ->label('Jugadora'),

                        Infolists\Components\TextEntry::make('doctor.user.name')
                            ->label('Médico Deportivo'),

                        Infolists\Components\TextEntry::make('medical_status')
                            ->label('Estado Médico')
                            ->badge(),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado del Documento')
                            ->badge(),
                    ])->columns(2),

                Infolists\Components\Section::make('Fechas')
                    ->schema([
                        Infolists\Components\TextEntry::make('examination_date')
                            ->label('Fecha de Examen')
                            ->date(),

                        Infolists\Components\TextEntry::make('issued_date')
                            ->label('Fecha de Emisión')
                            ->date(),

                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->date(),

                        Infolists\Components\TextEntry::make('next_examination_date')
                            ->label('Próximo Examen')
                            ->date(),
                    ])->columns(2),

                Infolists\Components\Section::make('Detalles Médicos')
                    ->schema([
                        Infolists\Components\TextEntry::make('observations')
                            ->label('Observaciones'),

                        Infolists\Components\TextEntry::make('restrictions')
                            ->label('Restricciones'),

                        Infolists\Components\TextEntry::make('recommendations')
                            ->label('Recomendaciones'),
                    ]),

                Infolists\Components\Section::make('Documentos')
                    ->schema([
                        Infolists\Components\TextEntry::make('certificate')
                            ->label('Certificado'),

                        Infolists\Components\TextEntry::make('attachments')
                            ->label('Documentos Adicionales'),
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
            'index' => Pages\ListMedicalCertificates::route('/'),
            'create' => Pages\CreateMedicalCertificate::route('/create'),
            'view' => Pages\ViewMedicalCertificate::route('/{record}'),
            'edit' => Pages\EditMedicalCertificate::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Referee no puede acceder al panel admin
        if ($user->hasRole('Referee')) {
            return false;
        }
        
        return $user->hasAnyRole([
            'SuperAdmin', 'LeagueAdmin', 'SportsDoctor'
        ]);
    }

    public static function canCreate(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin', 'SportsDoctor']);
    }

    public static function canEdit($record): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        return match($user->getRoleNames()->first()) {
            'SuperAdmin' => true,
            'LeagueAdmin' => true,
            'SportsDoctor' => $record->doctor_id === $user->sportsDoctor?->id,
            default => false
        };
    }

    public static function canDelete($record): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        return $user->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
    }
}
