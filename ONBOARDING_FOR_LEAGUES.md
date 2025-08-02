# 🏆 TAREAS: SISTEMA DE ONBOARDING PARA LIGAS

## 📋 **OBJETIVO**

Crear un sistema completo de onboarding para ligas que incluya: creación automática de usuario liga, envío de credenciales temporales, wizard de configuración inicial y activación del sistema.

---

## 🎯 **TAREA 1: SISTEMA DE CREACIÓN AUTOMÁTICA DE USUARIO LIGA**

### **1.1 Extender LeagueResource (Filament)**

```php
// app/Filament/Resources/LeagueResource.php - MODIFICAR

// Agregar campos al formulario:
- admin_email (email, required, unique)
- admin_first_name (string)
- admin_last_name (string)
- admin_phone (string, optional)

// Hook afterCreate():
- Crear usuario automáticamente
- Asignar rol 'league_admin'
- Generar contraseña temporal
- Enviar email de bienvenida
- Marcar liga como 'setup_pending'
```

### **1.2 Crear Estados de Liga**

```php
// app/Enums/LeagueStatus.php

enum LeagueStatus: string 
{
    case SetupPending = 'setup_pending';    // Recién creada
    case Configuring = 'configuring';      // En proceso de setup
    case Active = 'active';                 // Configurada y activa
    case Inactive = 'inactive';             // Temporalmente inactiva
    case Suspended = 'suspended';           // Suspendida por admin
}
```

### **1.3 Crear Modelo de Configuración de Liga**

```php
// php artisan make:model LeagueConfiguration -m

// Campos:
- league_id (foreign key)
- tournament_format (json) // Round robin, eliminación, etc.
- season_duration (integer) // Meses
- max_teams_per_tournament (integer)
- registration_fee (decimal)
- referee_assignment_mode (enum: auto, manual)
- custom_categories (json) // Categorías específicas de la liga
- custom_rules (json) // Reglas específicas
- branding (json) // Logo, colores personalizados
- contact_info (json) // Info de contacto de la liga
- setup_completed_at (timestamp)
- configuration_version (integer)
```

---

## 🎯 **TAREA 2: SISTEMA DE NOTIFICACIONES Y CONTRASEÑAS TEMPORALES**

### **2.1 Crear Tabla de Contraseñas Temporales**

```php
// php artisan make:migration create_temporary_passwords_table

// Campos:
- id
- user_id (foreign key)
- token (string, encrypted)
- expires_at (timestamp)
- used_at (timestamp, nullable)
- created_by (foreign key users) // Quien generó la contraseña
- purpose (enum: first_login, password_reset)
- metadata (json) // Info adicional
```

### **2.2 Crear TemporaryPasswordService**

```php
// app/Services/TemporaryPasswordService.php

// Métodos:
- generateTemporaryPassword(User $user, string $purpose): string
- validateTemporaryPassword(string $email, string $password): bool
- markAsUsed(string $token): void
- cleanExpiredPasswords(): void
- isPasswordTemporary(User $user): bool
- getPasswordMetadata(User $user): array
```

### **2.3 Crear Notificaciones por Email**

```php
// app/Notifications/LeagueWelcomeNotification.php

// Contenido del email:
- Bienvenida personalizada
- Credenciales de acceso (email + contraseña temporal)
- Enlace directo al sistema
- Instrucciones paso a paso
- Contacto de soporte
- Fecha de expiración de contraseña
```

### **2.4 Crear Job de Procesamiento**

```php
// app/Jobs/ProcessLeagueCreationJob.php

// Acciones:
- Crear usuario con datos de la liga
- Generar contraseña temporal (8 caracteres seguros)
- Asignar permisos específicos de liga
- Enviar notificación de bienvenida
- Crear configuración inicial de liga
- Log de todas las acciones
```

---

## 🎯 **TAREA 3: MIDDLEWARE Y AUTENTICACIÓN**

### **3.1 Crear Middleware de Contraseña Temporal**

```php
// app/Http/Middleware/RequirePasswordChange.php

// Funcionalidad:
- Detectar usuarios con contraseña temporal
- Redirigir a página de cambio obligatorio
- Permitir solo rutas de cambio de contraseña y logout
- Validar que nueva contraseña cumpla políticas
```

