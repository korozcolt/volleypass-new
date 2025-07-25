<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Enums\UserStatus;
use App\Models\Player;

class LeagueCategory extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'league_id',
        'name',
        'code',
        'description',
        'gender',
        'min_age',
        'max_age',
        'special_rules',
        'is_active',
        'sort_order',
        'color',
        'icon',
    ];

    protected $casts = [
        'special_rules' => 'array',
        'is_active' => 'boolean',
        'min_age' => 'integer',
        'max_age' => 'integer',
        'sort_order' => 'integer',
    ];

    // =======================
    // SPATIE CONFIGURATION
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'gender', 'min_age', 'max_age', 'is_active'])
            ->logOnlyDirty();
    }

    // =======================
    // RELATIONSHIPS
    // =======================

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function players()
    {
        return $this->hasManyThrough(
            Player::class,
            Club::class,
            'league_id', // Foreign key en clubs table
            'current_club_id', // Foreign key en players table
            'league_id', // Local key en league_categories table
            'id' // Local key en clubs table
        )->whereHas('user', function($query) {
            $query->where('status', UserStatus::Active)
                ->whereRaw('(julianday("now") - julianday(birth_date)) / 365.25 BETWEEN ? AND ?', [$this->min_age, $this->max_age]);
            
            // Filtrar por género si no es mixed
            if ($this->gender !== 'mixed') {
                $query->where('gender', $this->gender);
            }
        });
    }

    // =======================
    // SCOPES
    // =======================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeForAge(Builder $query, int $age): Builder
    {
        return $query->where('min_age', '<=', $age)
                    ->where('max_age', '>=', $age);
    }

    public function scopeForGender(Builder $query, string $gender): Builder
    {
        return $query->where(function ($q) use ($gender) {
            $q->where('gender', 'mixed')
              ->orWhere('gender', $gender);
        });
    }

    public function scopeForLeague(Builder $query, int $leagueId): Builder
    {
        return $query->where('league_id', $leagueId);
    }

  // =======================
    // ACCESSORS & MUTATORS
    // =======================

    protected function ageRange(): Attribute
    {
        return Attribute::make(
            get: fn () => [$this->min_age, $this->max_age]
        );
    }

    protected function ageRangeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->min_age}-{$this->max_age} años"
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->name} ({$this->age_range_label})"
        );
    }

    protected function genderLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->gender) {
                'male' => 'Masculino',
                'female' => 'Femenino',
                'mixed' => 'Mixto',
                default => 'Mixto'
            }
        );
    }

    // =======================
    // BUSINESS METHODS
    // =======================

    /**
     * Verifica si una edad es elegible para esta categoría
     */
    public function isAgeEligible(int $age): bool
    {
        return $age >= $this->min_age && $age <= $this->max_age;
    }

    /**
     * Obtiene el texto del rango de edad
     */
    public function getAgeRangeText(): string
    {
        return "{$this->min_age}-{$this->max_age} años";
    }

    /**
     * Verifica si tiene reglas especiales
     */
    public function hasSpecialRules(): bool
    {
        return !empty($this->special_rules);
    }

    /**
     * Verifica si una jugadora es elegible para esta categoría
     */
    public function isEligibleForPlayer(int $age, string $gender): bool
    {
        // Verificar rango de edad
        if (!$this->isAgeEligible($age)) {
            return false;
        }

        // Verificar género
        if ($this->gender !== 'mixed' && $this->gender !== $gender) {
            return false;
        }

        // Verificar reglas especiales si existen
        if ($this->hasSpecialRules()) {
            return $this->validateSpecialRules($age, $gender);
        }

        return true;
    }

    /**
     * Valida reglas especiales personalizadas
     */
    protected function validateSpecialRules(int $age, string $gender): bool
    {
        if (!$this->special_rules) {
            return true;
        }

        foreach ($this->special_rules as $rule) {
            if (!$this->evaluateRule($rule, $age, $gender)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evalúa una regla especial individual
     */
    protected function evaluateRule(array $rule, int $age, string $gender): bool
    {
        $type = $rule['type'] ?? '';

        return match($type) {
            'age_exception' => $this->evaluateAgeException($rule, $age),
            'gender_restriction' => $this->evaluateGenderRestriction($rule, $gender),
            'combined_rule' => $this->evaluateCombinedRule($rule, $age, $gender),
            default => true
        };
    }

    protected function evaluateAgeException(array $rule, int $age): bool
    {
        $exceptions = $rule['exceptions'] ?? [];
        return !in_array($age, $exceptions);
    }

    protected function evaluateGenderRestriction(array $rule, string $gender): bool
    {
        $allowedGenders = $rule['allowed_genders'] ?? ['male', 'female'];
        return in_array($gender, $allowedGenders);
    }

    protected function evaluateCombinedRule(array $rule, int $age, string $gender): bool
    {
        $conditions = $rule['conditions'] ?? [];

        foreach ($conditions as $condition) {
            if (!$this->evaluateCondition($condition, $age, $gender)) {
                return false;
            }
        }

        return true;
    }

    protected function evaluateCondition(array $condition, int $age, string $gender): bool
    {
        $field = $condition['field'] ?? '';
        $operator = $condition['operator'] ?? '=';
        $value = $condition['value'] ?? null;

        $fieldValue = match($field) {
            'age' => $age,
            'gender' => $gender,
            default => null
        };

        return match($operator) {
            '=' => $fieldValue == $value,
            '!=' => $fieldValue != $value,
            '>' => $fieldValue > $value,
            '<' => $fieldValue < $value,
            '>=' => $fieldValue >= $value,
            '<=' => $fieldValue <= $value,
            'in' => in_array($fieldValue, (array) $value),
            'not_in' => !in_array($fieldValue, (array) $value),
            default => true
        };
    }

    /**
     * Obtiene estadísticas de jugadoras en esta categoría
     */
    public function getPlayerStats(): array
    {
        $players = $this->league->players()
            ->whereHas('user', function($query) {
                $query->where('status', UserStatus::Active);
            })
            ->get();

        $stats = [
            'total' => 0,
            'male' => 0,
            'female' => 0,
            'by_age' => []
        ];

        foreach ($players as $player) {
            $age = $player->user->age ?? 0;
            $gender = $player->user->gender ?? 'unknown';

            if ($this->isEligibleForPlayer($age, $gender)) {
                $stats['total']++;

                if ($gender === 'male') {
                    $stats['male']++;
                } elseif ($gender === 'female') {
                    $stats['female']++;
                }

                $stats['by_age'][$age] = ($stats['by_age'][$age] ?? 0) + 1;
            }
        }

        return $stats;
    }

    /**
     * Verifica si hay superposición con otra categoría
     */
    public function hasOverlapWith(LeagueCategory $other): bool
    {
        // Si son de diferentes ligas, no hay superposición
        if ($this->league_id !== $other->league_id) {
            return false;
        }

        // Verificar superposición de rangos de edad
        $ageOverlap = !($this->max_age < $other->min_age || $this->min_age > $other->max_age);

        // Verificar superposición de género
        $genderOverlap = ($this->gender === 'mixed' || $other->gender === 'mixed' || $this->gender === $other->gender);

        return $ageOverlap && $genderOverlap;
    }
   // =======================
    // STATIC METHODS
    // =======================

    /**
     * Valida la configuración completa de categorías de una liga
     */
    public static function validateLeagueConfiguration(int $leagueId): array
    {
        $categories = static::where('league_id', $leagueId)
            ->active()
            ->ordered()
            ->get();

        $errors = [];
        $warnings = [];

        // Verificar que hay al menos una categoría
        if ($categories->isEmpty()) {
            $errors[] = 'La liga debe tener al menos una categoría configurada';
            return compact('errors', 'warnings');
        }

        // Verificar superposiciones
        foreach ($categories as $i => $category) {
            foreach ($categories->slice($i + 1) as $other) {
                if ($category->hasOverlapWith($other)) {
                    $warnings[] = "Las categorías '{$category->name}' y '{$other->name}' tienen superposición de rangos";
                }
            }
        }

        // Verificar gaps en rangos de edad
        $sortedCategories = $categories->sortBy('min_age');
        $previousMaxAge = null;

        foreach ($sortedCategories as $category) {
            if ($previousMaxAge !== null && $category->min_age > $previousMaxAge + 1) {
                $gapStart = $previousMaxAge + 1;
                $gapEnd = $category->min_age - 1;
                $warnings[] = "Hay un gap en las edades {$gapStart}-{$gapEnd} que no está cubierto por ninguna categoría";
            }
            $previousMaxAge = max($previousMaxAge ?? 0, $category->max_age);
        }

        return compact('errors', 'warnings');
    }

    /**
     * Encuentra la mejor categoría para una jugadora
     */
    public static function findBestCategoryForPlayer(int $leagueId, int $age, string $gender): ?static
    {
        return static::where('league_id', $leagueId)
            ->active()
            ->forAge($age)
            ->forGender($gender)
            ->ordered()
            ->first();
    }

    /**
     * Crea categorías por defecto para una liga
     */
    public static function createDefaultCategoriesForLeague(int $leagueId): void
    {
        $defaultCategories = [
            [
                'name' => 'Mini',
                'code' => 'MINI',
                'description' => 'Categoría Mini',
                'gender' => 'mixed',
                'min_age' => 8,
                'max_age' => 10,
                'sort_order' => 1,
                'color' => '#ec4899',
                'icon' => 'heroicon-o-heart',
            ],
            [
                'name' => 'Pre-Mini',
                'code' => 'PRE_MINI',
                'description' => 'Categoría Pre-Mini',
                'gender' => 'mixed',
                'min_age' => 11,
                'max_age' => 12,
                'sort_order' => 2,
                'color' => '#8b5cf6',
                'icon' => 'heroicon-o-star',
            ],
            [
                'name' => 'Infantil',
                'code' => 'INFANTIL',
                'description' => 'Categoría Infantil',
                'gender' => 'mixed',
                'min_age' => 13,
                'max_age' => 14,
                'sort_order' => 3,
                'color' => '#3b82f6',
                'icon' => 'heroicon-o-sparkles',
            ],
            [
                'name' => 'Cadete',
                'code' => 'CADETE',
                'description' => 'Categoría Cadete',
                'gender' => 'mixed',
                'min_age' => 15,
                'max_age' => 16,
                'sort_order' => 4,
                'color' => '#f59e0b',
                'icon' => 'heroicon-o-fire',
            ],
            [
                'name' => 'Juvenil',
                'code' => 'JUVENIL',
                'description' => 'Categoría Juvenil',
                'gender' => 'mixed',
                'min_age' => 17,
                'max_age' => 18,
                'sort_order' => 5,
                'color' => '#10b981',
                'icon' => 'heroicon-o-bolt',
            ],
            [
                'name' => 'Mayores',
                'code' => 'MAYORES',
                'description' => 'Categoría Mayores',
                'gender' => 'mixed',
                'min_age' => 19,
                'max_age' => 34,
                'sort_order' => 6,
                'color' => '#6366f1',
                'icon' => 'heroicon-o-trophy',
            ],
            [
                'name' => 'Masters',
                'code' => 'MASTERS',
                'description' => 'Categoría Masters',
                'gender' => 'mixed',
                'min_age' => 35,
                'max_age' => 100,
                'sort_order' => 7,
                'color' => '#6b7280',
                'icon' => 'heroicon-o-academic-cap',
            ],
        ];

        foreach ($defaultCategories as $categoryData) {
            $categoryData['league_id'] = $leagueId;
            static::create($categoryData);
        }
    }
}
