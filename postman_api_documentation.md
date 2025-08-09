# VolleyPass API - Documentación para Postman

## Información General

Esta documentación describe cómo usar la API pública de VolleyPass con Postman, incluyendo la configuración necesaria y ejemplos de todas las rutas disponibles.

### URL Base
- **Desarrollo Local**: `http://localhost:8000`
- **Herd (Local)**: `https://volleypass-new.test`
- **Producción**: `[URL_DE_PRODUCCION]`

### Versión de la API
- **Versión actual**: v1
- **Prefijo**: `/api/v1/public`

## Configuración de Postman

### 1. Configuración de SSL (Para Herd)

Si usas la URL de Herd (`https://volleypass-new.test`), necesitas deshabilitar la verificación SSL:

1. Ve a **File > Settings** (o **Postman > Preferences** en Mac)
2. En la pestaña **General**, desactiva **SSL certificate verification**
3. Alternativamente, puedes configurarlo por colección en **Settings > Pre-request Script**

### 2. Variables de Entorno

Crea un entorno en Postman con las siguientes variables:

```json
{
  "base_url": "https://volleypass-new.test",
  "api_version": "v1",
  "api_prefix": "/api/v1/public"
}
```

### 3. Headers Globales

Configura estos headers en tu colección:

```json
{
  "Accept": "application/json",
  "Content-Type": "application/json"
}
```

## Endpoints Disponibles

### 1. Torneos

#### Listar Todos los Torneos
- **Método**: GET
- **URL**: `{{base_url}}{{api_prefix}}/tournaments`
- **Descripción**: Obtiene la lista de todos los torneos públicos
- **Respuesta**: Array de objetos torneo

**Ejemplo de respuesta**:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Torneo Nacional 2024",
      "description": "Descripción del torneo",
      "start_date": "2024-01-15",
      "end_date": "2024-01-20",
      "status": "active"
    }
  ]
}
```

#### Obtener Detalles de un Torneo
- **Método**: GET
- **URL**: `{{base_url}}{{api_prefix}}/tournaments/{id}`
- **Parámetros**: 
  - `id` (path): ID del torneo
- **Descripción**: Obtiene los detalles completos de un torneo específico

**Ejemplo**:
- URL: `{{base_url}}{{api_prefix}}/tournaments/1`

#### Obtener Tabla de Posiciones de un Torneo
- **Método**: GET
- **URL**: `{{base_url}}{{api_prefix}}/tournaments/{id}/standings`
- **Parámetros**: 
  - `id` (path): ID del torneo
- **Descripción**: Obtiene la tabla de posiciones del torneo

**Ejemplo**:
- URL: `{{base_url}}{{api_prefix}}/tournaments/1/standings`

### 2. Partidos

#### Listar Partidos Programados
- **Método**: GET
- **URL**: `{{base_url}}{{api_prefix}}/matches/scheduled`
- **Descripción**: Obtiene todos los partidos programados
- **Respuesta**: Array de partidos con estado "scheduled"

#### Listar Partidos en Vivo
- **Método**: GET
- **URL**: `{{base_url}}{{api_prefix}}/matches/live`
- **Descripción**: Obtiene todos los partidos que están siendo jugados actualmente
- **Respuesta**: Array de partidos con estado "live"

#### Obtener Detalles de un Partido
- **Método**: GET
- **URL**: `{{base_url}}{{api_prefix}}/matches/{id}`
- **Parámetros**: 
  - `id` (path): ID del partido
- **Descripción**: Obtiene los detalles completos de un partido específico

**Ejemplo**:
- URL: `{{base_url}}{{api_prefix}}/matches/1`

### 3. Jugadores de Partido

#### Listar Jugadores de un Partido
- **Método**: GET
- **URL**: `{{base_url}}{{api_prefix}}/matches/{match_id}/players`
- **Parámetros**: 
  - `match_id` (path): ID del partido
- **Descripción**: Obtiene la lista de jugadores participantes en un partido

**Ejemplo**:
- URL: `{{base_url}}{{api_prefix}}/matches/1/players`

### 4. Jugadores de Equipo

#### Listar Jugadores de un Equipo
- **Método**: GET
- **URL**: `{{base_url}}{{api_prefix}}/teams/{team_id}/players`
- **Parámetros**: 
  - `team_id` (path): ID del equipo
- **Descripción**: Obtiene la lista de jugadores de un equipo específico

**Ejemplo**:
- URL: `{{base_url}}{{api_prefix}}/teams/1/players`

## Colección de Postman

### Estructura de la Colección

```
VolleyPass API v1
├── 📁 Tournaments
│   ├── GET List All Tournaments
│   ├── GET Tournament Details
│   └── GET Tournament Standings
├── 📁 Matches
│   ├── GET Scheduled Matches
│   ├── GET Live Matches
│   └── GET Match Details
├── 📁 Players
│   ├── GET Match Players
│   └── GET Team Players
└── 📁 Tests
    └── GET API Health Check
```

### Configuración de Tests

Puedes agregar tests automáticos a cada request:

```javascript
// Test básico para verificar status 200
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

// Test para verificar que la respuesta es JSON
pm.test("Response is JSON", function () {
    pm.response.to.be.json;
});

// Test para verificar estructura de datos
pm.test("Response has data property", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('data');
});
```

## Ejemplos de Uso con cURL

Para verificar que los endpoints funcionan antes de usar Postman:

```bash
# Listar torneos
curl -X GET "https://volleypass-new.test/api/v1/public/tournaments" \
  -H "Accept: application/json" \
  -k

# Obtener detalles de torneo
curl -X GET "https://volleypass-new.test/api/v1/public/tournaments/1" \
  -H "Accept: application/json" \
  -k

# Listar partidos programados
curl -X GET "https://volleypass-new.test/api/v1/public/matches/scheduled" \
  -H "Accept: application/json" \
  -k
```

## Códigos de Respuesta

- **200 OK**: Solicitud exitosa
- **404 Not Found**: Recurso no encontrado
- **422 Unprocessable Entity**: Error de validación
- **500 Internal Server Error**: Error del servidor

## Notas Importantes

1. **SSL en Desarrollo**: Si usas Herd, recuerda deshabilitar la verificación SSL en Postman
2. **Headers**: Siempre incluye `Accept: application/json`
3. **Datos de Prueba**: La API contiene datos de prueba (4 torneos, 32 partidos programados)
4. **Errores Conocidos**: Algunos endpoints de jugadores pueden devolver errores SQL si no hay datos relacionados

## Solución de Problemas

### Error "unable to verify the first certificate"
- **Causa**: Certificado SSL auto-firmado en desarrollo
- **Solución**: Deshabilitar verificación SSL en Postman

### Error 404 "Not Found"
- **Causa**: URL incorrecta o falta el prefijo `/v1`
- **Solución**: Verificar que la URL incluya `/api/v1/public`

### Error de Conexión
- **Causa**: Servidor no está ejecutándose
- **Solución**: Verificar que Laravel esté corriendo con `php artisan serve`

## Contacto y Soporte

Para reportar problemas o solicitar nuevas funcionalidades, contacta al equipo de desarrollo.