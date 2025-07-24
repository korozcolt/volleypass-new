<?php

namespace App\Filament\Resources\LeagueResource\Pages;

use App\Filament\Resources\LeagueResource;
use App\Filament\Resources\LeagueResource\Widgets\CategoryImpactPreviewWidget;
use App\Filament\Resources\LeagueResource\Widgets\CategoryStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeague extends EditRecord
{
    protected static string $resource = LeagueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),

            // Acción adicional para gestión rápida de categorías
            Actions\Action::make('manage_categories')
                ->label('Gestionar Categorías')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('info')
                ->url(fn() => '#categories')
                ->extraAttributes(['onclick' => 'document.querySelector("[data-tab=\'categories\']")?.click(); return false;']),

            // Acción para validación rápida
            Actions\Action::make('validate_configuration')
                ->label('Validar Configuración')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->validateCategoryConfiguration();
                })
                ->visible(fn() => $this->record->hasCustomCategories()),
        ];
    }

    /**
     * Widgets que se mostrarán después del formulario principal
     */
    protected function getFooterWidgets(): array
    {
        return [
            CategoryStatsWidget::class,
            CategoryImpactPreviewWidget::class,
        ];
    }

    /**
     * Método para validar configuración de categorías
     */
    private function validateCategoryConfiguration(): void
    {
        if (!$this->record->hasCustomCategories()) {
            $this->notify('warning', 'No hay categorías personalizadas configuradas');
            return;
        }

        // Aquí puedes agregar lógica de validación más específica
        $impact = $this->calculateCategoryImpact();

        if ($impact['summary']['no_category'] > 0) {
            $this->notify('warning',
                'Configuración incompleta',
                "{$impact['summary']['no_category']} jugadoras sin categoría asignada"
            );
        } else {
            $this->notify('success',
                'Configuración válida',
                'Todas las jugadoras tienen categoría asignada'
            );
        }
    }

    /**
     * Método helper para cálculo rápido de impacto
     */
    private function calculateCategoryImpact(): array
    {
        // Lógica simplificada para validación rápida
        $players = \App\Models\Player::whereHas('currentClub', function ($query) {
            $query->where('league_id', $this->record->id);
        })->get();

        $summary = [
            'no_category' => 0,
            'category_change' => 0,
            'no_change' => 0,
        ];

        if (!$this->record->hasCustomCategories()) {
            return ['summary' => $summary];
        }

        $customCategories = $this->record->getActiveCategories();

        foreach ($players as $player) {
            $age = $player->age;
            if (!$age) continue;

            $customCategory = $customCategories->first(function ($category) use ($age) {
                return $age >= $category->min_age && $age <= $category->max_age;
            });

            if (!$customCategory) {
                $summary['no_category']++;
            } else {
                $traditionalCategory = \App\Enums\PlayerCategory::getForAge($age, $player->user->gender ?? 'female');
                if ($customCategory->code !== $traditionalCategory->value) {
                    $summary['category_change']++;
                } else {
                    $summary['no_change']++;
                }
            }
        }

        return ['summary' => $summary];
    }

    /**
     * Método helper para notificaciones mejoradas
     */
    private function notify(string $type, string $title, string $body = ''): void
    {
        \Filament\Notifications\Notification::make()
            ->title($title)
            ->body($body)
            ->$type()
            ->send();
    }

    /**
     * Configurar pestañas activas por defecto
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Si hay problemas de categorías, activar tab de categorías por defecto
        if ($this->record && $this->record->hasCustomCategories()) {
            $impact = $this->calculateCategoryImpact();
            if ($impact['summary']['no_category'] > 0) {
                // Lógica para highlight de problemas críticos
                session()->flash('highlight_categories', true);
            }
        }

        return parent::mutateFormDataBeforeFill($data);
    }

    /**
     * Configuración de página
     */
    public function getTitle(): string
    {
        $title = parent::getTitle();

        // Agregar indicador si hay problemas críticos
        if ($this->record && $this->record->hasCustomCategories()) {
            $impact = $this->calculateCategoryImpact();
            if ($impact['summary']['no_category'] > 0) {
                $title .= " ⚠️";
            }
        }

        return $title;
    }

    /**
     * Breadcrumbs personalizados
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();

        // Agregar contexto adicional si es necesario
        if ($this->record && $this->record->hasCustomCategories()) {
            $categories = $this->record->getActiveCategories();
            $lastBreadcrumb = array_key_last($breadcrumbs);
            $breadcrumbs[$lastBreadcrumb] .= " ({$categories->count()} categorías)";
        }

        return $breadcrumbs;
    }
}
