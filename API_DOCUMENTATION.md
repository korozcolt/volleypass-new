# VolleyPass API Documentation

## Información General

**Base URL:** `http://volleypass-new.test/api`

**Versión:** v1

**Formato de respuesta:** JSON

**Autenticación:** Bearer Token (Laravel Sanctum)

---

## Autenticación

### Headers requeridos para endpoints autenticados

```http
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

### Obtener Token de Autenticación

**POST** `/auth/login`

**Body:**

```json
{
  "email": "usuario@ejemplo.com",
  "password": "contraseña"
}
```

**Response (200):**

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Juan Pérez",
      "email": "usuario@ejemplo.com",
      "role": "player"
    },
    "token": "1|abcdef123456..."
  }
}
```

**Response (401):**

```json
{
  "success": false,
  "message": "Credenciales inválidas"
}
```

### Cerrar Sesión

**POST** `/auth/logout`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "message": "Sesión cerrada exitosamente"
}
```

---

## Endpoints Públicos

### Obtener Tabla de Posiciones de Torneo

**GET** `/v1/public/tournaments/{id}/standings`

**Descripción:** Obtiene la tabla de posiciones general de un torneo.

**Response (200):**

```json
{
  "tournament": {
    "id": 1,
    "name": "Liga Nacional 2024",
    "status": "in_progress"
  },
  "standings": {
    "overall_standings": [
      {
        "position": 1,
        "team": {
          "id": 1,
          "name": "Equipo A",
          "club": {
            "name": "Club Deportivo ABC"
          }
        },
        "points": 15,
        "matches_played": 5,
        "wins": 5,
        "losses": 0,
        "sets_won": 15,
        "sets_lost": 3
      }
    ]
  }
}
```

### Obtener Tabla de Posiciones por Grupo

**GET** `/v1/public/tournaments/{id}/groups/{groupId}/standings`

**Descripción:** Obtiene la tabla de posiciones de un grupo específico dentro de un torneo.

**Response (200):**

```json
{
  "tournament": {
    "id": 1,
    "name": "Liga Nacional 2024"
  },
  "group": {
    "id": 1,
    "name": "Grupo A",
    "group_letter": "A"
  },
  "standings": [
    {
      "position": 1,
      "team": {
        "id": 1,
        "name": "Equipo A"
      },
      "points": 9,
      "matches_played": 3,
      "wins": 3,
      "losses": 0
    }
  ],
  "matches_progress": {
    "total_matches": 6,
    "completed_matches": 3,
    "pending_matches": 3,
    "completion_percentage": 50
  }
}
```

### Obtener Jugadores de un Partido

**GET** `/v1/public/matches/{id}/players`

**Descripción:** Obtiene todos los jugadores y oficiales de un partido específico.

**Response (200):**

```json
{
  "match": {
    "id": 1,
    "date": "2024-01-15",
    "time": "19:00:00",
    "status": "scheduled"
  },
  "teams": [
    {
      "id": 1,
      "name": "Equipo Local",
      "club": {
        "name": "Club Deportivo ABC"
      }
    },
    {
      "id": 2,
      "name": "Equipo Visitante",
      "club": {
        "name": "Club Deportivo XYZ"
      }
    }
  ],
  "players": [
    {
      "id": 1,
      "jersey_number": 10,
      "position": "Libero",
      "team_id": 1,
      "user": {
        "name": "Juan Pérez"
      }
    }
  ],
  "officials": [
    {
      "id": 1,
      "role": "referee",
      "user": {
        "name": "Carlos Referee"
      }
    }
  ]
}
```

### Obtener Jugadores de un Equipo en un Partido

**GET** `/v1/public/matches/{id}/teams/{teamId}/players`

**Descripción:** Obtiene los jugadores de un equipo específico en un partido.

**Response (200):**

```json
{
  "match": {
    "id": 1,
    "date": "2024-01-15",
    "time": "19:00:00"
  },
  "team": {
    "id": 1,
    "name": "Equipo Local",
    "club": {
      "name": "Club Deportivo ABC"
    }
  },
  "players": [
    {
      "id": 1,
      "jersey_number": 10,
      "position": "Libero",
      "user": {
        "name": "Juan Pérez"
      }
    }
  ]
}
```

---

## Endpoints de Perfiles de Usuario

### Obtener Perfil Propio

**GET** `/users/profile`

**Headers:** Authorization requerido

**Descripción:** Obtiene el perfil completo del usuario autenticado. La respuesta varía según el tipo de usuario (jugador, entrenador, árbitro, etc.).

**Response (200):**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "first_name": "Juan",
    "last_name": "Pérez",
    "phone": "+57 300 123 4567",
    "birth_date": "1990-05-15",
    "gender": "male",
    "address": "Calle 123 #45-67",
    "roles": ["Player"],
    "user_type": "player",
    "profile": {
      "nickname": "Juancho",
      "bio": "Jugador de voleibol desde 2010",
      "avatar_url": "https://example.com/avatar.jpg",
      "emergency_contact_name": "María Pérez",
      "emergency_contact_phone": "+57 300 987 6543",
      "emergency_contact_relationship": "Madre",
      "blood_type": "O+",
      "allergies": "Ninguna conocida",
      "medical_conditions": "Ninguna",
      "t_shirt_size": "M",
      "social_media": {
        "instagram": "@juancho_volley",
        "facebook": "Juan Pérez"
      }
    },
    "player_info": {
      "position": "Libero",
      "jersey_number": 10,
      "height": 1.75,
      "weight": 70.5,
      "current_club": "Club Deportivo ABC",
      "category": "Senior",
      "years_playing": 12
    },
    "location": {
      "city": "Bogotá",
      "department": "Cundinamarca",
      "country": "Colombia"
    }
  }
}
```

