# Endpoints de Gestión de Categorías de Jugadores

Esta documentación describe los endpoints disponibles para la gestión de categorías de jugadores en la API de VolleyPass.

## Autenticación

Todos los endpoints requieren autenticación mediante token. Asegúrate de incluir el token en el encabezado de la solicitud:

```
Authorization: Bearer {tu_token}
```

## Endpoints Disponibles

### Obtener información de categoría de un jugador

**Endpoint:** `GET /api/v1/players/{player_id}/category`

**Descripción:** Obtiene información detallada sobre la categoría actual de un jugador, incluyendo si está en la categoría correcta según su edad y género.

**Parámetros de ruta:**
- `player_id`: ID del jugador

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "player": {
    "id": 123,
    "name": "Nombre del Jugador",
    "age": 15,
    "gender": "female",
    "current_category": {
      "name": "Cadete",
      "value": "Cadete",
      "color": "#4CAF50",
      "icon": "volleyball",
      "age_range": "14-16 años"
    },
    "is_correct_category": true
  }
}
```

### Actualizar categoría de un jugador

**Endpoint:** `PUT /api/v1/players/{player_id}/category`

**Descripción:** Actualiza la categoría de un jugador. Este endpoint valida la elegibilidad del jugador para la nueva categoría y envía notificaciones automáticas a las partes interesadas.

**Parámetros de ruta:**
- `player_id`: ID del jugador

**Parámetros de solicitud (JSON):**
```json
{
  "category": "Cadete",
  "reason": "Cambio debido a habilidades técnicas excepcionales"
}
```

**Parámetros requeridos:**
- `category`: Debe ser uno de los valores válidos del enum PlayerCategory (Mini, Pre_Mini, Infantil, Cadete, Juvenil, Mayores, Masters)
- `reason`: Motivo del cambio de categoría (máximo 500 caracteres)

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "message": "Categoría actualizada exitosamente",
  "player": {
    "id": 123,
    "name": "Nombre del Jugador",
    "old_category": "Infantil",
    "new_category": "Cadete",
    "reason": "Cambio debido a habilidades técnicas excepcionales"
  }
}
```

**Respuesta de error (422):**
```json
{
  "success": false,
  "message": "El jugador no es elegible para esta categoría",
  "errors": [
    "La edad del jugador (15) no está dentro del rango permitido para la categoría Mayores (17-99)"
  ]
}
```

## Notificaciones

Cuando se actualiza la categoría de un jugador, el sistema envía automáticamente notificaciones a:

1. El jugador
2. El director del club (si el jugador pertenece a un club)
3. El administrador de la liga (si el cambio requiere aprobación especial)

Las notificaciones incluyen detalles sobre el cambio, como la categoría anterior, la nueva categoría y el motivo del cambio.

## Consideraciones de Seguridad

- Solo los usuarios con los roles adecuados (LeagueAdmin, ClubDirector, SuperAdmin) pueden actualizar categorías de jugadores.
- Todas las actualizaciones de categoría se registran para auditoría.
- El sistema valida la elegibilidad del jugador para la nueva categoría según las reglas configuradas en la liga.