### **3.2 Crear Middleware de Setup de Liga**

```php
// app/Http/Middleware/RequireLeagueSetup.php

// Funcionalidad:
- Verificar si la liga completó el setup
- Redirigir al wizard si está pendiente
- Permitir solo rutas del wizard
- Manejar estados intermedios
```

### **3.3 Crear Guard Personalizado para Liga**

```php
// Validaciones especiales para usuarios de liga:
- Verificar que la liga esté activa
- Validar permisos específicos de liga
- Restringir acceso a recursos de otras ligas
```

---

## 🎯 **TAREA 4: WIZARD DE CONFIGURACIÓN DE LIGA**

### **4.1 Crear LeagueSetupResource**

```php
// app/Filament/Resources/LeagueSetupResource.php

// Características:
- Solo accesible por league_admin
- Wizard de 7 pasos
- Guardado automático por paso
- Validación en tiempo real
- Preview de configuración
```

### **4.2 Diseñar Pasos del Wizard**

#### **Paso 1: Cambio de Contraseña Obligatorio**

```php
- Verificar contraseña actual temporal
- Validar nueva contraseña (política de seguridad)
- Confirmar nueva contraseña
- Activar cuenta permanentemente
```

#### **Paso 2: Información Básica de la Liga**

```php
- Nombre oficial de la liga
- Logo de la liga (upload)
- Colores corporativos
- Descripción/misión
- Redes sociales
```

#### **Paso 3: Configuración de Temporada**

```php
- Duración de temporada (meses)
- Fechas de inicio/fin
- Períodos de registro
- Períodos de transferencias
- Descansos/holidays
```

#### **Paso 4: Categorías y Divisiones**

```php
- Seleccionar categorías activas (de las por defecto)
- Crear categorías personalizadas
- Configurar divisiones (A, B, C, etc.)
- Límites de edad personalizados
- Reglas especiales por categoría
```

#### **Paso 5: Formato de Torneos**

```php
- Tipo de torneo (round-robin, eliminación, mixto)
- Número máximo de equipos por categoría
- Sistema de puntuación
- Criterios de desempate
- Configuración de playoffs
```

#### **Paso 6: Configuración Financiera**

```php
- Cuota de inscripción por equipo
- Cuotas de jugadoras
- Métodos de pago aceptados
- Políticas de reembolso
- Descuentos/becas
```

#### **Paso 7: Árbitros y Oficiales**

```php
- Modo de asignación (automático/manual)
- Crear lista inicial de árbitros
- Tarifas de arbitraje
- Políticas de evaluación
- Certificaciones requeridas
```

#### **Paso 8: Revisión y Activación**

```php
- Resumen completo de configuración
- Lista de verificación final
- Términos y condiciones
- Activación oficial de la liga
```

### **4.3 Crear Validaciones por Paso**

```php
// app/Http/Requests/LeagueSetup/

- PasswordChangeRequest.php
- BasicInfoRequest.php
- SeasonConfigRequest.php
- CategoriesRequest.php
- TournamentFormatRequest.php
- FinancialConfigRequest.php
- RefereesConfigRequest.php
- FinalReviewRequest.php
```

---

## 🎯 **TAREA 5: SERVICIOS DE CONFIGURACIÓN DE LIGA**

### **5.1 Crear LeagueOnboardingService**

```php
// app/Services/LeagueOnboardingService.php

// Métodos:
- initializeLeagueSetup(League $league): void
- updateSetupStep(League $league, string $step, array $data): bool
- validateStepData(string $step, array $data): array
- getSetupProgress(League $league): array
- completeSetup(League $league): bool
- generateLeagueDefaults(League $league): array
```

### **5.2 Crear LeagueConfigurationService**

```php
// app/Services/LeagueConfigurationService.php

// Métodos:
- createDefaultCategories(League $league): void
- setupTournamentFormat(League $league, array $config): void
- configureFinancialSettings(League $league, array $config): void
- setupRefereeManagement(League $league, array $config): void
- validateConfiguration(League $league): array
- activateLeague(League $league): bool
```

### **5.3 Crear LeaguePermissionService**

