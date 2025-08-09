# Configuración de Postman para VolleyPass API

## Archivos Incluidos

Este proyecto incluye los siguientes archivos para Postman:

1. **`VolleyPass_API_Collection.postman_collection.json`** - Colección completa con todos los endpoints
2. **`VolleyPass_Environment.postman_environment.json`** - Entorno con variables predefinidas
3. **`postman_api_documentation.md`** - Documentación detallada de la API

## Pasos de Instalación

### 1. Importar la Colección

1. Abre Postman
2. Haz clic en **"Import"** en la esquina superior izquierda
3. Selecciona **"Upload Files"**
4. Navega hasta tu proyecto y selecciona `VolleyPass_API_Collection.postman_collection.json`
5. Haz clic en **"Import"**

### 2. Importar el Entorno

1. En Postman, haz clic en **"Import"** nuevamente
2. Selecciona **"Upload Files"**
3. Selecciona `VolleyPass_Environment.postman_environment.json`
4. Haz clic en **"Import"**

### 3. Configurar el Entorno

1. En la esquina superior derecha de Postman, selecciona **"VolleyPass Environment"** del dropdown
2. Haz clic en el ícono del ojo 👁️ para ver las variables
3. Verifica que `base_url` esté configurado correctamente:
   - Para Herd: `https://volleypass-new.test`
   - Para desarrollo local: `http://localhost:8000`

### 4. Configurar SSL (Solo para Herd)

Si usas la URL de Herd (`https://volleypass-new.test`):

1. Ve a **File > Settings** (o **Postman > Preferences** en Mac)
2. En la pestaña **General**, desactiva **"SSL certificate verification"**
3. Haz clic en **"Save"**

## Estructura de la Colección

La colección está organizada en las siguientes carpetas:

### 📁 Tournaments
- **List All Tournaments** - `GET /api/v1/public/tournaments`
- **Get Tournament Details** - `GET /api/v1/public/tournaments/{id}`
- **Get Tournament Standings** - `GET /api/v1/public/tournaments/{id}/standings`

### 📁 Matches
- **Get Scheduled Matches** - `GET /api/v1/public/matches/scheduled`
- **Get Live Matches** - `GET /api/v1/public/matches/live`
- **Get Match Details** - `GET /api/v1/public/matches/{id}`

### 📁 Players
- **Get Match Players** - `GET /api/v1/public/matches/{match_id}/players`
- **Get Team Players** - `GET /api/v1/public/teams/{team_id}/players`

### 📁 Health Check
- **API Health Check** - Verificación básica de conectividad

## Variables de Entorno

El entorno incluye las siguientes variables:

| Variable | Valor por Defecto | Descripción |
|----------|-------------------|-------------|
| `base_url` | `https://volleypass-new.test` | URL base de la API |
| `base_url_local` | `http://localhost:8000` | URL local alternativa |
| `api_version` | `v1` | Versión de la API |
| `api_prefix` | `/api/v1/public` | Prefijo completo de la API |
| `tournament_id` | `1` | ID de torneo para pruebas |
| `match_id` | `1` | ID de partido para pruebas |
| `team_id` | `1` | ID de equipo para pruebas |

## Uso de las Variables

Puedes usar las variables en cualquier request de la siguiente manera:

```
{{base_url}}/api/v1/public/tournaments/{{tournament_id}}
```

Esto se expandirá automáticamente a:
```
https://volleypass-new.test/api/v1/public/tournaments/1
```

## Tests Automáticos

Cada request incluye tests automáticos que verifican:

- ✅ Código de estado HTTP correcto
- ✅ Respuesta en formato JSON
- ✅ Estructura de datos esperada
- ✅ Tiempo de respuesta aceptable

## Cambiar entre Entornos

Para cambiar entre desarrollo local y Herd:

1. Haz clic en el dropdown del entorno
2. Selecciona **"Manage Environments"**
3. Edita la variable `base_url`:
   - Para Herd: `https://volleypass-new.test`
   - Para local: `http://localhost:8000`

## Solución de Problemas

### Error "unable to verify the first certificate"
**Solución**: Deshabilita la verificación SSL en Settings > General

### Error 404 "Not Found"
**Solución**: Verifica que:
- El servidor Laravel esté ejecutándose
- La URL incluya el prefijo `/api/v1/public`
- El ID del recurso exista

### Error de Conexión
**Solución**: 
- Verifica que el servidor esté corriendo
- Para local: `php artisan serve --port=8000`
- Para Herd: Verifica que el sitio esté configurado

### Variables no se Expanden
**Solución**: 
- Verifica que el entorno esté seleccionado
- Revisa que las variables estén habilitadas
- Usa la sintaxis correcta: `{{variable_name}}`

## Ejecutar Toda la Colección

Para ejecutar todos los tests de una vez:

1. Haz clic derecho en la colección "VolleyPass API v1"
2. Selecciona **"Run collection"**
3. Configura las opciones de ejecución
4. Haz clic en **"Run VolleyPass API v1"**

Esto ejecutará todos los endpoints y mostrará un reporte completo de los resultados.

## Datos de Prueba Disponibles

La API actualmente contiene:
- ✅ 4 torneos públicos
- ✅ 32 partidos programados
- ⚠️ Datos limitados de jugadores (algunos endpoints pueden fallar)

## Contacto

Para soporte técnico o reportar problemas con la colección de Postman, contacta al equipo de desarrollo.