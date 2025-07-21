# Guía de Migración a Categorías Dinámicas

Esta guía explica cómo migrar el sistema existente al nuevo sistema de categorías dinámicas.

## Contexto

Este sistema está en desarrollo y no contiene datos críticos, por lo que podemos ser más agresivos en las migraciones sin preocuparnos por la pérdida de datos históricos.

## Comandos Disponibles

### 1. Migración Completa (Recomendado para desarrollo)

```bash
php artisan categories:migrate --fresh
```

**¿Qué hace?**
- Elimina TODAS las categorías personalizadas existentes
- Resetea las categorías de todos los jugadores
- Crea categorías por defecto para todas las ligas activas
- Reasigna automáticamente todos los jugadores a sus nuevas categorías

**Cuándo usar:** Cuando quieres empezar completamente de cero.

### 2. Migración Selectiva

```bash
# Migrar ligas específicas
php artisan categories:migrate --league=1 --league=2

# Migración normal (sin limpiar datos existentes)
php artisan categories:migrate
```

### 3. Configuración Adicional

```bash
# Modo desarrollo (sin confirmaciones)
php artisan categories:setup --dev

# Configurar ligas específicas
php artisan categories:setup --league=1 --dev

# Ver qué se haría sin ejecutar
php artisan categories:setup --dry-run

# Resetear configuración existente
php artisan categories:setup --reset --dev
```

### 4. Validación del Sistema

```bash
# Validar estado actual
php artisan categories:validate

# Validar y corregir problemas automáticamente
php artisan categories:validate --fix

# Validar ligas específicas
php artisan categories:validate --league=1 --league=2 --fix
```

## Flujo Recomendado para Desarrollo

### Paso 1: Migración Inicial
```bash
php artisan categories:migrate --fresh
```

### Paso 2: Validar Resultado
```bash
php artisan categories:validate
```

### Paso 3: Corregir Problemas (si los hay)
```bash
php artisan categories:validate --fix
```

## Qué Hace Cada Comando

### `categories:migrate`
- **Propósito:** Migración principal del sistema
- **Seguro para desarrollo:** ✅ Sí
- **Destructivo:** Solo con `--fresh`
- **Resultado:** Sistema completamente funcional

### `categories:setup`
- **Propósito:** Configuración granular de ligas
- **Seguro para desarrollo:** ✅ Sí
- **Destructivo:** Solo con `--reset`
- **Resultado:** Categorías configuradas para ligas específicas

### `categories:validate`
- **Propósito:** Diagnóstico y corrección
- **Seguro para desarrollo:** ✅ Sí
- **Destructivo:** No (solo con `--fix` hace cambios)
- **Resultado:** Reporte de estado del sistema

## Categorías por Defecto Creadas

El sistema crea automáticamente estas categorías:

1. **Mini (8-10 años)** - Mixta
2. **Infantil (11-12 años)** - Femenina/Masculina
3. **Cadete (13-14 años)** - Femenina/Masculina
4. **Juvenil (15-17 años)** - Femenina/Masculina
5. **Adulto (18+ años)** - Femenina/Masculina

## Resolución de Problemas Comunes

### Problema: Liga sin categorías
**Solución:**
```bash
php artisan categories:setup --league=ID_LIGA --dev
```

### Problema: Jugadores sin categoría asignada
**Solución:**
```bash
php artisan categories:validate --league=ID_LIGA --fix
```

### Problema: Configuración inconsistente
**Solución:**
```bash
php artisan categories:migrate --league=ID_LIGA
```

### Problema: Quiero empezar de cero
**Solución:**
```bash
php artisan categories:migrate --fresh
```

## Logs y Debugging

Los comandos generan logs detallados en:
- `storage/logs/laravel.log`

Para debugging adicional, puedes revisar:
- Estado de las ligas en el panel de administración
- Tabla `league_categories` en la base de datos
- Campo `category` en la tabla `players`

## Notas Importantes

1. **Entorno de desarrollo:** Estos comandos están optimizados para desarrollo donde no hay datos críticos
2. **Backup:** Aunque no es crítico en desarrollo, siempre puedes hacer backup de la DB antes de migrar
3. **Reversibilidad:** El comando `--fresh` es completamente reversible ejecutándolo nuevamente
4. **Performance:** Los comandos están optimizados para procesar grandes cantidades de datos eficientemente

## Después de la Migración

Una vez completada la migración:

1. ✅ Las ligas tendrán categorías dinámicas configuradas
2. ✅ Los jugadores estarán asignados automáticamente a sus categorías
3. ✅ El panel de administración mostrará las nuevas opciones de gestión
4. ✅ El sistema funcionará completamente con el nuevo modelo

¡El sistema estará listo para usar! 🎉
