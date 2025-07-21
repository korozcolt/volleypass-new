# Sistema de Categorías Dinámicas por Liga - Requisitos

## Introducción

El Sistema de Categorías Dinámicas permite que cada liga configure sus propios rangos de edad y categorías deportivas, superando las limitaciones del sistema actual de categorías fijas. Esto es esencial para la escalabilidad nacional del sistema, ya que diferentes regiones tienen normativas distintas.

## Problema Actual

### Situación Problemática
- ✅ Categorías fijas definidas en `PlayerCategory` enum
- ✅ Asignación automática por edad implementada
- ❌ No configurable por liga - todas usan los mismos rangos
- ❌ Rangos predefinidos que no se adaptan a diferencias regionales

### Ejemplos Reales
- **Liga A**: Mini (6-8), Pre-Mini (9-10), Infantil (11-12)
- **Liga B**: Mini (8-10), Infantil (11-14), Juvenil (15-17)  
- **Liga C**: Sin Masters, con Sub-21 (19-21)

## Requisitos

### Requisito 1: Configuración Flexible de Categorías por Liga

**User Story:** Como administrador de liga, quiero configurar mis propias categorías deportivas con rangos de edad específicos, para adaptar el sistema a las normativas de mi región.

#### Acceptance Criteria

1. WHEN accedo al panel de administración de liga THEN debo poder ver una sección de "Categorías"
2. WHEN configuro una nueva categoría THEN debo poder definir nombre, rango de edad (min-max), género y orden de visualización
3. WHEN defino rangos de edad THEN el sistema debe validar que no se superpongan para el mismo género
4. WHEN activo/desactivo una categoría THEN debe afectar inmediatamente la asignación de nuevas jugadoras
5. WHEN guardo la configuración THEN debe aplicarse solo a mi liga sin afectar otras ligas

### Requisito 2: Asignación Automática Dinámica

**User Story:** Como sistema, quiero asignar automáticamente la categoría correcta a cada jugadora basándome en la configuración específica de su liga, para garantizar precisión en la categorización.

#### Acceptance Criteria

1. WHEN se registra una nueva jugadora THEN el sistema debe consultar la configuración de categorías de su liga
2. WHEN encuentra una categoría apropiada THEN debe asignarla automáticamente basándose en edad y género
3. WHEN no encuentra configuración específica THEN debe usar el sistema de fallback con categorías estándar
4. WHEN cambia la edad de una jugadora THEN debe recalcular y actualizar su categoría automáticamente
5. WHEN una jugadora cambia de liga THEN debe reasignar su categoría según la nueva configuración

### Requisito 3: Validaciones Dinámicas en Generación de Carnets

**User Story:** Como sistema de carnetización, quiero validar que la categoría de cada jugadora sea correcta según la configuración de su liga, para garantizar la integridad de los carnets generados.

#### Acceptance Criteria

1. WHEN se genera un carnet automáticamente THEN debe validar que la categoría sea válida para la liga
2. WHEN la categoría no es válida THEN debe rechazar la generación y notificar el error específico
3. WHEN la edad no coincide con la categoría THEN debe sugerir la categoría correcta
4. WHEN una categoría está desactivada THEN debe impedir su uso en nuevos carnets
5. WHEN se valida una jugadora THEN debe verificar contra la configuración actual de la liga

### Requisito 4: Migración Segura de Datos Existentes

**User Story:** Como administrador del sistema, quiero migrar todas las jugadoras existentes al nuevo sistema de categorías dinámicas sin perder datos ni causar inconsistencias.

#### Acceptance Criteria

1. WHEN se ejecuta la migración THEN debe crear configuraciones por defecto para todas las ligas existentes
2. WHEN se crean las configuraciones THEN deben usar los rangos actuales del enum como base
3. WHEN se migran jugadoras THEN deben mantener sus categorías actuales si son válidas
4. WHEN una jugadora tiene categoría inválida THEN debe reasignarla según la nueva configuración
5. WHEN falla la migración THEN debe poder revertirse completamente sin pérdida de datos

### Requisito 5: Interface de Administración Intuitiva

**User Story:** Como administrador de liga, quiero una interfaz clara y fácil de usar para gestionar las categorías de mi liga, para poder configurar el sistema sin conocimientos técnicos.

#### Acceptance Criteria

1. WHEN accedo a la gestión de categorías THEN debe mostrar una tabla clara con todas las categorías configuradas
2. WHEN creo una nueva categoría THEN debe tener un formulario intuitivo con validaciones en tiempo real
3. WHEN edito una categoría existente THEN debe mostrar cuántas jugadoras se verían afectadas
4. WHEN elimino una categoría THEN debe advertir sobre el impacto y requerir confirmación
5. WHEN veo la lista THEN debe mostrar estadísticas como número de jugadoras por categoría

