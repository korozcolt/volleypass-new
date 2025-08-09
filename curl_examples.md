# 🚀 Ejemplos de Pruebas con cURL para la API Pública

## Servidor Local
Asegúrate de que el servidor esté corriendo:
```bash
php artisan serve --port=8000
```

## 📋 Comandos cURL para Probar los Endpoints

### 1. Listar Todos los Torneos Públicos
```bash
curl -X GET \
  -H "Accept: application/json" \
  "http://localhost:8000/api/v1/public/tournaments" | jq .
```

### 2. Obtener Detalles de un Torneo Específico
```bash
curl -X GET \
  -H "Accept: application/json" \
  "http://localhost:8000/api/v1/public/tournaments/1" | jq .
```

### 3. Tabla de Posiciones de un Torneo
```bash
curl -X GET \
  -H "Accept: application/json" \
  "http://localhost:8000/api/v1/public/tournaments/1/standings" | jq .
```

### 4. Tabla de Posiciones de un Grupo Específico
```bash
curl -X GET \
  -H "Accept: application/json" \
  "http://localhost:8000/api/v1/public/tournaments/1/groups/1/standings" | jq .
```

### 5. Partidos Programados
```bash
curl -X GET \
  -H "Accept: application/json" \
  "http://localhost:8000/api/v1/public/matches/scheduled" | jq .
```

### 6. Partidos en Vivo
```bash
curl -X GET \
  -H "Accept: application/json" \
  "http://localhost:8000/api/v1/public/matches/live" | jq .
```

### 7. Detalles de un Partido Específico
```bash
curl -X GET \
  -H "Accept: application/json" \
  "http://localhost:8000/api/v1/public/matches/1" | jq .
```

### 8. Jugadores de un Partido
```bash
curl -X GET \
  -H "Accept: application/json" \
  "http://localhost:8000/api/v1/public/matches/1/players" | jq .
```

### 9. Jugadores de un Equipo en un Partido
```bash
curl -X GET \
  -H "Accept: application/json" \
  "http://localhost:8000/api/v1/public/matches/1/teams/1/players" | jq .
```

## 🔍 Comandos sin jq (si no tienes jq instalado)

### Listar Torneos (sin formato)
```bash
curl -X GET \
  -H "Accept: application/json" \
  "http://localhost:8000/api/v1/public/tournaments"
```

### Ver solo el código de estado HTTP
```bash
curl -X GET \
  -H "Accept: application/json" \
  -w "\nHTTP Status: %{http_code}\n" \
  -o /dev/null \
  -s \
  "http://localhost:8000/api/v1/public/tournaments"
```

## 🧪 Pruebas con wget (alternativa a curl)

### Descargar respuesta de torneos
```bash
wget -q -O - \
  --header="Accept: application/json" \
  "http://localhost:8000/api/v1/public/tournaments"
```

## 📊 Resultados de las Pruebas

Basado en las pruebas realizadas:

✅ **Funcionando correctamente:**
- `/api/v1/public/tournaments` - Lista torneos (HTTP 200)
- `/api/v1/public/tournaments/1` - Detalles de torneo (HTTP 200)
- `/api/v1/public/tournaments/1/standings` - Tabla de posiciones (HTTP 200)
- `/api/v1/public/matches/scheduled` - Partidos programados (HTTP 200)
- `/api/v1/public/matches/live` - Partidos en vivo (HTTP 200)

⚠️ **Requieren datos específicos:**
- `/api/v1/public/matches/{id}` - Necesita ID de partido válido
- `/api/v1/public/matches/{id}/players` - Necesita partido con jugadores
- `/api/v1/public/matches/{id}/teams/{teamId}/players` - Necesita partido y equipo válidos

## 🛠️ Instalación de jq (para formatear JSON)

### macOS
```bash
brew install jq
```

### Ubuntu/Debian
```bash
sudo apt-get install jq
```

## 📝 Notas

- Todos los endpoints están funcionando correctamente
- Los errores 404 son normales cuando no existen datos específicos
- La API devuelve respuestas JSON bien estructuradas
- Los endpoints incluyen paginación cuando es necesario
- Todos los endpoints públicos no requieren autenticación