**Nota:** La sección `player_info` solo aparece si el usuario es un jugador. Para otros tipos de usuario (entrenadores, árbitros, administradores), esta sección no se incluye.

### Actualizar Perfil Propio

**PUT** `/users/profile`

**Headers:** Authorization requerido

**Body:**

```json
{
  "first_name": "Juan",
  "last_name": "Pérez",
  "phone": "+57 300 123 4567",
  "address": "Calle 123 #45-67",
  "nickname": "Juancho",
  "bio": "Jugador de voleibol desde 2010",
  "emergency_contact_name": "María Pérez",
  "emergency_contact_phone": "+57 300 987 6543",
  "emergency_contact_relationship": "Madre",
  "blood_type": "O+",
  "allergies": "Ninguna conocida",
  "medical_conditions": "Ninguna",
  "t_shirt_size": "M",
  "social_media": {
    "instagram": "@juancho_volley",
    "facebook": "Juan Pérez"
  }
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Perfil actualizado exitosamente",
  "data": {
    // ... datos actualizados del usuario
  }
}
```

### Obtener Perfil Público de Usuario

**GET** `/users/{userId}/profile`

**Headers:** Authorization requerido

**Descripción:** Obtiene el perfil público de cualquier usuario. Solo muestra información que el usuario ha configurado como pública.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Juan Pérez",
    "first_name": "Juan",
    "last_name": "Pérez",
    "user_type": "player",
    "profile": {
      "nickname": "Juancho",
      "bio": "Jugador de voleibol desde 2010",
      "avatar_url": "https://example.com/avatar.jpg"
    },
    "player_info": {
      "position": "Libero",
      "jersey_number": 10,
      "current_club": "Club Deportivo ABC",
      "category": "Senior"
    },
    "location": {
      "city": "Bogotá",
      "department": "Cundinamarca",
      "country": "Colombia"
    }
  }
}
```

**Nota:** Los perfiles públicos solo muestran información limitada basada en la configuración de privacidad del usuario.

## Endpoints de Jugadores (Estadísticas)

### Obtener Estadísticas del Jugador

**GET** `/players/{id}/stats`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": {
    "matches_played": 25,
    "sets_played": 89,
    "points_scored": 156,
    "aces": 23,
    "blocks": 45,
    "attacks": {
      "total": 234,
      "successful": 189,
      "percentage": 80.8
    },
    "serves": {
      "total": 145,
      "successful": 132,
      "percentage": 91.0
    }
  }
}
```

