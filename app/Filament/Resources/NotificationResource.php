<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Models\Notification;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Notificaciones';
    protected static ?string $modelLabel = 'Notificación';
    protected static ?string $pluralModelLabel = 'Notificaciones';
    protected static ?string $navigationGroup = 'Comunicación';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Notificación')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('message')
                            ->label('Mensaje')
                            ->required()
                            ->rows(4),

                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'info' => 'Información',
                                'success' => 'Éxito',
                                'warning' => 'Advertencia',
                                'error' => 'Error',
                                'announcement' => 'Anuncio',
                            ])
                            ->default('info')
                            ->required(),

                        Forms\Components\Select::make('priority')
                            ->label('Prioridad')
                            ->options([
                                'low' => 'Baja',
                                'normal' => 'Normal',
                                'high' => 'Alta',
                                'urgent' => 'Urgente',
                            ])
                            ->default('normal')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Destinatarios')
                    ->schema([
                        Forms\Components\Select::make('recipient_type')
                            ->label('Tipo de Destinatario')
                            ->options([
                                'all' => 'Todos los usuarios',
                                'role' => 'Por rol',
                                'specific' => 'Usuarios específicos',
                                'club' => 'Por club',
                                'league' => 'Por liga',
                            ])
                            ->default('all')
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('role_filter')
                            ->label('Rol')
                            ->options(\Spatie\Permission\Models\Role::pluck('name', 'name'))
                            ->multiple()
                            ->visible(fn ($get) => $get('recipient_type') === 'role'),

                        Forms\Components\Select::make('user_ids')
                            ->label('Usuarios Específicos')
                            ->options(\App\Models\User::pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->visible(fn ($get) => $get('recipient_type') === 'specific'),

                        Forms\Components\Select::make('club_filter')
                            ->label('Club')
                            ->options(\App\Models\Club::pluck('name', 'id'))
                            ->multiple()
                            ->visible(fn ($get) => $get('recipient_type') === 'club'),

                        Forms\Components\Select::make('league_filter')
                            ->label('Liga')
                            ->options(\App\Models\League::pluck('name', 'id'))
                            ->multiple()
                            ->visible(fn ($get) => $get('recipient_type') === 'league'),
                    ]),

                Forms\Components\Section::make('Configuración de Envío')
                    ->schema([
                        Forms\Components\Toggle::make('send_email')
                            ->label('Enviar por Email')
                            ->default(true),

                        Forms\Components\Toggle::make('send_push')
                            ->label('Enviar Notificación Push')
                            ->default(false),

                        Forms\Components\Toggle::make('send_whatsapp')
                            ->label('Enviar por WhatsApp')
                            ->default(false),

                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Programar Envío')
                            ->helperText('Dejar vacío para enviar inmediatamente'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración Adicional')
                    ->schema([
                        Forms\Components\TextInput::make('action_url')
                            ->label('URL de Acción')
                            ->url()
                            ->maxLength(255)
                            ->helperText('URL a la que dirigir cuando se haga clic en la notificación'),

                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadatos')
                            ->keyLabel('Clave')
                            ->valueLabel('Valor'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'info',
                        'success' => 'success',
                        'warning' => 'warning',
                        'error' => 'danger',
                        'announcement' => 'primary',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('recipient_type')
                    ->label('Destinatarios')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'all' => 'Todos',
                        'role' => 'Por rol',
                        'specific' => 'Específicos',
                        'club' => 'Por club',
                        'league' => 'Por liga',
                    }),

                Tables\Columns\TextColumn::make('sent_count')
                    ->label('Enviadas')
                    ->badge()
                    ->default(0),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Programada')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_sent')
                    ->label('Enviada')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'info' => 'Información',
                        'success' => 'Éxito',
                        'warning' => 'Advertencia',
                        'error' => 'Error',
                        'announcement' => 'Anuncio',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options([
                        'low' => 'Baja',
                        'normal' => 'Normal',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                    ]),

                Tables\Filters\TernaryFilter::make('is_sent')
                    ->label('Enviada'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send')
                    ->label('Enviar')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_sent)
                    ->action(function ($record) {
                        // Aquí iría la lógica para enviar la notificación
                        $record->update([
                            'is_sent' => true,
                            'sent_at' => now(),
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
                Infolists\Components\Section::make('Información de la Notificación')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Título'),

                        Infolists\Components\TextEntry::make('message')
                            ->label('Mensaje'),

                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipo')
                            ->badge(),

                        Infolists\Components\TextEntry::make('priority')
                            ->label('Prioridad')
                            ->badge(),
                    ])->columns(2),

                Infolists\Components\Section::make('Configuración de Envío')
                    ->schema([
                        Infolists\Components\IconEntry::make('send_email')
                            ->label('Email')
                            ->boolean(),

                        Infolists\Components\IconEntry::make('send_push')
                            ->label('Push')
                            ->boolean(),

                        Infolists\Components\IconEntry::make('send_whatsapp')
                            ->label('WhatsApp')
                            ->boolean(),

                        Infolists\Components\TextEntry::make('scheduled_at')
                            ->label('Programada para')
                            ->dateTime(),
                    ])->columns(4),

                Infolists\Components\Section::make('Estado')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_sent')
                            ->label('Enviada')
                            ->boolean(),

                        Infolists\Components\TextEntry::make('sent_count')
                            ->label('Cantidad Enviada'),

                        Infolists\Components\TextEntry::make('sent_at')
                            ->label('Enviada el')
                            ->dateTime(),
                    ])->columns(3),
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'view' => Pages\ViewNotification::route('/{record}'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
