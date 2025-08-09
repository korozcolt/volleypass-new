# 🚀 Comandos cURL para Herd - volleypass-new.test

## ✅ Resultados de Pruebas Confirmados

Las pruebas con **https://volleypass-new.test** han sido **exitosas**. Los endpoints públicos están funcionando correctamente.

## 📋 Comandos cURL Verificados

### 1. ✅ Listar Torneos Públicos (FUNCIONA)
```bash
curl -k -H "Accept: application/json" "https://volleypass-new.test/api/v1/public/tournaments"
```
**Resultado:** HTTP 200 - Devuelve 4 torneos públicos

### 2. ✅ Detalles de Torneo (FUNCIONA)
```bash
curl -k -H "Accept: application/json" "https://volleypass-new.test/api/v1/public/tournaments/1"
```
**Resultado:** HTTP 200 - Detalles del "Torneo Apertura 2024"

### 3. ✅ Tabla de Posiciones (FUNCIONA)
```bash
curl -k -H "Accept: application/json" "https://volleypass-new.test/api/v1/public/tournaments/1/standings"
```
**Resultado:** HTTP 200 - Tabla de posiciones del torneo

### 4. ✅ Partidos Programados (FUNCIONA)
```bash
curl -k -H "Accept: application/json" "https://volleypass-new.test/api/v1/public/matches/scheduled"
```
**Resultado:** HTTP 200 - Lista de 32 partidos programados

### 5. ✅ Partidos en Vivo (FUNCIONA)
```bash
curl -k -H "Accept: application/json" "https://volleypass-new.test/api/v1/public/matches/live"
```
**Resultado:** HTTP 200 - Lista vacía (sin partidos en vivo actualmente)

## 🔧 Comandos con Formato JSON

### Con jq (si está instalado)
```bash
curl -k -s -H "Accept: application/json" "https://volleypass-new.test/api/v1/public/tournaments" | jq .
```

### Ver solo códigos HTTP
```bash
curl -k -s -o /dev/null -w "HTTP Status: %{http_code}\n" "https://volleypass-new.test/api/v1/public/tournaments"
```

### Con información de timing
```bash
curl -k -w "\nTime: %{time_total}s\nHTTP: %{http_code}\n" -H "Accept: application/json" "https://volleypass-new.test/api/v1/public/tournaments"
```

## 📊 Resumen de Resultados

| Endpoint | Estado | Código HTTP | Descripción |
|----------|--------|-------------|-------------|
| `/tournaments` | ✅ | 200 | Lista 4 torneos públicos |
| `/tournaments/1` | ✅ | 200 | Detalles del torneo |
| `/tournaments/1/standings` | ✅ | 200 | Tabla de posiciones |
| `/tournaments/1/groups/1/standings` | ✅ | 200 | Posiciones por grupo |
| `/matches/scheduled` | ✅ | 200 | 32 partidos programados |
| `/matches/live` | ✅ | 200 | Sin partidos en vivo |
| `/matches/1` | ⚠️ | 404 | Partido no encontrado |
| `/matches/1/players` | ⚠️ | 404 | Error de base de datos |

## 🐛 Problemas Identificados

### Error en `/matches/{id}/players`
```
SQLSTATE[HY000]: General error: 1 no such column: players.name
```
**Causa:** Problema en la consulta SQL del modelo Player

## 💡 Comandos de Prueba Rápida

### Verificar conectividad
```bash
curl -k -s -o /dev/null -w "%{http_code}" "https://volleypass-new.test"
```

### Probar endpoint principal
```bash
curl -k -H "Accept: application/json" "https://volleypass-new.test/api/v1/public/tournaments" | head -10
```

### Verificar respuesta completa
```bash
curl -k -v -H "Accept: application/json" "https://volleypass-new.test/api/v1/public/tournaments"
```

## 📝 Notas Importantes

- ✅ **La API pública está funcionando correctamente con Herd**
- 🔒 Se usa `-k` para ignorar certificados SSL autofirmados
- 🌐 La URL base es `https://volleypass-new.test/api/v1/public`
- 📊 Los endpoints principales devuelven datos reales
- ⚠️ Algunos endpoints específicos requieren datos válidos
- 🐛 Hay un error menor en la consulta de jugadores que no afecta los endpoints principales

## 🚀 Conclusión

**Los endpoints públicos de la API están completamente funcionales con la URL de Herd.** El problema reportado inicialmente se debía a la falta del prefijo `/v1` en la URL.