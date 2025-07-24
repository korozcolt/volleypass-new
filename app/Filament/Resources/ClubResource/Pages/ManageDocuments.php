<?php

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use App\Models\Club;
use App\Models\PlayerDocument;
use App\Enums\DocumentType;
use App\Enums\DocumentStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ManageDocuments extends ManageRelatedRecords
{
    protected static string $resource = ClubResource::class;

    protected static string $relationship = 'documents';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Documentos';

    public static function getNavigationLabel(): string
    {
        return 'Documentos';
    }

    public function getTitle(): string
    {
        return 'Gestión de Documentos - ' . $this->getRecord()->name;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Documento')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Documento')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                if (filled($state)) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Identificador')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alpha_dash(),

                        Forms\Components\Select::make('document_type')
                            ->label('Tipo de Documento')
                            ->options(DocumentType::class)
                            ->required()
                            ->native(false)
                            ->searchable(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(1000)
                            ->rows(3),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(DocumentStatus::class)
                            ->required()
                            ->native(false)
                            ->default(DocumentStatus::Pending),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Archivo')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Archivo')
                            ->directory('club-documents')
                            ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(10240) // 10MB
                            ->downloadable()
                            ->previewable()
                            ->openable()
                            ->required(),

                        Forms\Components\TextInput::make('file_size')
                            ->label('Tamaño del Archivo (KB)')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('mime_type')
                            ->label('Tipo MIME')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Fechas Importantes')
                    ->schema([
                        Forms\Components\DatePicker::make('issued_at')
                            ->label('Fecha de Emisión')
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Fecha de Vencimiento')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('issued_at'),

                        Forms\Components\DatePicker::make('verified_at')
                            ->label('Fecha de Verificación')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->disabled(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->maxLength(1000)
                            ->rows(3),

                        Forms\Components\Toggle::make('is_required')
                            ->label('Documento Obligatorio')
                            ->default(false),

                        Forms\Components\Toggle::make('is_public')
                            ->label('Documento Público')
                            ->default(false),
                    ])
                    ->columns(3),
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
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('document_type')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(function (string $state): string {
                        return match ($state) {
                            'approved' => 'success',
                            'pending' => 'warning',
                            'rejected' => 'danger',
                            'expired' => 'gray',
                            default => 'gray',
                        };
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_size')
                    ->label('Tamaño')
                    ->formatStateUsing(function (?int $state): string {
                        return $state ? number_format($state / 1024, 2) . ' KB' : 'N/A';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Fecha Emisión')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->color(function (?string $state): string {
                        if (!$state) return 'gray';
                        $expiresAt = \Carbon\Carbon::parse($state);
                        $now = \Carbon\Carbon::now();
                        
                        if ($expiresAt->isPast()) return 'danger';
                        if ($expiresAt->diffInDays($now) <= 30) return 'warning';
                        return 'success';
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('Obligatorio')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('document_type')
                    ->label('Tipo de Documento')
                    ->options(DocumentType::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(DocumentStatus::class)
                    ->multiple(),

                Tables\Filters\Filter::make('expires_soon')
                    ->label('Vence Pronto')
                    ->query(function (Builder $query): Builder {
                        return $query->where('expires_at', '<=', now()->addDays(30))
                                    ->where('expires_at', '>', now());
                    }),

                Tables\Filters\Filter::make('expired')
                    ->label('Vencidos')
                    ->query(function (Builder $query): Builder {
                        return $query->where('expires_at', '<', now());
                    }),

                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Obligatorio'),

                Tables\Filters\Filter::make('created_from')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Creado desde')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                function (Builder $query, $date): Builder {
                                    return $query->whereDate('created_at', '>=', $date);
                                },
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nuevo Documento')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['club_id'] = $this->getRecord()->id;
                        return $data;
                    }),

                Tables\Actions\Action::make('upload_bulk')
                    ->label('Subida Masiva')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->color('info')
                    ->form([
                        Forms\Components\FileUpload::make('files')
                            ->label('Archivos')
                            ->multiple()
                            ->directory('club-documents/bulk')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240)
                            ->required(),
                        
                        Forms\Components\Select::make('document_type')
                            ->label('Tipo de Documento')
                            ->options(DocumentType::class)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $club = $this->getRecord();
                        $count = 0;
                        
                        foreach ($data['files'] as $file) {
                            PlayerDocument::create([
                                'club_id' => $club->id,
                                'name' => pathinfo($file, PATHINFO_FILENAME),
                                'document_type' => $data['document_type'],
                                'file_path' => $file,
                                'status' => DocumentStatus::Pending,
                            ]);
                            $count++;
                        }
                        
                        Notification::make()
                            ->title('Documentos subidos exitosamente')
                            ->body("Se subieron {$count} documentos.")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(function (PlayerDocument $record): string {
                        return Storage::url($record->file_path);
                    })
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(function (PlayerDocument $record): bool {
                        return $record->status === DocumentStatus::Pending;
                    })
                    ->action(function (PlayerDocument $record) {
                        $record->update([
                            'status' => DocumentStatus::Approved,
                            'verified_at' => now(),
                        ]);
                        
                        Notification::make()
                            ->title('Documento aprobado')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function (PlayerDocument $record): bool {
                        return $record->status === DocumentStatus::Pending;
                    })
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Motivo del Rechazo')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (PlayerDocument $record, array $data) {
                        $record->update([
                            'status' => DocumentStatus::Rejected,
                            'notes' => $data['rejection_reason'],
                        ]);
                        
                        Notification::make()
                            ->title('Documento rechazado')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Editar'),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),

                    Tables\Actions\BulkAction::make('approve_bulk')
                        ->label('Aprobar seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function (PlayerDocument $record) {
                                if ($record->status === DocumentStatus::Pending) {
                                    $record->update([
                                        'status' => DocumentStatus::Approved,
                                        'verified_at' => now(),
                                    ]);
                                }
                            });
                            
                            Notification::make()
                                ->title('Documentos aprobados')
                                ->body('Se aprobaron los documentos seleccionados.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('export_documents')
                        ->label('Exportar seleccionados')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            // Implementar lógica de exportación
                            Notification::make()
                                ->title('Exportación iniciada')
                                ->body('Los documentos se están preparando para descarga.')
                                ->info()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_report')
                ->label('Generar Reporte')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->action(function () {
                    $club = $this->getRecord();
                    
                    // Implementar lógica de generación de reporte
                    Notification::make()
                        ->title('Reporte generado')
                        ->body('El reporte de documentos ha sido generado exitosamente.')
                        ->success()
                        ->send();
                }),
        ];
    }
}