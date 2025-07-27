<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Validator;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SystemConfiguration extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'key',
        'name',
        'description',
        'value',
        'type',
        'group',
        'is_public',
        'is_editable',
        'validation_rules',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_editable' => 'boolean',
        'value' => 'string', // Se maneja como string y se convierte según el tipo
    ];

    // =======================
    // SPATIE ACTIVITY LOG
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value', 'type', 'is_public', 'is_editable'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Configuración del sistema creada',
                'updated' => 'Configuración del sistema actualizada',
                'deleted' => 'Configuración del sistema eliminada',
                default => "Configuración {$eventName}"
            });
    }

    // =======================
    // ACCESSORS Y MUTATORS
    // =======================

    public function getTypedValueAttribute()
    {
        return match ($this->type) {
            'boolean' => (bool) $this->value,
            'number' => is_numeric($this->value) ? (float) $this->value : 0,
            'json' => json_decode($this->value, true),
            'date' => $this->value ? \Carbon\Carbon::parse($this->value) : null,
            default => $this->value,
        };
    }

    public function setValueAttribute($value)
    {
        // Obtener el tipo actual, ya sea del modelo o de los atributos que se están estableciendo
        $type = $this->type ?? $this->attributes['type'] ?? 'string';

        $this->attributes['value'] = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            'date' => $value instanceof \Carbon\Carbon ? $value->toDateString() : $value,
            default => (string) $value,
        };
    }

    // =======================
    // SCOPES
    // =======================

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // =======================
    // MÉTODOS ESTÁTICOS
    // =======================

    public static function get(string $key, $default = null)
    {
        $config = static::where('key', $key)->first();

        if (!$config) {
            return $default;
        }

        return $config->typed_value;
    }

    public static function getValue(string $key, $default = null)
    {
        return static::get($key, $default);
    }

    public static function set(string $key, $value): bool
    {
        $config = static::where('key', $key)->first();

        if (!$config) {
            return false;
        }

        if (!$config->is_editable) {
            return false;
        }

        $config->update(['value' => $value]);

        return true;
    }

    public static function getByGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(function ($config) {
                return [$config->key => $config->typed_value];
            })
            ->toArray();
    }

    public static function getPublicConfigs(): array
    {
        return static::public()
            ->get()
            ->mapWithKeys(function ($config) {
                return [$config->key => $config->typed_value];
            })
            ->toArray();
    }

    // =======================
    // VALIDACIÓN
    // =======================

    public function validateValue($value): bool
    {
        if (!$this->validation_rules) {
            return true;
        }

        $validator = \Illuminate\Support\Facades\Validator::make(
            ['value' => $value],
            ['value' => $this->validation_rules]
        );

        return !$validator->fails();
    }

    public function getValidationErrors($value): array
    {
        if (!$this->validation_rules) {
            return [];
        }

        $validator = \Illuminate\Support\Facades\Validator::make(
            ['value' => $value],
            ['value' => $this->validation_rules]
        );

        return $validator->errors()->get('value');
    }
}
