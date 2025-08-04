<?php

namespace App\Models;

use App\Enums\SetupStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SetupState extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'step',
        'status',
        'data',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => SetupStatus::class,
        'data' => 'array',
        'completed_at' => 'datetime',
    ];

    // =======================
    // RELATIONSHIPS
    // =======================

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeCompleted($query)
    {
        return $query->where('status', SetupStatus::Completed);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', SetupStatus::InProgress);
    }

    public function scopeByStep($query, string $step)
    {
        return $query->where('step', $step);
    }

    // =======================
    // METHODS
    // =======================

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => SetupStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    public function markAsInProgress(): void
    {
        $this->update([
            'status' => SetupStatus::InProgress,
            'completed_at' => null,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === SetupStatus::Completed;
    }

    public function isInProgress(): bool
    {
        return $this->status === SetupStatus::InProgress;
    }

    public function getProgressPercentage(): int
    {
        return match ($this->status) {
            SetupStatus::NotStarted => 0,
            SetupStatus::InProgress => 50,
            SetupStatus::Completed => 100,
            SetupStatus::RequiresUpdate => 75,
        };
    }

    // =======================
    // STATIC METHODS
    // =======================

    public static function getSetupSteps(): array
    {
        return [
            'basic_info' => 'Información Básica',
            'contact_info' => 'Información de Contacto',
            'regional_config' => 'Configuración Regional',
            'volleyball_rules' => 'Reglas de Voleibol',
            'categories' => 'Categorías',
            'admin_users' => 'Usuarios Administrativos',
            'review' => 'Revisión y Finalización',
        ];
    }

    public static function getOverallProgress(): int
    {
        $steps = self::getSetupSteps();
        $totalSteps = count($steps);
        $completedSteps = self::completed()->count();

        return $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
    }

    public static function isSetupComplete(): bool
    {
        $steps = array_keys(self::getSetupSteps());
        $completedSteps = self::completed()->pluck('step')->toArray();

        return count(array_diff($steps, $completedSteps)) === 0;
    }

    public static function getNextStep(): ?string
    {
        $steps = array_keys(self::getSetupSteps());
        $completedSteps = self::completed()->pluck('step')->toArray();

        foreach ($steps as $step) {
            if (!in_array($step, $completedSteps)) {
                return $step;
            }
        }

        return null;
    }

    // =======================
    // ACTIVITY LOG
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['step', 'status', 'data'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