---

## Endpoints de Partidos

### Obtener Lista de Partidos

**GET** `/matches`

**Query Parameters:**

- `status` (opcional): `scheduled`, `in_progress`, `completed`, `cancelled`
- `team_id` (opcional): ID del equipo
- `tournament_id` (opcional): ID del torneo
- `date_from` (opcional): Fecha desde (YYYY-MM-DD)
- `date_to` (opcional): Fecha hasta (YYYY-MM-DD)
- `page` (opcional): Número de página
- `per_page` (opcional): Elementos por página (máx. 50)

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "home_team": {
        "id": 1,
        "name": "Equipo Local",
        "logo": "url_del_logo"
      },
      "away_team": {
        "id": 2,
        "name": "Equipo Visitante",
        "logo": "url_del_logo"
      },
      "status": "scheduled",
      "scheduled_at": "2024-01-15T19:00:00Z",
      "venue": {
        "id": 1,
        "name": "Coliseo Principal",
        "address": "Calle 123 #45-67"
      },
      "tournament": {
        "id": 1,
        "name": "Liga Nacional 2024"
      },
      "home_sets": 0,
      "away_sets": 0
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_items": 48,
    "per_page": 10
  }
}
```

### Obtener Detalles de un Partido

**GET** `/matches/{id}`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "home_team": {
      "id": 1,
      "name": "Equipo Local",
      "logo": "url_del_logo",
      "players": [
        {
          "id": 1,
          "name": "Juan Pérez",
          "jersey_number": 10,
          "position": "outside_hitter"
        }
      ]
    },
    "away_team": {
      "id": 2,
      "name": "Equipo Visitante",
      "logo": "url_del_logo",
      "players": []
    },
    "status": "in_progress",
    "scheduled_at": "2024-01-15T19:00:00Z",
    "started_at": "2024-01-15T19:05:00Z",
    "venue": {
      "id": 1,
      "name": "Coliseo Principal",
      "address": "Calle 123 #45-67"
    },
    "tournament": {
      "id": 1,
      "name": "Liga Nacional 2024"
    },
    "home_sets": 2,
    "away_sets": 1,
    "current_set": 4,
    "sets": [
      {
        "set_number": 1,
        "home_score": 25,
        "away_score": 23,
        "status": "completed",
        "duration_minutes": 28
      },
      {
        "set_number": 2,
        "home_score": 25,
        "away_score": 20,
        "status": "completed",
        "duration_minutes": 25
      },
      {
        "set_number": 3,
        "home_score": 22,
        "away_score": 25,
        "status": "completed",
        "duration_minutes": 32
      },
      {
        "set_number": 4,
        "home_score": 15,
        "away_score": 12,
        "status": "in_progress",
        "duration_minutes": null
      }
    ],
    "referees": [
      {
        "id": 1,
        "name": "Carlos Referee",
        "type": "main"
      }
    ]
  }
}
```

### Obtener Partidos en Vivo

**GET** `/matches/live`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "home_team": "Equipo Local",
      "away_team": "Equipo Visitante",
      "status": "in_progress",
      "current_set": 3,
      "started_at": "2024-01-15T19:05:00Z",
      "score_summary": {
        "sets": {
          "home": 1,
          "away": 1
        },
        "current_set": {
          "number": 3,
          "home_score": 18,
          "away_score": 15
        }
      }
    }
  ]
}
```

---

## Endpoints de Equipos

### Obtener Lista de Equipos

**GET** `/teams`

**Query Parameters:**

- `club_id` (opcional): ID del club
- `category_id` (opcional): ID de la categoría
- `search` (opcional): Búsqueda por nombre

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Equipo Femenino A",
      "logo": "url_del_logo",
      "club": {
        "id": 1,
        "name": "Club Deportivo"
      },
      "category": {
        "id": 1,
        "name": "Mayores Femenino"
      },
      "players_count": 12,
      "coach": {
        "id": 1,
        "name": "Ana Coach"
      }
    }
  ]
}
```

