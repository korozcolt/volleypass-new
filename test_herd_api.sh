#!/bin/bash

# Script de prueba para los endpoints públicos usando la URL de Herd
# URL: https://volleypass-new.test

echo "=== PRUEBA DE ENDPOINTS PÚBLICOS CON HERD ==="
echo "🌐 URL Base: https://volleypass-new.test"
echo ""

# URL base de la API con Herd
BASE_URL="https://volleypass-new.test/api/v1/public"

# Función para hacer peticiones y mostrar resultados
test_herd_endpoint() {
    local method=$1
    local endpoint=$2
    local description=$3
    
    echo "📡 Probando: $description"
    echo "🔗 $method $endpoint"
    echo "---"
    
    # Usar curl con opciones para HTTPS y certificados
    response=$(curl -s -w "\n%{http_code}" -X $method \
        -H "Accept: application/json" \
        -H "Content-Type: application/json" \
        -k \
        --connect-timeout 10 \
        --max-time 30 \
        "$endpoint" 2>/dev/null)
    
    # Verificar si curl falló
    if [ $? -ne 0 ]; then
        echo "❌ Error de conexión - No se pudo conectar al servidor"
        echo "💡 Verifica que Herd esté corriendo y que el sitio esté disponible"
        echo ""
        echo "================================================"
        echo ""
        return
    fi
    
    # Separar el cuerpo de la respuesta del código HTTP
    http_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')
    
    echo "📊 Código HTTP: $http_code"
    
    if [ "$http_code" = "200" ]; then
        echo "✅ Éxito"
        echo "📄 Respuesta (primeras líneas):"
        echo "$body" | head -10
    elif [ "$http_code" = "404" ]; then
        echo "⚠️  No encontrado (404)"
        echo "📄 Respuesta:"
        echo "$body"
    elif [ "$http_code" = "000" ]; then
        echo "❌ Error de conexión - Servidor no disponible"
        echo "💡 Verifica que Herd esté corriendo"
    else
        echo "❌ Error HTTP $http_code"
        echo "📄 Respuesta:"
        echo "$body"
    fi
    
    echo ""
    echo "================================================"
    echo ""
}

# Verificar conectividad básica
echo "🔍 Verificando conectividad con Herd..."
ping_result=$(curl -s -k --connect-timeout 5 --max-time 10 -o /dev/null -w "%{http_code}" "https://volleypass-new.test" 2>/dev/null)
if [ "$ping_result" = "000" ]; then
    echo "❌ No se puede conectar a https://volleypass-new.test"
    echo "💡 Asegúrate de que:"
    echo "   - Herd esté corriendo"
    echo "   - El sitio volleypass-new.test esté configurado"
    echo "   - No haya problemas de DNS local"
    echo ""
    exit 1
else
    echo "✅ Conectividad OK (HTTP $ping_result)"
    echo ""
fi

# 1. Listar todos los torneos públicos
test_herd_endpoint "GET" "$BASE_URL/tournaments" "Listar todos los torneos públicos"

# 2. Obtener detalles de un torneo específico (ID 1)
test_herd_endpoint "GET" "$BASE_URL/tournaments/1" "Detalles del torneo con ID 1"

# 3. Obtener tabla de posiciones de un torneo
test_herd_endpoint "GET" "$BASE_URL/tournaments/1/standings" "Tabla de posiciones del torneo 1"

# 4. Obtener tabla de posiciones de un grupo específico
test_herd_endpoint "GET" "$BASE_URL/tournaments/1/groups/1/standings" "Tabla de posiciones del grupo 1 del torneo 1"

# 5. Listar partidos programados
test_herd_endpoint "GET" "$BASE_URL/matches/scheduled" "Partidos programados"

# 6. Listar partidos en vivo
test_herd_endpoint "GET" "$BASE_URL/matches/live" "Partidos en vivo"

# 7. Obtener detalles de un partido específico
test_herd_endpoint "GET" "$BASE_URL/matches/1" "Detalles del partido con ID 1"

# 8. Obtener jugadores de un partido
test_herd_endpoint "GET" "$BASE_URL/matches/1/players" "Jugadores del partido 1"

# 9. Obtener jugadores de un equipo específico en un partido
test_herd_endpoint "GET" "$BASE_URL/matches/1/teams/1/players" "Jugadores del equipo 1 en el partido 1"

echo "🏁 PRUEBAS CON HERD COMPLETADAS"
echo ""
echo "💡 Comandos manuales para probar:"
echo "   curl -k -H 'Accept: application/json' https://volleypass-new.test/api/v1/public/tournaments"
echo "   curl -k -H 'Accept: application/json' https://volleypass-new.test/api/v1/public/matches/scheduled"
echo ""
echo "📝 Notas:"
echo "   - Se usa -k para ignorar certificados SSL autofirmados"
echo "   - Los timeouts están configurados para conexiones lentas"
echo "   - Verifica que Herd esté corriendo si hay errores de conexión"