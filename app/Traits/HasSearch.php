<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSearch
{
    /**
     * Campos searchables por defecto
     */
    protected function getSearchableFields(): array
    {
        return property_exists($this, 'searchable') ? $this->searchable : ['name'];
    }

    /**
     * Scope para búsqueda global
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            foreach ($this->getSearchableFields() as $field) {
                if (str_contains($field, '.')) {
                    // Búsqueda en relaciones
                    [$relation, $column] = explode('.', $field, 2);
                    $q->orWhereHas($relation, function (Builder $subQuery) use ($column, $search) {
                        $subQuery->where($column, 'LIKE', "%{$search}%");
                    });
                } else {
                    // Búsqueda en campos directos
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            }
        });
    }

    /**
     * Scope para búsqueda exacta
     */
    public function scopeSearchExact(Builder $query, string $field, $value): Builder
    {
        return $query->where($field, $value);
    }

    /**
     * Scope para búsqueda por rango de fechas
     */
    public function scopeSearchDateRange(Builder $query, string $field, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->where($field, '>=', $from);
        }

        if ($to) {
            $query->where($field, '<=', $to);
        }

        return $query;
    }
}