### Requisito 6: Compatibilidad con Sistema Existente

**User Story:** Como desarrollador, quiero mantener la compatibilidad con el código existente que usa el enum PlayerCategory, para evitar romper funcionalidades actuales.

#### Acceptance Criteria

1. WHEN el código existente usa PlayerCategory THEN debe seguir funcionando sin modificaciones
2. WHEN no hay configuración específica de liga THEN debe usar el comportamiento tradicional del enum
3. WHEN se llama a métodos del enum THEN debe considerar la configuración dinámica si está disponible
4. WHEN se actualiza el sistema THEN no debe requerir cambios en código que no esté relacionado con categorías
5. WHEN hay conflictos THEN la configuración dinámica debe tener prioridad sobre el enum

### Requisito 7: Reportes y Estadísticas por Configuración

**User Story:** Como administrador de liga, quiero ver reportes y estadísticas basados en mi configuración específica de categorías, para tomar decisiones informadas.

#### Acceptance Criteria

1. WHEN genero reportes THEN deben usar las categorías configuradas para mi liga
2. WHEN veo estadísticas THEN deben mostrar distribución por mis categorías específicas
3. WHEN comparo períodos THEN debe considerar cambios en configuración de categorías
4. WHEN exporto datos THEN debe incluir información de configuración de categorías usada
5. WHEN analizo tendencias THEN debe mostrar impacto de cambios en configuración

### Requisito 8: Validación de Integridad de Configuración

**User Story:** Como sistema, quiero validar que las configuraciones de categorías sean lógicas y consistentes, para prevenir errores de configuración.

#### Acceptance Criteria

1. WHEN se define un rango de edad THEN debe validar que min_age < max_age
2. WHEN se crean múltiples categorías THEN debe validar que no haya superposición de rangos para el mismo género
3. WHEN se desactiva una categoría THEN debe validar que no haya jugadoras activas asignadas
4. WHEN se modifica un rango THEN debe validar el impacto en jugadoras existentes
5. WHEN se guarda la configuración THEN debe verificar que cubra todos los rangos de edad necesarios

### Requisito 9: Notificaciones de Cambios de Categoría

**User Story:** Como jugadora y director de club, quiero ser notificado cuando cambie la categoría de una jugadora debido a cambios en configuración, para estar informado de los cambios.

#### Acceptance Criteria

1. WHEN cambia la configuración de categorías THEN debe notificar a jugadoras afectadas
2. WHEN una jugadora cambia automáticamente de categoría THEN debe notificar al director del club
3. WHEN hay conflictos de categoría THEN debe notificar al administrador de liga
4. WHEN se requiere acción manual THEN debe enviar notificación con instrucciones claras
5. WHEN se resuelve un conflicto THEN debe confirmar la resolución a todos los involucrados

### Requisito 10: Auditoría de Cambios de Configuración

**User Story:** Como auditor del sistema, quiero tener un registro completo de todos los cambios en configuraciones de categorías, para mantener trazabilidad y cumplimiento.

#### Acceptance Criteria

1. WHEN se modifica una configuración de categoría THEN debe registrar quién, cuándo y qué cambió
2. WHEN se reasignan jugadoras automáticamente THEN debe registrar el motivo y resultado
3. WHEN hay errores en asignación THEN debe registrar el error y las acciones tomadas
4. WHEN se consulta el historial THEN debe mostrar cronología completa de cambios
5. WHEN se requiere auditoría THEN debe poder generar reportes de todos los cambios en un período

### Requisito 11: Performance y Escalabilidad

**User Story:** Como sistema, quiero mantener un rendimiento óptimo incluso con múltiples ligas y configuraciones complejas de categorías, para garantizar una experiencia fluida.

#### Acceptance Criteria

1. WHEN se consultan categorías THEN debe usar caché para evitar consultas repetitivas
2. WHEN se asignan categorías masivamente THEN debe procesar en lotes eficientemente
3. WHEN hay muchas ligas THEN el tiempo de respuesta no debe degradarse significativamente
4. WHEN se actualizan configuraciones THEN debe invalidar caché selectivamente
5. WHEN se consultan estadísticas THEN debe usar índices optimizados para consultas rápidas

### Requisito 12: Configuración de Categorías Especiales

**User Story:** Como administrador de liga, quiero poder configurar categorías especiales con reglas particulares, para manejar casos específicos de mi región.

#### Acceptance Criteria

1. WHEN creo una categoría THEN debo poder marcarla como "especial" con reglas adicionales
2. WHEN defino reglas especiales THEN debo poder especificar validaciones adicionales
3. WHEN una jugadora califica para categoría especial THEN debe tener prioridad sobre categorías normales
4. WHEN hay múltiples categorías aplicables THEN debe usar el orden de prioridad configurado
5. WHEN se valida elegibilidad THEN debe considerar todas las reglas especiales definidas
