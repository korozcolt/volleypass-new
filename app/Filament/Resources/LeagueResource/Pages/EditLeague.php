<?php

namespace App\Filament\Resources\LeagueResource\Pages;

use App\Filament\Resources\LeagueResource;
use App\Filament\Resources\LeagueResource\Widgets\CategoryStatsWidget;
use App\Filament\Resources\LeagueResource\Widgets\CategoryImpactPreviewWidget;
use App\Models\LeagueConfiguration;
use App\Models\LeagueCategory;
use App\Services\LeagueConfigurationService;
use App\Services\CategoryNotificationService;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditLeague extends EditRecord
{
    protected static string $resource = LeagueResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Información General';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('export_categories')
                ->label('Exportar Categorías')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $this->exportCategories();
                }),
            Actions\Action::make('import_categories')
                ->label('Importar Categorías')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Forms\Components\FileUpload::make('categories_file')
                        ->label('Archivo de Categorías')
                        ->acceptedFileTypes(['application/json'])
                        ->required()
                        ->helperText('Selecciona un archivo JSON con la configuración de categorías'),
                ])
                ->action(function (array $data) {
                    $this->importCategories($data['categories_file']);
                }),
            Actions\Action::make('reload_configurations')
                ->label('Recargar Configuraciones')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function () {
                    // Recargar configuraciones básicas
                    $this->record->refresh();
                    Notification::make()
                        ->title('Configuraciones de liga recargadas exitosamente.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CategoryStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CategoryImpactPreviewWidget::class,
        ];
    }

    private function exportCategories(): void
    {
        $categories = $this->record->categories()->active()->get();
        
        $exportData = [
            'league_name' => $this->record->name,
            'export_date' => now()->toISOString(),
            'categories' => $categories->map(function ($category) {
                return [
                    'name' => $category->name,
                    'code' => $category->code,
                    'min_age' => $category->min_age,
                    'max_age' => $category->max_age,
                    'gender' => $category->gender,
                    'sort_order' => $category->sort_order,
                    'description' => $category->description,
                ];
            })->toArray(),
        ];

        $filename = 'categories_' . Str::slug($this->record->name) . '_' . now()->format('Y-m-d_H-i-s') . '.json';
        $content = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        Storage::disk('public')->put('exports/' . $filename, $content);
        
        Notification::make()
             ->title('Categorías exportadas exitosamente')
             ->body('Archivo: ' . $filename)
             ->success()
             ->send();
    }

    private function importCategories(string $filePath): void
    {
        try {
            $content = Storage::disk('public')->get($filePath);
            $data = json_decode($content, true);
            
            if (!isset($data['categories']) || !is_array($data['categories'])) {
                throw new \Exception('Formato de archivo inválido');
            }

            $importedCount = 0;
            $errors = [];

            foreach ($data['categories'] as $categoryData) {
                try {
                    LeagueCategory::updateOrCreate(
                        [
                            'league_id' => $this->record->id,
                            'code' => $categoryData['code'],
                        ],
                        [
                            'name' => $categoryData['name'],
                            'min_age' => $categoryData['min_age'],
                            'max_age' => $categoryData['max_age'],
                            'gender' => $categoryData['gender'] ?? 'female',
                            'sort_order' => $categoryData['sort_order'] ?? 0,
                            'description' => $categoryData['description'] ?? null,
                            'is_active' => true,
                        ]
                    );
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error en categoría {$categoryData['name']}: " . $e->getMessage();
                }
            }

            // Las categorías han sido importadas exitosamente

            if (empty($errors)) {
                Notification::make()
                    ->title('Importación completada exitosamente')
                    ->body("{$importedCount} categorías importadas")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Importación completada con errores')
                    ->body("{$importedCount} categorías importadas, " . count($errors) . ' errores')
                    ->warning()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error en la importación')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
