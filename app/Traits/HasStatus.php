<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasStatus
{
    /**
     * Boot function para el trait
     */
    protected static function bootHasStatus(): void
    {
        static::creating(function ($model) {
            if (empty($model->status) && method_exists($model, 'getDefaultStatus')) {
                $model->status = $model->getDefaultStatus();
            }
        });
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeWithStatus(Builder $query, $status): Builder
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }

        return $query->where('status', $status);
    }

    /**
     * Scope para excluir estados
     */
    public function scopeWithoutStatus(Builder $query, $status): Builder
    {
        if (is_array($status)) {
            return $query->whereNotIn('status', $status);
        }

        return $query->where('status', '!=', $status);
    }

    /**
     * Scope para registros activos
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para registros inactivos
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope para registros pendientes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Verifica si el registro está en un estado específico
     */
    public function hasStatus($status): bool
    {
        if (is_array($status)) {
            return in_array($this->status->value ?? $this->status, $status);
        }

        return ($this->status->value ?? $this->status) === $status;
    }

    /**
     * Cambia el estado del registro
     */
    public function changeStatus($newStatus, string $reason = null): bool
    {
        $oldStatus = $this->status;

        // Verificar si la transición es válida
        if (method_exists($this->status, 'canTransitionTo') &&
            !$this->status->canTransitionTo($newStatus)) {
            throw new \Exception("No se puede cambiar de {$oldStatus->getLabel()} a {$newStatus->getLabel()}");
        }

        $this->status = $newStatus;
        $result = $this->save();

        if ($result && $reason) {
            $this->logActivity(
                'status_changed',
                "Estado cambiado de {$oldStatus->getLabel()} a {$newStatus->getLabel()}: {$reason}"
            );
        }

        return $result;
    }

    /**
     * Obtiene el badge HTML del estado
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->status->getLabelHtml();
    }
}
