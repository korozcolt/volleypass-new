<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasActiveInactive
{
    /**
     * Boot function para el trait
     */
    protected static function bootHasActiveInactive(): void
    {
        static::creating(function ($model) {
            if (empty($model->is_active) && $model->hasAttribute('is_active')) {
                $model->is_active = true; // Por defecto activo
            }
        });
    }

    /**
     * Scope para registros activos
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para registros inactivos
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Activar el registro
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Desactivar el registro
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Alternar estado activo/inactivo
     */
    public function toggleActive(): bool
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Verificar si está activo
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Verificar si está inactivo
     */
    public function isInactive(): bool
    {
        return $this->is_active === false;
    }

    /**
     * Obtener badge HTML del estado
     */
    public function getActiveStatusBadgeAttribute(): string
    {
        $class = $this->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
        $label = $this->is_active ? 'Activo' : 'Inactivo';

        return '<span class="py-1 px-3 rounded-full text-xs font-medium ' . $class . '">' . $label . '</span>';
    }

    /**
     * Verificar si tiene el atributo is_active
     */
    protected function hasAttribute(string $attribute): bool
    {
        return in_array($attribute, $this->fillable) ||
            array_key_exists($attribute, $this->attributes) ||
            method_exists($this, 'get' . str_replace('_', '', ucwords($attribute, '_')) . 'Attribute');
    }
}