### Obtener Detalles de un Equipo

**GET** `/teams/{id}`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Equipo Femenino A",
    "logo": "url_del_logo",
    "club": {
      "id": 1,
      "name": "Club Deportivo",
      "logo": "url_del_logo"
    },
    "category": {
      "id": 1,
      "name": "Mayores Femenino",
      "min_age": 18,
      "max_age": null,
      "gender": "female"
    },
    "coach": {
      "id": 1,
      "name": "Ana Coach",
      "email": "coach@ejemplo.com",
      "phone": "+57 300 123 4567"
    },
    "players": [
      {
        "id": 1,
        "name": "María Jugadora",
        "jersey_number": 1,
        "position": "setter",
        "is_captain": true,
        "federation_status": "federated"
      }
    ],
    "statistics": {
      "matches_played": 15,
      "wins": 12,
      "losses": 3,
      "sets_won": 38,
      "sets_lost": 15
    }
  }
}
```

---

## Endpoints de Torneos

### Obtener Lista de Torneos

**GET** `/tournaments`

**Query Parameters:**

- `status` (opcional): `upcoming`, `active`, `completed`
- `league_id` (opcional): ID de la liga
- `category_id` (opcional): ID de la categoría

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Liga Nacional 2024",
      "description": "Torneo nacional de voleibol",
      "status": "active",
      "start_date": "2024-01-01",
      "end_date": "2024-06-30",
      "league": {
        "id": 1,
        "name": "Liga Nacional"
      },
      "categories": [
        {
          "id": 1,
          "name": "Mayores Femenino"
        }
      ],
      "teams_count": 12,
      "matches_count": 66
    }
  ]
}
```

### Obtener Tabla de Posiciones

**GET** `/tournaments/{id}/standings`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "position": 1,
      "team": {
        "id": 1,
        "name": "Equipo A",
        "logo": "url_del_logo"
      },
      "matches_played": 10,
      "wins": 8,
      "losses": 2,
      "sets_won": 26,
      "sets_lost": 12,
      "points_for": 658,
      "points_against": 542,
      "points": 24,
      "percentage": 80.0
    }
  ]
}
```

---

## Endpoints de Sanciones

### Obtener Sanciones del Jugador

**GET** `/sanctions/player`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "yellow_card",
      "severity": "minor",
      "status": "active",
      "reason": "Conducta antideportiva",
      "applied_at": "2024-01-15T20:30:00Z",
      "expires_at": "2024-01-22T20:30:00Z",
      "match": {
        "id": 1,
        "home_team": "Equipo Local",
        "away_team": "Equipo Visitante",
        "date": "2024-01-15"
      },
      "applied_by": "Carlos Referee",
      "appeal_reason": null,
      "appeal_date": null
    }
  ]
}
```

### Apelar una Sanción

**POST** `/sanctions/{id}/appeal`

**Headers:** Authorization requerido

**Body:**

```json
{
  "reason": "La sanción fue aplicada incorrectamente. El jugador no cometió la falta descrita."
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Apelación registrada exitosamente"
}
```

### Obtener Sanciones de un Partido

