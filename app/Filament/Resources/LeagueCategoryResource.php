<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeagueCategoryResource\Pages;
use App\Models\LeagueCategory;
use App\Models\League;
use App\Services\CategoryValidationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class LeagueCategoryResource extends Resource
{
    protected static ?string $model = LeagueCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Categorías por Liga';
    protected static ?string $modelLabel = 'Categoría de Liga';
    protected static ?string $pluralModelLabel = 'Categorías de Liga';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLeagueCategories::route('/'),
            'create' => Pages\CreateLeagueCategory::route('/create'),
            'edit' => Pages\EditLeagueCategory::route('/{record}/edit'),
        ];
    }
}
