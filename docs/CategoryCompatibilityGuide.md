# Guía de Compatibilidad del Sistema de Categorías Dinámicas

## Introducción

Esta guía explica cómo migrar código existente para usar el nuevo sistema de categorías dinámicas manteniendo compatibilidad total con el código legacy.

## Opciones de Migración

### 1. Usando el Facade (Recomendado)

El facade `CategoryCompatibility` proporciona acceso directo a todas las funcionalidades:

```php
use App\Facades\CategoryCompatibility;

// Código anterior
$category = PlayerCategory::getForAge($age, $gender);

// Código nuevo (compatible con sistema dinámico)
$category = CategoryCompatibility::getCategoryForAge($age, $gender, $league);

// Verificar elegibilidad
$isEligible = CategoryCompatibility::isAgeEligibleForCategory($age, PlayerCategory::Mini, $league);

// Obtener opciones para formularios
$options = CategoryCompatibility::getCategoryOptions($league);
```

### 2. Usando el Trait

Para modelos que necesitan funcionalidad de categorías:

```php
use App\Traits\HasDynamicCategories;

class MyModel extends Model
{
    use HasDynamicCategories;
    
    public function assignCategory($age, $gender, $league = null)
    {
        $category = $this->getCategoryForAge($age, $gender, $league);
        // ... lógica de asignación
    }
}
```

### 3. Usando el Servicio Directamente

Para casos más complejos:

```php
use App\Services\CategoryCompatibilityService;

class MyService
{
    public function __construct(
        private CategoryCompatibilityService $categoryService
    ) {}
    
    public function processPlayer($player)
    {
        $category = $this->categoryService->getCategoryForPlayer($player);
        // ... lógica de procesamiento
    }
}
```

## Ejemplos de Migración

### Formularios Filament

**Antes:**
```php
Forms\Components\Select::make('category')
    ->options(PlayerCategory::class)
```

**Después:**
```php
Forms\Components\Select::make('category')
    ->options(function () {
        $league = $this->getRecord()?->league; // Obtener liga del contexto
        return CategoryCompatibility::getCategoryOptions($league);
    })
```

### Validaciones

**Antes:**
```php
public function validateCategory($age, $category)
{
    $enum = PlayerCategory::from($category);
    return $enum->isAgeEligible($age);
}
```

**Después:**
```php
public function validateCategory($age, $category, $league = null)
{
    $enum = CategoryCompatibility::getCategoryEnum($category);
    if (!$enum) return false;
    
    return CategoryCompatibility::isAgeEligibleForCategory($age, $enum, $league);
}
```

### Reportes y Estadísticas

**Antes:**
```php
public function getCategoryStats()
{
    $stats = [];
    foreach (PlayerCategory::cases() as $category) {
        $stats[$category->getLabel()] = $this->getPlayerCount($category);
    }
    return $stats;
}
```

**Después:**
```php
public function getCategoryStats($league = null)
{
    return CategoryCompatibility::getCategoryStats($league);
}
```

## Detección Automática del Sistema

El sistema detecta automáticamente si debe usar categorías dinámicas o tradicionales:

```php
// Verificar qué sistema está activo
if (CategoryCompatibility::isDynamicSystemActive($league)) {
    // Lógica específica para sistema dinámico
    $categories = $league->getActiveCategories();
} else {
    // Lógica tradicional
    $categories = PlayerCategory::cases();
}

// Obtener información de compatibilidad
$info = CategoryCompatibility::getCompatibilityInfo($league);
// Resultado: ['system_mode' => 'dynamic|traditional', ...]
```

## Migración de Jugadores

Cuando se cambia la configuración de categorías de una liga:

```php
// Migrar automáticamente todas las jugadoras de la liga
$results = CategoryCompatibility::migratePlayersCategories($league);

// Resultado:
// [
//     'migrated' => 15,
//     'errors' => 0,
//     'unchanged' => 5,
//     'details' => [...]
// ]
```

## Fallback Automático

El sistema siempre tiene fallback al comportamiento tradicional:

```php
// Si no hay liga o no tiene categorías personalizadas
$category = CategoryCompatibility::getCategoryForAge(9, 'female'); // Usa enum tradicional

// Si hay liga con categorías personalizadas
$category = CategoryCompatibility::getCategoryForAge(9, 'female', $league); // Usa configuración dinámica

// Si la configuración dinámica no mapea al enum, usa fallback
$category = CategoryCompatibility::getCategoryForAge(9, 'female', $leagueWithCustomCategories); // Puede retornar null o fallback
```

## Mejores Prácticas

### 1. Siempre Pasar la Liga Cuando Esté Disponible

```php
// ✅ Correcto
$category = CategoryCompatibility::getCategoryForAge($age, $gender, $player->league);

// ❌ Incorrecto (pierde funcionalidad dinámica)
$category = CategoryCompatibility::getCategoryForAge($age, $gender);
```

### 2. Verificar el Sistema Activo para Lógica Específica

```php
if (CategoryCompatibility::isDynamicSystemActive($league)) {
    // Lógica que solo aplica al sistema dinámico
    $customRules = $league->categories()->where('has_special_rules', true)->get();
}
```

### 3. Usar Información de Compatibilidad para Debugging

```php
$info = CategoryCompatibility::getCompatibilityInfo($league);
Log::info('Category system info', $info);
```

### 4. Manejar Casos Edge

```php
$category = CategoryCompatibility::getCategoryForAge($age, $gender, $league);

if (!$category) {
    // Manejar caso donde no hay categoría válida
    // Esto puede pasar con configuraciones dinámicas complejas
    $category = PlayerCategory::getTraditionalCategoryForAge($age);
}
```

## Testing

Para tests, usar los mismos patrones:

```php
public function test_category_assignment_with_dynamic_system()
{
    // Crear liga con categorías personalizadas
    $league = League::factory()->create();
    LeagueCategory::factory()->create([
        'league_id' => $league->id,
        'code' => 'MINI',
        'min_age' => 6,
        'max_age' => 9,
    ]);
    
    // Test usando facade
    $category = CategoryCompatibility::getCategoryForAge(8, 'female', $league);
    $this->assertEquals(PlayerCategory::Mini, $category);
}
```

## Troubleshooting

### Problema: Categoría Retorna Null

```php
$category = CategoryCompatibility::getCategoryForAge($age, $gender, $league);
if (!$category) {
    $info = CategoryCompatibility::getCompatibilityInfo($league);
    Log::warning('No category found', [
        'age' => $age,
        'gender' => $gender,
        'league_id' => $league?->id,
        'system_info' => $info
    ]);
}
```

### Problema: Migración de Jugadores Falla

```php
$results = CategoryCompatibility::migratePlayersCategories($league);
if ($results['errors'] > 0) {
    foreach ($results['details'] as $detail) {
        if ($detail['action'] === 'error') {
            Log::error('Migration error', $detail);
        }
    }
}
```

## Conclusión

La capa de compatibilidad permite una migración gradual y segura al nuevo sistema de categorías dinámicas, manteniendo toda la funcionalidad existente mientras se agregan las nuevas capacidades.