**GET** `/sanctions/match/{matchId}`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "player": {
        "id": 1,
        "name": "Juan Pérez",
        "jersey_number": 10
      },
      "team": {
        "id": 1,
        "name": "Equipo Local"
      },
      "type": "yellow_card",
      "severity": "minor",
      "status": "active",
      "reason": "Conducta antideportiva",
      "applied_at": "2024-01-15T20:30:00Z",
      "applied_by": "Carlos Referee",
      "expires_at": "2024-01-22T20:30:00Z",
      "appeal_reason": null,
      "appeal_date": null
    }
  ]
}
```

---

## Endpoints de Rotaciones

### Obtener Rotación Actual

**GET** `/rotation/{matchId}/current`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": {
    "match_id": 1,
    "set_number": 3,
    "home_rotation": [
      {
        "position": 1,
        "player": {
          "id": 1,
          "name": "Juan Pérez",
          "jersey_number": 10
        }
      },
      {
        "position": 2,
        "player": {
          "id": 2,
          "name": "María García",
          "jersey_number": 5
        }
      }
    ],
    "away_rotation": [
      {
        "position": 1,
        "player": {
          "id": 15,
          "name": "Ana López",
          "jersey_number": 8
        }
      }
    ]
  }
}
```

### Actualizar Rotación

**POST** `/rotation/{matchId}/update`

**Headers:** Authorization requerido

**Body:**

```json
{
  "team": "home",
  "rotations": [
    {
      "position": 1,
      "player_id": 1
    },
    {
      "position": 2,
      "player_id": 2
    }
  ]
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Rotación actualizada exitosamente",
  "data": {
    // ... rotación actualizada
  }
}
```

### Obtener Posiciones Disponibles

**GET** `/rotation/available-positions`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": {
    "positions": [
      {
        "number": 1,
        "name": "Posición 1 (Saque)",
        "description": "Jugador en posición de saque"
      },
      {
        "number": 2,
        "name": "Posición 2 (Delantero derecho)",
        "description": "Jugador delantero lado derecho"
      }
    ],
    "rotation_order": [1, 6, 5, 4, 3, 2],
    "current_rotations": {
      "home": [],
      "away": []
    }
  }
}
```

---

## Endpoints de Pagos

### Obtener Pagos del Jugador

**GET** `/payments/player`

**Query Parameters:**

- `status` (opcional): `pending`, `completed`, `overdue`
- `type` (opcional): `monthly_fee`, `federation`, `tournament`

**Headers:** Authorization requerido

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "monthly_fee",
      "status": "pending",
      "amount": 50000,
      "currency": "COP",
      "description": "Cuota mensual Enero 2024",
      "due_date": "2024-01-31",
      "payment_date": null,
      "reference_number": "PAY-2024-001",
      "club": {
        "id": 1,
        "name": "Club Deportivo"
      }
    }
  ]
}
```

### Registrar Pago

**POST** `/payments/{id}/register`

**Headers:** Authorization requerido

**Body:**

```json
{
  "payment_method": "bank_transfer",
  "transaction_reference": "TXN123456789",
  "payment_date": "2024-01-15",
  "notes": "Pago realizado por transferencia bancaria"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Pago registrado exitosamente",
  "data": {
    "id": 1,
    "status": "under_verification",
    "payment_date": "2024-01-15",
    "transaction_reference": "TXN123456789"
  }
}
```

---

## Códigos de Estado HTTP

- **200 OK**: Solicitud exitosa
- **201 Created**: Recurso creado exitosamente
- **400 Bad Request**: Datos de entrada inválidos
- **401 Unauthorized**: Token de autenticación inválido o faltante
- **403 Forbidden**: Sin permisos para acceder al recurso
- **404 Not Found**: Recurso no encontrado
- **422 Unprocessable Entity**: Errores de validación
- **500 Internal Server Error**: Error interno del servidor

---

## Estructura de Errores

### Error de Validación (422)

```json
{
  "success": false,
  "message": "Datos de entrada inválidos",
  "errors": {
    "email": ["El campo email es obligatorio"],
    "password": ["La contraseña debe tener al menos 8 caracteres"]
  }
}
```

### Error General

```json
{
  "success": false,
  "message": "Descripción del error",
  "error_code": "SPECIFIC_ERROR_CODE"
}
```

