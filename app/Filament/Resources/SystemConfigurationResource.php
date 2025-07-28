<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemConfigurationResource\Pages;
use App\Models\SystemConfiguration;
use App\Services\SystemConfigurationService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class SystemConfigurationResource extends Resource
{
    protected static ?string $model = SystemConfiguration::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Configuración del Sistema';
    protected static ?string $modelLabel = 'Configuración';
    protected static ?string $pluralModelLabel = 'Configuraciones';
    protected static ?string $navigationGroup = 'Administración del Sistema';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Clave')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3),

                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'string' => 'Texto',
                                'number' => 'Número',
                                'boolean' => 'Booleano',
                                'json' => 'JSON',
                                'date' => 'Fecha',
                                'email' => 'Email',
                                'url' => 'URL',
                            ])
                            ->default('string')
                            ->required()
                            ->reactive(),
                    ])->columns(2),

                Forms\Components\Section::make('Valor')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('Valor')
                            ->visible(fn($get) => in_array($get('type'), ['string', 'email', 'url']))
                            ->maxLength(1000)
                            ->dehydrateStateUsing(fn($state) => (string) $state),

                        Forms\Components\TextInput::make('value')
                            ->label('Valor')
                            ->numeric()
                            ->visible(fn($get) => $get('type') === 'number')
                            ->dehydrateStateUsing(fn($state) => (string) $state),

                        Forms\Components\Toggle::make('value')
                            ->label('Valor')
                            ->visible(fn($get) => $get('type') === 'boolean')
                            ->formatStateUsing(fn($state) => (bool) $state)
                            ->dehydrateStateUsing(fn($state) => $state ? '1' : '0'),

                        Forms\Components\DatePicker::make('value')
                            ->label('Valor')
                            ->visible(fn($get) => $get('type') === 'date')
                            ->formatStateUsing(function ($state, $get) {
                                if (!$state || $get('type') !== 'date') {
                                    return null;
                                }
                                try {
                                    return \Carbon\Carbon::parse($state)->format('Y-m-d');
                                } catch (\Exception $e) {
                                    return null;
                                }
                            })
                            ->dehydrateStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->toDateString() : null),

                        Forms\Components\Textarea::make('value')
                            ->label('Valor JSON')
                            ->rows(5)
                            ->visible(fn($get) => $get('type') === 'json')
                            ->helperText('Ingrese un JSON válido')
                            ->formatStateUsing(fn($state) => is_string($state) ? $state : json_encode($state, JSON_PRETTY_PRINT))
                            ->dehydrateStateUsing(fn($state) => $state),
                    ]),

                Forms\Components\Section::make('Configuración Avanzada')
                    ->schema([
                        Forms\Components\TextInput::make('group')
                            ->label('Grupo')
                            ->maxLength(100)
                            ->helperText('Agrupa configuraciones relacionadas'),

                        Forms\Components\Toggle::make('is_public')
                            ->label('Público')
                            ->helperText('Si está marcado, será visible en la API pública'),

                        Forms\Components\Toggle::make('is_editable')
                            ->label('Editable')
                            ->default(true)
                            ->helperText('Si está marcado, se puede editar desde el panel'),

                        Forms\Components\TextInput::make('validation_rules')
                            ->label('Reglas de Validación')
                            ->maxLength(255)
                            ->helperText('Reglas de validación de Laravel (ej: required|min:3)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Clave')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('group')
                    ->label('Grupo')
                    ->searchable()
                    ->badge(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_editable')
                    ->label('Editable')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'string' => 'Texto',
                        'number' => 'Número',
                        'boolean' => 'Booleano',
                        'json' => 'JSON',
                        'date' => 'Fecha',
                        'email' => 'Email',
                        'url' => 'URL',
                    ]),

                Tables\Filters\SelectFilter::make('group')
                    ->label('Grupo')
                    ->options(function () {
                        return SystemConfiguration::distinct('group')
                            ->whereNotNull('group')
                            ->pluck('group', 'group')
                            ->toArray();
                    }),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Público'),

                Tables\Filters\TernaryFilter::make('is_editable')
                    ->label('Editable'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->is_editable),
                Tables\Actions\Action::make('test_config')
                    ->label('Probar')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->visible(fn($record) => in_array($record->key, [
                        'notifications.email_enabled',
                        'notifications.whatsapp_enabled',
                        'maintenance.mode'
                    ]))
                    ->action(function ($record) {
                        // Lógica para probar configuraciones específicas
                        match ($record->key) {
                            'notifications.email_enabled' => static::testEmailNotification(),
                            'notifications.whatsapp_enabled' => static::testWhatsAppNotification(),
                            'maintenance.mode' => static::testMaintenanceMode($record),
                            default => null,
                        };
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->hasRole('SuperAdmin')),
                ]),
            ])
            ->defaultSort('group')
            ->groups([
                Tables\Grouping\Group::make('group')
                    ->label('Grupo')
                    ->collapsible(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Configuración')
                    ->schema([
                        Infolists\Components\TextEntry::make('key')
                            ->label('Clave')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción'),

                        Infolists\Components\TextEntry::make('group')
                            ->label('Grupo')
                            ->badge(),

                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipo')
                            ->badge(),
                    ])->columns(2),

                Infolists\Components\Section::make('Valor Actual')
                    ->schema([
                        Infolists\Components\TextEntry::make('value')
                            ->label('Valor')
                            ->copyable(),
                    ]),

                Infolists\Components\Section::make('Configuración')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_public')
                            ->label('Público')
                            ->boolean(),

                        Infolists\Components\IconEntry::make('is_editable')
                            ->label('Editable')
                            ->boolean(),

                        Infolists\Components\TextEntry::make('validation_rules')
                            ->label('Reglas de Validación'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime(),
                    ])->columns(2),
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
            'index' => Pages\ListSystemConfigurations::route('/'),
            'create' => Pages\CreateSystemConfiguration::route('/create'),
            'view' => Pages\ViewSystemConfiguration::route('/{record}'),
            'edit' => Pages\EditSystemConfiguration::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasRole('SuperAdmin');
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->hasRole('SuperAdmin');
    }

    /**
     * Recargar configuraciones después de crear/actualizar
     */
    public static function afterSave(): void
    {
        app(SystemConfigurationService::class)->reload();
    }

    /**
     * Probar notificación por email
     */
    private static function testEmailNotification(): void
    {
        try {
            // Aquí podrías enviar un email de prueba
            Notification::route('mail', system_config('notifications.admin_email'))
                ->notify(new \App\Notifications\TestEmailNotification());

            \Filament\Notifications\Notification::make()
                ->title('Email de prueba enviado')
                ->success()
                ->send();
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error al enviar email')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Probar notificación por WhatsApp
     */
    private static function testWhatsAppNotification(): void
    {
        \Filament\Notifications\Notification::make()
            ->title('Función de WhatsApp')
            ->body('La integración de WhatsApp está pendiente de implementación.')
            ->info()
            ->send();
    }

    /**
     * Probar modo mantenimiento
     */
    private static function testMaintenanceMode($record): void
    {
        $isActive = (bool) $record->typed_value;

        \Filament\Notifications\Notification::make()
            ->title('Modo Mantenimiento')
            ->body($isActive
                ? 'El modo mantenimiento está ACTIVO. Los usuarios no podrán acceder al sistema.'
                : 'El modo mantenimiento está INACTIVO. El sistema funciona normalmente.')
            ->color($isActive ? 'danger' : 'success')
            ->send();
    }
}
