<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Enums\UserStatus;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentUsersTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Usuarios Registrados Recientemente';

    protected static ?int $sort = 3;

    protected static ?string $pollingInterval = '30s';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with(['roles', 'country', 'department', 'city'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(32)
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(25),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(30)
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->limit(1)
                    ->colors([
                        'danger' => 'SuperAdmin',
                        'primary' => 'LeagueAdmin',
                        'success' => 'ClubDirector',
                        'info' => 'Player',
                        'warning' => 'Coach',
                        'purple' => 'SportsDoctor',
                        'gray' => 'Verifier',
                    ]),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor()),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ciudad')
                    ->limit(20)
                    ->placeholder('No especificada'),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('d/m/Y H:i:s')),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    //->url(fn (User $record): string => route('filament.admin.resources.users.view', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('edit')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    //->url(fn (User $record): string => route('filament.admin.resources.users.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('No hay usuarios recientes')
            ->emptyStateDescription('Los nuevos usuarios registrados aparecerán aquí.')
            ->emptyStateIcon('heroicon-o-users')
            ->striped()
            ->paginated(false);
    }

    /**
     * Determinar si puede ver este widget según el rol
     */
    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole([
            'SuperAdmin',
            'LeagueAdmin'
        ]);
    }
}