---

## Paginación

Los endpoints que retornan listas incluyen información de paginación:

```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_items": 48,
    "per_page": 10,
    "has_next_page": true,
    "has_previous_page": false
  }
}
```

---

## Filtros y Búsqueda

Muchos endpoints soportan filtros mediante query parameters:

- `search`: Búsqueda por texto
- `status`: Filtrar por estado
- `date_from` / `date_to`: Rango de fechas
- `sort_by`: Campo para ordenar
- `sort_order`: `asc` o `desc`
- `page`: Número de página
- `per_page`: Elementos por página (máximo 50)

**Ejemplo:**

```
GET /api/matches?status=completed&date_from=2024-01-01&date_to=2024-01-31&sort_by=scheduled_at&sort_order=desc&page=1&per_page=20
```

---

## WebSockets (Tiempo Real)

Para actualizaciones en tiempo real de partidos:

**Canal:** `match.{match_id}`

**Eventos:**

- `MatchScoreUpdated`: Actualización de marcador
- `MatchStatusChanged`: Cambio de estado del partido
- `SetCompleted`: Set completado
- `PlayerRotationUpdated`: Rotación actualizada

**Configuración:**

```javascript
const echo = new Echo({
    broadcaster: 'pusher',
    key: 'your-pusher-key',
    cluster: 'your-cluster',
    forceTLS: true,
    auth: {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }
});

echo.channel('match.1')
    .listen('MatchScoreUpdated', (e) => {
        console.log('Score updated:', e.match);
    });
```

---

## Notas Importantes

1. **Rate Limiting**: La API tiene límites de 60 requests por minuto por usuario autenticado.

2. **Versionado**: Incluir header `Accept: application/vnd.api+json;version=1` para especificar versión.

3. **Timezone**: Todas las fechas están en UTC. El cliente debe convertir a timezone local.

4. **Archivos**: Para subir archivos (logos, fotos), usar `Content-Type: multipart/form-data`.

5. **Caché**: Algunos endpoints incluyen headers de caché. Respetar `Cache-Control` y `ETag`.

6. **Logs**: Todos los requests se registran. Incluir `X-Request-ID` para tracking.

---

## Ejemplos de Integración

### JavaScript/TypeScript

```typescript
class VolleyPassAPI {
  private baseURL = 'http://localhost:8000/api';
  private token: string;

  constructor(token: string) {
    this.token = token;
  }

  private async request(endpoint: string, options: RequestInit = {}) {
    const response = await fetch(`${this.baseURL}${endpoint}`, {
      ...options,
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers
      }
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    return response.json();
  }

  async getPlayerProfile() {
    return this.request('/players/profile');
  }

  async getMatches(filters: any = {}) {
    const params = new URLSearchParams(filters);
    return this.request(`/matches?${params}`);
  }

  async updateRotation(matchId: number, data: any) {
    return this.request(`/rotation/${matchId}/update`, {
      method: 'POST',
      body: JSON.stringify(data)
    });
  }
}
```

### React Native

```typescript
import AsyncStorage from '@react-native-async-storage/async-storage';

class VolleyPassService {
  private baseURL = 'http://localhost:8000/api';

  async getAuthToken() {
    return await AsyncStorage.getItem('auth_token');
  }

  async login(email: string, password: string) {
    const response = await fetch(`${this.baseURL}/auth/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ email, password })
    });

    const data = await response.json();
    
    if (data.success) {
      await AsyncStorage.setItem('auth_token', data.data.token);
      return data.data;
    }
    
    throw new Error(data.message);
  }

  async getPlayerProfile() {
    const token = await this.getAuthToken();
    const response = await fetch(`${this.baseURL}/players/profile`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    return response.json();
  }
}
```

---

Esta documentación cubre todos los endpoints principales de la API VolleyPass. Para más detalles específicos o endpoints adicionales, consultar la documentación Swagger en `/api/documentation`.
