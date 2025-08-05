<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use App\Models\Team;
use App\Models\Player;
use App\Enums\SelectionStatus;
use App\Enums\UserStatus;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PlayerSelections extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = TeamResource::class;

    protected static string $view = 'filament.resources.team-resource.pages.player-selections';

    public Team $record;

    public static function canAccess(array $parameters = []): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['SuperAdmin', 'LeagueAdmin']);
    }

    public function getTitle(): string
    {
        return 'Gestión de Selecciones - ' . $this->record->name;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Player::query()
                    ->whereHas('club', function (Builder $query) {
                        $query->where('department_id', $this->record->department_id);
                    })
                    ->where('gender', $this->record->gender)
                    ->where('league_category_id', $this->record->league_category_id)
                    ->where('status', UserStatus::Active)
                    ->with(['user', 'club'])
            )
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('Nombre Completo')
                    ->searchable(['users.first_name', 'users.last_name'])
                    ->sortable(),

                TextColumn::make('club.name')
                    ->label('Club')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('position')
                    ->label('Posición')
                    ->badge(),

                TextColumn::make('selection_status')
                    ->label('Estado de Selección')
                    ->badge()
                    ->color(fn($record) => $record?->selection_status?->getColor() ?? 'gray')
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? 'Sin Estado'),

                TextColumn::make('selection_date')
                    ->label('Fecha de Selección')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('No definida'),

                TextColumn::make('selection_notes')
                    ->label('Notas')
                    ->limit(50)
                    ->placeholder('Sin notas'),
            ])
            ->actions([
                Action::make('update_selection')
                    ->label('Actualizar Selección')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Select::make('selection_status')
                            ->label('Estado de Selección')
                            ->options(SelectionStatus::options())
                            ->required()
                            ->live(),

                        DateTimePicker::make('selection_date')
                            ->label('Fecha de Selección')
                            ->visible(fn (\Filament\Forms\Get $get) => $get('selection_status') !== SelectionStatus::NONE->value)
                            ->default(now()),

                        Textarea::make('selection_notes')
                            ->label('Notas de Selección')
                            ->rows(3)
                            ->visible(fn (\Filament\Forms\Get $get) => $get('selection_status') !== SelectionStatus::NONE->value),
                    ])
                    ->fillForm(fn ($record) => [
                        'selection_status' => $record->selection_status?->value ?? SelectionStatus::NONE->value,
                        'selection_date' => $record->selection_date,
                        'selection_notes' => $record->selection_notes,
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'selection_status' => $data['selection_status'],
                            'selection_date' => $data['selection_status'] !== SelectionStatus::NONE->value ? ($data['selection_date'] ?? now()) : null,
                            'selection_notes' => $data['selection_status'] !== SelectionStatus::NONE->value ? $data['selection_notes'] : null,
                        ]);

                        Notification::make()
                            ->title('Estado de selección actualizado')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('mark_preselection')
                    ->label('Marcar como Preselección')
                    ->icon('heroicon-o-flag')
                    ->color('warning')
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            $record->update([
                                'selection_status' => SelectionStatus::PRESELECCION,
                                'selection_date' => now(),
                            ]);
                        });

                        Notification::make()
                            ->title('Jugadoras marcadas como preselección')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('mark_selection')
                    ->label('Marcar como Selección')
                    ->icon('heroicon-o-flag')
                    ->color('success')
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            $record->update([
                                'selection_status' => SelectionStatus::SELECCION,
                                'selection_date' => now(),
                            ]);
                        });

                        Notification::make()
                            ->title('Jugadoras marcadas como selección')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('clear_selection')
                    ->label('Limpiar Selección')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            $record->update([
                                'selection_status' => SelectionStatus::NONE,
                                'selection_date' => null,
                                'selection_notes' => null,
                            ]);
                        });

                        Notification::make()
                            ->title('Selecciones limpiadas')
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('selection_status')
                    ->label('Estado de Selección')
                    ->options(SelectionStatus::options()),
            ]);
    }
}