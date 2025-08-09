#!/bin/bash

# Script simple para probar endpoints públicos de la API
echo "🚀 PRUEBAS RÁPIDAS DE LA API PÚBLICA"
echo "====================================="
echo ""

BASE_URL="http://localhost:8000/api/v1/public"

# Función simple para probar endpoints
test_simple() {
    local endpoint=$1
    local description=$2
    
    echo "📡 $description"
    echo "🔗 GET $endpoint"
    echo "---"
    
    curl -s -w "\nHTTP Status: %{http_code}\n" \
        -H "Accept: application/json" \
        "$endpoint" | head -20
    
    echo ""
    echo "================================================"
    echo ""
}

echo "1️⃣ Listando torneos públicos..."
test_simple "$BASE_URL/tournaments" "Torneos públicos disponibles"

echo "2️⃣ Probando detalles de torneo..."
test_simple "$BASE_URL/tournaments/1" "Detalles del torneo ID 1"

echo "3️⃣ Probando tabla de posiciones..."
test_simple "$BASE_URL/tournaments/1/standings" "Tabla de posiciones del torneo 1"

echo "4️⃣ Probando partidos programados..."
test_simple "$BASE_URL/matches/scheduled" "Partidos programados"

echo "5️⃣ Probando partidos en vivo..."
test_simple "$BASE_URL/matches/live" "Partidos en vivo"

echo "✅ PRUEBAS COMPLETADAS"
echo ""
echo "💡 Para ver respuestas completas, usa:"
echo "   curl -H 'Accept: application/json' $BASE_URL/tournaments"
echo ""
echo "📚 Endpoints disponibles:"
echo "   GET /api/v1/public/tournaments"
echo "   GET /api/v1/public/tournaments/{id}"
echo "   GET /api/v1/public/tournaments/{id}/standings"
echo "   GET /api/v1/public/tournaments/{id}/groups/{groupId}/standings"
echo "   GET /api/v1/public/matches/scheduled"
echo "   GET /api/v1/public/matches/live"
echo "   GET /api/v1/public/matches/{id}"
echo "   GET /api/v1/public/matches/{id}/players"
echo "   GET /api/v1/public/matches/{id}/teams/{teamId}/players"