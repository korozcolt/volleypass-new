# Implementación de Gestión de Categorías de Jugadores

## Descripción General

Este módulo implementa la funcionalidad para gestionar las categorías de los jugadores a través de una API RESTful. Permite consultar la información de categoría de un jugador y actualizar su categoría con validaciones y notificaciones automáticas.

## Componentes Principales

### 1. Controlador API

El controlador `PlayerCategoryController` maneja las solicitudes HTTP relacionadas con las categorías de jugadores:

- **Método `show`**: Obtiene información detallada sobre la categoría actual de un jugador.
- **Método `update`**: Actualiza la categoría de un jugador con validaciones y notificaciones.

### 2. Servicio de Asignación de Categorías

El servicio `CategoryAssignmentService` contiene la lógica de negocio para:

- Validar la elegibilidad de un jugador para una categoría específica.
- Aplicar reglas especiales de asignación de categorías.
- Gestionar el proceso de cambio de categoría, incluyendo notificaciones.

### 3. Notificaciones

El sistema utiliza el servicio `CategoryNotificationService` para enviar notificaciones a las partes interesadas cuando se cambia la categoría de un jugador:

- Notificación al jugador.
- Notificación al director del club.
- Notificación al administrador de la liga (en casos especiales).

### 4. Eventos

Se dispara el evento `PlayerCategoryReassigned` cuando se cambia la categoría de un jugador, lo que permite a otros componentes del sistema reaccionar a este cambio.

## Flujo de Trabajo

1. **Consulta de Categoría**:
   - El cliente solicita información sobre la categoría de un jugador.
   - El controlador obtiene los datos del jugador y su categoría.
   - Se devuelve la información, incluyendo si el jugador está en la categoría correcta según su edad y género.

2. **Actualización de Categoría**:
   - El cliente envía una solicitud para cambiar la categoría de un jugador.
   - El controlador valida los datos de entrada.
   - Se verifica si la categoría es diferente de la actual.
   - Se llama al servicio de asignación de categorías para validar y aplicar el cambio.
   - Se envían notificaciones a las partes interesadas.
   - Se devuelve el resultado de la operación.

## Diagrama de Secuencia

```
Cliente                  API Controller             CategoryAssignmentService       Notificaciones
   |                           |                               |                           |
   | PUT /players/{id}/category|                               |                           |
   |-------------------------->|                               |                           |
   |                           | Validar datos                 |                           |
   |                           |------------------------       |                           |
   |                           |                      |        |                           |
   |                           |<-----------------------       |                           |
   |                           |                               |                           |
   |                           | updatePlayerCategory()        |                           |
   |                           |------------------------------>|                           |
   |                           |                               | Validar elegibilidad      |
   |                           |                               |------------------------   |
   |                           |                               |                      |    |
   |                           |                               |<-----------------------  |
   |                           |                               |                           |
   |                           |                               | Actualizar categoría      |
   |                           |                               |------------------------   |
   |                           |                               |                      |    |
   |                           |                               |<-----------------------  |
   |                           |                               |                           |
   |                           |                               | Enviar notificaciones     |
   |                           |                               |---------------------------------------->|
   |                           |                               |                           |              |
   |                           |                               |                           |<-------------|
   |                           |                               |                           |
   |                           |<------------------------------|                           |
   |                           |                               |                           |
   |<--------------------------|                               |                           |
   |                           |                               |                           |
```

## Consideraciones de Seguridad

- **Autenticación**: Todos los endpoints requieren autenticación mediante token.
- **Autorización**: Se verifica que el usuario tenga los permisos adecuados para realizar cambios de categoría.
- **Validación**: Se validan todos los datos de entrada para prevenir ataques de inyección.
- **Auditoría**: Se registran todos los cambios de categoría para fines de auditoría.

## Pruebas

Se han implementado pruebas unitarias para verificar el correcto funcionamiento del controlador y sus interacciones con otros componentes del sistema. Las pruebas cubren:

- Obtención de información de categoría.
- Actualización exitosa de categoría.
- Validación de datos de entrada.
- Manejo de errores.

## Documentación Adicional

- [Endpoints de API](player-category-endpoints.md): Documentación detallada de los endpoints disponibles.
- [Ejemplos de Uso](examples/player-category-examples.md): Ejemplos prácticos de cómo utilizar los endpoints desde diferentes clientes.