```php
// app/Services/LeaguePermissionService.php

// Métodos:
- assignLeaguePermissions(User $user, League $league): void
- revokeLeaguePermissions(User $user, League $league): void
- validateLeagueAccess(User $user, League $league): bool
- getLeagueCapabilities(User $user, League $league): array
```

---

## 🎯 **TAREA 6: INTERFACES ESPECÍFICAS DEL WIZARD**

### **6.1 Crear Componentes Filament para Liga**

```php
// app/Filament/Components/LeagueSetup/

- LeagueSetupWizard.php (Wizard principal)
- PasswordChangeForm.php (Cambio de contraseña)
- CategorySelector.php (Selector de categorías)
- TournamentFormatBuilder.php (Constructor de formato)
- FinancialConfigForm.php (Configuración financiera)
- RefereeManagementPanel.php (Panel de árbitros)
- ConfigurationPreview.php (Preview final)
```

### **6.2 Crear Páginas Filament Personalizadas**

```php
// app/Filament/Pages/LeagueSetup/

- LeagueOnboardingPage.php (Página principal del wizard)
- SetupProgressPage.php (Página de progreso)
- ConfigurationSummaryPage.php (Resumen de configuración)
```

### **6.3 Implementar Diseño Visual Específico**

```css
// resources/css/league-setup.css

- Diseño del wizard específico para ligas
- Branding personalizable
- Progress indicators
- Validación visual en tiempo real
- Mobile responsive
```

---

## 🎯 **TAREA 7: SISTEMA DE ACTIVACIÓN Y VALIDACIÓN**

### **7.1 Crear Sistema de Activación de Liga**

```php
// app/Services/LeagueActivationService.php

// Métodos:
- validateReadyForActivation(League $league): array
- activateLeague(League $league): bool
- deactivateLeague(League $league, string $reason): bool
- getActivationRequirements(): array
- notifyActivationStatus(League $league): void
```

### **7.2 Crear Validaciones de Activación**

```php
// Requisitos para activar una liga:
- Configuración básica completa
- Al menos una categoría activa
- Información financiera configurada
- Políticas de torneo definidas
- Usuario administrador verificado
- Términos y condiciones aceptados
```

### **7.3 Crear Sistema de Notificaciones Post-Activación**

```php
// app/Notifications/LeagueActivatedNotification.php

// Notificar a:
- Administrador de la liga (confirmación)
- Superadmin del sistema (nueva liga activa)
- Árbitros asignados (si los hay)

// Contenido:
- Confirmación de activación
- Próximos pasos
- Recursos disponibles
- Contacto de soporte
```

---

## 🎯 **TAREA 8: DASHBOARD POST-ONBOARDING**

### **8.1 Crear Dashboard de Liga**

```php
// app/Filament/Resources/LeagueDashboardResource.php

// Widgets incluidos:
- Estado general de la liga
- Resumen de configuración
- Estadísticas básicas (equipos, jugadores, etc.)
- Acciones rápidas
- Próximos partidos/eventos
- Notificaciones importantes
```

### **8.2 Crear Menu Contextual de Liga**

```php
// Navegación específica para administradores de liga:
- Dashboard principal
- Gestión de equipos
- Gestión de jugadores
- Configuración de torneos
- Árbitros y oficiales
- Reportes y estadísticas
- Configuración de liga
```

### **8.3 Implementar Widgets de Estado**

```php
// app/Filament/Widgets/League/

- LeagueOverviewWidget.php (Estado general)
- SetupProgressWidget.php (Progreso de configuración)
- QuickActionsWidget.php (Acciones rápidas)
- NotificationsWidget.php (Notificaciones)
- UpcomingEventsWidget.php (Próximos eventos)
```

---

## 🎯 **TAREA 9: SISTEMA DE ROLLBACK Y RECOVERY**

### **9.1 Crear Sistema de Backup de Configuración**

```php
// app/Services/LeagueBackupService.php

// Métodos:
- createConfigurationBackup(League $league): string
- restoreConfiguration(League $league, string $backupId): bool
- listBackups(League $league): array
- cleanOldBackups(League $league): void
```

### **9.2 Implementar Rollback de Setup**

```php
// Funcionalidad para:
- Volver a pasos anteriores del wizard
- Restaurar configuración anterior
- Manejar errores de configuración
- Recovery automático
```

