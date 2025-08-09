#!/bin/bash

# Script de prueba para los endpoints públicos de la API
# Asegúrate de que el servidor esté corriendo en http://localhost:8000

echo "=== PRUEBA DE ENDPOINTS PÚBLICOS DE LA API ==="
echo ""

# URL base de la API
BASE_URL="http://localhost:8000/api/v1/public"

# Función para hacer peticiones y mostrar resultados
test_endpoint() {
    local method=$1
    local endpoint=$2
    local description=$3
    
    echo "📡 Probando: $description"
    echo "🔗 $method $endpoint"
    echo "---"
    
    response=$(curl -s -w "\n%{http_code}" -X $method \
        -H "Accept: application/json" \
        -H "Content-Type: application/json" \
        "$endpoint")
    
    # Separar el cuerpo de la respuesta del código HTTP
    http_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')
    
    echo "📊 Código HTTP: $http_code"
    
    if [ "$http_code" = "200" ]; then
        echo "✅ Éxito"
        echo "📄 Respuesta:"
        echo "$body" | python3 -m json.tool 2>/dev/null || echo "$body"
    elif [ "$http_code" = "404" ]; then
        echo "⚠️  No encontrado (404) - Esto puede ser normal si no hay datos"
        echo "📄 Respuesta:"
        echo "$body" | python3 -m json.tool 2>/dev/null || echo "$body"
    else
        echo "❌ Error"
        echo "📄 Respuesta:"
        echo "$body"
    fi
    
    echo ""
    echo "================================================"
    echo ""
}

# 1. Listar todos los torneos públicos
test_endpoint "GET" "$BASE_URL/tournaments" "Listar todos los torneos públicos"

# 2. Obtener detalles de un torneo específico (ID 1)
test_endpoint "GET" "$BASE_URL/tournaments/1" "Detalles del torneo con ID 1"

# 3. Obtener tabla de posiciones de un torneo
test_endpoint "GET" "$BASE_URL/tournaments/1/standings" "Tabla de posiciones del torneo 1"

# 4. Obtener tabla de posiciones de un grupo específico
test_endpoint "GET" "$BASE_URL/tournaments/1/groups/1/standings" "Tabla de posiciones del grupo 1 del torneo 1"

# 5. Listar partidos programados
test_endpoint "GET" "$BASE_URL/matches/scheduled" "Partidos programados"

# 6. Listar partidos en vivo
test_endpoint "GET" "$BASE_URL/matches/live" "Partidos en vivo"

# 7. Obtener detalles de un partido específico
test_endpoint "GET" "$BASE_URL/matches/1" "Detalles del partido con ID 1"

# 8. Obtener jugadores de un partido
test_endpoint "GET" "$BASE_URL/matches/1/players" "Jugadores del partido 1"

# 9. Obtener jugadores de un equipo específico en un partido
test_endpoint "GET" "$BASE_URL/matches/1/teams/1/players" "Jugadores del equipo 1 en el partido 1"

echo "🏁 PRUEBAS COMPLETADAS"
echo ""
echo "💡 Notas:"
echo "   - Los errores 404 pueden ser normales si no hay datos de prueba"
echo "   - Asegúrate de que el servidor esté corriendo en http://localhost:8000"
echo "   - Puedes ejecutar 'php artisan serve' para iniciar el servidor"
echo "   - Para crear datos de prueba, ejecuta: php artisan db:seed --class=ExampleDataSeeder"