<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Enums\ConfigurationType;

class LeagueConfiguration extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'league_id',
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
        'validation_rules',
        'default_value',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'type' => ConfigurationType::class,
    ];

    // =======================
    // RELACIONES
    // =======================

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    // =======================
    // ACCESSORS Y MUTATORS
    // =======================

    public function getTypedValueAttribute()
    {
        return match ($this->type) {
            ConfigurationType::BOOLEAN => (bool) $this->value,
            ConfigurationType::NUMBER => is_numeric($this->value) ? (float) $this->value : 0,
            ConfigurationType::JSON => json_decode($this->value, true),
            ConfigurationType::DATE => $this->value ? \Carbon\Carbon::parse($this->value) : null,
            default => $this->value,
        };
    }

    public function setValueAttribute($value)
    {
        $type = $this->type ?? $this->attributes['type'] ?? ConfigurationType::STRING;

        $this->attributes['value'] = match ($type) {
            ConfigurationType::BOOLEAN => $value ? '1' : '0',
            ConfigurationType::JSON => is_array($value) ? json_encode($value) : $value,
            ConfigurationType::DATE => $value instanceof \Carbon\Carbon ? $value->toDateString() : $value,
            default => (string) $value,
        };
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByLeague($query, $league_id)
    {
        return $query->where('league_id', $league_id);
    }

    // =======================
    // MÉTODOS ESTÁTICOS
    // =======================

    public static function get(int $league_id, string $key, $default = null)
    {
        $config = static::where('league_id', $league_id)
            ->where('key', $key)
            ->first();

        if (!$config) {
            return $default;
        }

        return $config->typed_value;
    }

    public static function set(int $league_id, string $key, $value): bool
    {
        $config = static::where('league_id', $league_id)
            ->where('key', $key)
            ->first();

        if (!$config) {
            return false;
        }

        $config->update(['value' => $value]);
        return true;
    }

    public static function getByGroup(int $league_id, string $group): array
    {
        return static::where('league_id', $league_id)
            ->where('group', $group)
            ->get()
            ->mapWithKeys(function ($config) {
                return [$config->key => $config->typed_value];
            })
            ->toArray();
    }

    public static function getPublicConfigs(int $league_id): array
    {
        return static::where('league_id', $league_id)
            ->public()
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

        $validator = Validator::make(
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

        $validator = Validator::make(
            ['value' => $value],
            ['value' => $this->validation_rules]
        );

        return $validator->errors()->get('value');
    }

    // =======================
    // SPATIE ACTIVITY LOG
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value', 'type', 'is_public'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Configuración de liga creada',
                'updated' => 'Configuración de liga actualizada',
                'deleted' => 'Configuración de liga eliminada',
                default => "Configuración {$eventName}"
            });
    }
}