### **9.3 Crear Sistema de Auditoría**

```php
// Registro completo de:
- Cada paso del wizard completado
- Cambios en configuración
- Activaciones/desactivaciones
- Errores y resoluciones
- Accesos y modificaciones
```

---

## 🎯 **TAREA 10: TESTING Y VALIDACIÓN**

### **10.1 Crear Tests del Sistema de Onboarding**

```php
// tests/Feature/LeagueOnboarding/

- LeagueCreationTest.php (Creación automática de usuario)
- TemporaryPasswordTest.php (Sistema de contraseñas temporales)
- WizardFlowTest.php (Flujo completo del wizard)
- ConfigurationValidationTest.php (Validaciones)
- ActivationTest.php (Proceso de activación)
- NotificationTest.php (Sistema de notificaciones)
```

### **10.2 Crear Tests de Integración**

```php
// tests/Integration/

- CompleteOnboardingFlowTest.php (Flujo end-to-end)
- EmailNotificationTest.php (Envío de emails)
- PermissionAssignmentTest.php (Asignación de permisos)
- ConfigurationPersistenceTest.php (Persistencia de datos)
```

### **10.3 Crear Factories para Testing**

```php
// database/factories/

- LeagueConfigurationFactory.php
- TemporaryPasswordFactory.php
- LeagueUserFactory.php
```

---

## 🎯 **TAREA 11: DOCUMENTACIÓN Y TRAINING**

### **11.1 Crear Documentación Técnica**

```markdown
// docs/league-onboarding/

- setup-flow-diagram.md (Diagrama de flujo)
- configuration-options.md (Opciones de configuración)
- troubleshooting.md (Resolución de problemas)
- api-reference.md (Referencia de servicios)
```

### **11.2 Crear Guías de Usuario**

```markdown
// docs/user-guides/

- league-admin-quickstart.md
- configuration-best-practices.md
- common-setup-issues.md
- post-activation-steps.md
```

### **11.3 Crear Material de Training**

```php
// Recursos para entrenamiento:
- Video tutorials (grabación de pantalla)
- FAQ interactivo
- Casos de uso comunes
- Mejores prácticas
```

---

## 📊 **MÉTRICAS DE ÉXITO**

### **Funcionales:**

- [ ] Creación automática de usuario liga funcional
- [ ] Envío de emails de bienvenida automatizado  
- [ ] Wizard completable en menos de 20 minutos
- [ ] Todos los pasos con validación robusta
- [ ] Sistema de rollback funcional
- [ ] Activación automática post-configuración

### **Técnicas:**

- [ ] Cobertura de tests > 95%
- [ ] Manejo de errores exhaustivo
- [ ] Performance óptima (< 2s por paso)
- [ ] Seguridad robusta (contraseñas, permisos)
- [ ] Escalabilidad para 100+ ligas

### **UX:**

- [ ] Flujo intuitivo y claro
- [ ] Feedback visual inmediato
- [ ] Mensajes de error útiles
- [ ] Progreso visible en todo momento
- [ ] Diseño mobile-friendly

### **Negocio:**

- [ ] Reducción del tiempo de setup de ligas
- [ ] Menor soporte técnico requerido
- [ ] Mayor adopción del sistema
- [ ] Configuraciones más consistentes

---

## ⏱️ **ESTIMACIÓN DE TIEMPO**

- **Tarea 1-2:** 4-5 días (Creación automática y notificaciones)
- **Tarea 3-4:** 5-6 días (Middleware y wizard)
- **Tarea 5-6:** 3-4 días (Servicios e interfaces)
- **Tarea 7-8:** 3-4 días (Activación y dashboard)
- **Tarea 9-10:** 3-4 días (Rollback y testing)
- **Tarea 11:** 2 días (Documentación)

**Total estimado: 20-25 días de desarrollo**

---

## 🔄 **DEPENDENCIAS Y PRE-REQUISITOS**

### **Dependencias:**

- Sistema de Setup Superadmin completado
- Sistema de federación funcional
- Roles y permisos base implementados
- Sistema de emails configurado

### **Integración con:**

- Filament Resources existentes
- Sistema de notificaciones
- Gestión de medios (logos)
- Sistema de auditoría
