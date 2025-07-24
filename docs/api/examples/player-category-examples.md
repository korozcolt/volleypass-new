# Ejemplos de Uso de los Endpoints de Categoría de Jugadores

Este documento proporciona ejemplos prácticos de cómo utilizar los endpoints de gestión de categorías de jugadores desde diferentes tipos de clientes.

## Ejemplo con cURL

### Obtener información de categoría

```bash
curl -X GET \
  'https://api.volleypass.com/api/v1/players/123/category' \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Accept: application/json'
```

### Actualizar categoría

```bash
curl -X PUT \
  'https://api.volleypass.com/api/v1/players/123/category' \
  -H 'Authorization: Bearer YOUR_API_TOKEN' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "category": "Cadete",
    "reason": "Cambio debido a habilidades técnicas excepcionales"
}'
```

## Ejemplo con JavaScript (Fetch API)

### Obtener información de categoría

```javascript
const fetchPlayerCategory = async (playerId, token) => {
  try {
    const response = await fetch(`https://api.volleypass.com/api/v1/players/${playerId}/category`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.message || 'Error al obtener información de categoría');
    }
    
    return data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
};

// Uso
fetchPlayerCategory(123, 'YOUR_API_TOKEN')
  .then(data => {
    console.log('Información de categoría:', data);
    // Mostrar información en la UI
    displayCategoryInfo(data.player);
  })
  .catch(error => {
    // Manejar error
    showErrorMessage(error.message);
  });
```

### Actualizar categoría

```javascript
const updatePlayerCategory = async (playerId, categoryData, token) => {
  try {
    const response = await fetch(`https://api.volleypass.com/api/v1/players/${playerId}/category`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(categoryData)
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.message || 'Error al actualizar categoría');
    }
    
    return data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
};

// Uso
const categoryData = {
  category: 'Cadete',
  reason: 'Cambio debido a habilidades técnicas excepcionales'
};

updatePlayerCategory(123, categoryData, 'YOUR_API_TOKEN')
  .then(data => {
    console.log('Categoría actualizada:', data);
    // Mostrar mensaje de éxito
    showSuccessMessage(data.message);
    // Actualizar UI
    updatePlayerInfo(data.player);
  })
  .catch(error => {
    // Manejar error
    showErrorMessage(error.message);
  });
```

## Ejemplo con Axios (JavaScript)

### Configuración

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'https://api.volleypass.com/api/v1',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// Interceptor para añadir token de autenticación
api.interceptors.request.use(config => {
  const token = localStorage.getItem('api_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
```

### Obtener información de categoría

```javascript
const getPlayerCategory = async (playerId) => {
  try {
    const response = await api.get(`/players/${playerId}/category`);
    return response.data;
  } catch (error) {
    console.error('Error al obtener categoría:', error.response?.data?.message || error.message);
    throw error;
  }
};
```

### Actualizar categoría

```javascript
const updatePlayerCategory = async (playerId, categoryData) => {
  try {
    const response = await api.put(`/players/${playerId}/category`, categoryData);
    return response.data;
  } catch (error) {
    console.error('Error al actualizar categoría:', error.response?.data?.message || error.message);
    throw error;
  }
};
```

## Ejemplo con Flutter (Dart)

### Obtener información de categoría

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class PlayerCategoryService {
  final String baseUrl = 'https://api.volleypass.com/api/v1';
  final String token;
  
  PlayerCategoryService({required this.token});
  
  Future<Map<String, dynamic>> getPlayerCategory(int playerId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/players/$playerId/category'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    
    final data = json.decode(response.body);
    
    if (response.statusCode == 200) {
      return data;
    } else {
      throw Exception(data['message'] ?? 'Error al obtener información de categoría');
    }
  }
  
  Future<Map<String, dynamic>> updatePlayerCategory(int playerId, String category, String reason) async {
    final response = await http.put(
      Uri.parse('$baseUrl/players/$playerId/category'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: json.encode({
        'category': category,
        'reason': reason,
      }),
    );
    
    final data = json.decode(response.body);
    
    if (response.statusCode == 200) {
      return data;
    } else {
      throw Exception(data['message'] ?? 'Error al actualizar categoría');
    }
  }
}
```

## Manejo de Errores

Todos los ejemplos anteriores incluyen manejo básico de errores. Es importante tener en cuenta que los errores pueden ocurrir por varias razones:

1. **Errores de autenticación (401)**: Token inválido o expirado
2. **Errores de autorización (403)**: El usuario no tiene permisos para realizar la acción
3. **Errores de validación (422)**: Datos de entrada inválidos
4. **Errores del servidor (500)**: Problemas internos del servidor

Siempre verifica el código de estado HTTP y el mensaje de error devuelto por la API para proporcionar retroalimentación adecuada al usuario.

## Consideraciones de Seguridad

- Nunca almacenes tokens de API en código fuente público
- Utiliza HTTPS para todas las comunicaciones con la API
- Implementa mecanismos de renovación de tokens cuando expiren
- Valida los datos de entrada antes de enviarlos a la API
- Maneja adecuadamente los errores para evitar exponer información sensible