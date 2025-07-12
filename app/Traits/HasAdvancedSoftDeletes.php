<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasAdvancedSoftDeletes
{
    use SoftDeletes;

    /**
     * Boot function para el trait
     */
    protected static function bootHasAdvancedSoftDeletes(): void
    {
        static::deleting(function (Model $model) {
            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });

        static::restoring(function (Model $model) {
            $model->deleted_by = null;
        });
    }

    /**
     * Relación con el usuario que eliminó el registro
     */
    public function deleter()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'deleted_by');
    }

    /**
     * Scope para filtrar por eliminador
     */
    public function scopeDeletedBy($query, $userId)
    {
        return $query->where('deleted_by', $userId);
    }

    /**
     * Obtiene el nombre del eliminador
     */
    public function getDeleterNameAttribute(): ?string
    {
        return $this->deleter?->name;
    }

    /**
     * Elimina permanentemente con verificación de permisos
     */
    public function forceDeleteWithPermission(): bool
    {
        if (!Auth::user()->can('force-delete', $this)) {
            throw new \Exception('No tienes permisos para eliminar permanentemente este registro.');
        }

        return $this->forceDelete();
    }
}
