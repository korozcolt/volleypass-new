# 🧹 PLAN DE LIMPIEZA FRONTEND Y REVISIÓN BACKEND - VolleyPass

## 🔥 **FASE 1: LIMPIEZA TOTAL DEL FRONTEND**

### **Archivos/Carpetas a ELIMINAR:**

```bash
# 1. Eliminar todos los componentes React/Inertia
rm -rf resources/js/Components/
rm -rf resources/js/Pages/
rm -rf resources/js/Hooks/
rm -rf resources/js/Layouts/
rm -rf resources/js/utils/

# 2. Eliminar archivos de configuración frontend
rm -f resources/js/app.tsx
rm -f resources/js/bootstrap.js
rm -f resources/js/types/
rm -f tsconfig.json
rm -f postcss.config.js

# 3. Limpiar package.json (mantener solo dependencias de admin)
# Eliminar: React, Inertia, TypeScript, etc.

# 4. Eliminar vistas Blade relacionadas con frontend público
rm -rf resources/views/referee/
rm -rf resources/views/club/
rm -rf resources/views/player/
rm -rf resources/views/auth/ # Usar solo Filament auth

# 5. Mantener solo vistas necesarias para admin
# resources/views/app.blade.php (solo para Filament)
# resources/views/filament/ (recursos de admin)
```

### **Rutas a LIMPIAR:**

```php
// routes/web.php - LIMPIAR TODO EXCEPTO:
<?php

use Illuminate\Support\Facades\Route;

// Solo ruta básica
Route::get('/', function () {
    return redirect('/admin');
});

// Todas las rutas /referee, /club, /player - ELIMINAR
// Solo mantener rutas de Filament (auto-registradas)
```

### **Controllers a REVISAR/ELIMINAR:**

```bash
# Eliminar controllers de frontend
rm -f app/Http/Controllers/Referee/RefereeController.php
rm -f app/Http/Controllers/Club/ClubController.php
rm -f app/Http/Controllers/Player/PlayerController.php

# Mantener solo:
# - Controllers de API (si existen)
# - Resources de Filament
# - Middleware necesarios
```

---

## 🏗️ **FASE 2: REVISIÓN BACKEND SEGÚN README**

### **📋 ESTADO ACTUAL vs README REQUIREMENTS**

#### ✅ **YA IMPLEMENTADO (según documentación):**

1. **Sistema de Federación** ✅
   - Estados: No Federado, Pago Enviado, Federado, Vencido, Suspendido
   - Servicios: FederationService, PaymentValidationService
   - Tests: 41+ tests pasando

2. **Estructura Base** ✅
   - Ubicaciones geográficas (Colombia completo)
   - Sistema multi-rol con permisos
   - Paquetes Spatie integrados
   - Base de datos con 45+ tablas

3. **Modelos Core** ✅
   - Users, UserProfiles
   - Teams, Players
   - Leagues, Clubs
   - Federación completa

#### 🚧 **PENDIENTE POR IMPLEMENTAR:**

### **A. SISTEMA DE CONFIGURACIÓN INICIAL**

#### **A1. Setup Superadmin (Paso a Paso Gráfico)**

```php
// Nueva funcionalidad requerida
- [ ] Wizard de configuración inicial post-instalación
- [ ] Configuración de categorías por defecto del sistema
- [ ] Upload de logo de la aplicación
- [ ] Configuración de nombre de la app
- [ ] Configuración de datos de contacto
- [ ] Configuración de moneda y región
- [ ] Configuración de reglas de voleibol por defecto
```

#### **A2. Sistema de Onboarding para Ligas**

```php
// Nueva funcionalidad requerida
- [ ] Wizard de configuración de liga (post-creación)
- [ ] Setup de categorías específicas de la liga
- [ ] Configuración de reglas de torneo
- [ ] Setup de clubes iniciales
- [ ] Configuración de temporadas
- [ ] Setup de árbitros de la liga
```

### **B. SISTEMA DE TORNEOS Y PARTIDOS**

#### **B1. Gestión de Torneos** 🔴 CRÍTICO

```php
- [ ] TournamentResource (Filament)
- [ ] Sistema de fixtures automático
- [ ] Configuración de formatos (round-robin, eliminación)
- [ ] Sistema de clasificación automática
- [ ] Generación de calendarios
```

#### **B2. Control de Partidos en Tiempo Real** 🔴 CRÍTICO

```php
- [ ] MatchResource (Filament) 
- [ ] Sistema de marcadores en vivo
- [ ] Control de rotaciones de voleibol
- [ ] Sistema de sustituciones
- [ ] Gestión de sanciones/tarjetas
- [ ] Estadísticas de partido en tiempo real
```

### **C. SISTEMA DE ÁRBITROS**

#### **C1. Gestión de Árbitros** 🟡 MEDIA PRIORIDAD

```php
- [ ] RefereeResource (Filament)
- [ ] Sistema de certificaciones
- [ ] Asignación automática de partidos
- [ ] Evaluación de desempeño
- [ ] Disponibilidad y horarios
```

### **D. SISTEMA DE TRANSFERS Y CARNETS**

#### **D1. Transferencias** 🟡 MEDIA PRIORIDAD

```php
- [ ] TransferResource (Filament)
- [ ] Workflow de aprobación multi-nivel
- [ ] Validaciones de períodos de transferencia
- [ ] Historial de transferencias
```

#### **D2. Carnetización Digital** 🟡 MEDIA PRIORIDAD

```php
- [ ] PlayerCardResource (Filament)
- [ ] Generación de QR codes únicos
- [ ] Sistema de verificación por contexto
- [ ] Carnets diferenciados por tipo de liga
```

### **E. SISTEMA DE PAGOS Y VALIDACIONES**

#### **E1. Gestión de Pagos** 🟡 MEDIA PRIORIDAD

```php
- [ ] PaymentResource (Filament)
- [ ] Validación automática de comprobantes
- [ ] Estados de pago con workflow
- [ ] Reportes financieros
```

### **F. ANALYTICS Y REPORTES**

#### **F1. Dashboard Avanzado** 🟢 BAJA PRIORIDAD

```php
- [ ] Métricas por tipo de liga
- [ ] Estadísticas de jugadoras
- [ ] Reportes de rendimiento
- [ ] Analytics de uso del sistema
```

---

## 📧 **FASE 3: SISTEMA DE NOTIFICACIONES Y ONBOARDING**

### **A. Sistema de Creación de Ligas con Notificación**

#### **A1. Workflow de Creación de Liga**

```php
// app/Filament/Resources/LeagueResource.php - EXTENDER
- [ ] Campo 'admin_email' en formulario de creación
- [ ] Generación automática de contraseña temporal
- [ ] Envío de email de bienvenida automático
- [ ] Estado 'setup_pending' para ligas recién creadas
```

#### **A2. Sistema de Notificaciones**

```php
// Nuevas clases a crear:
- [ ] LeagueCreatedNotification (email)
- [ ] WelcomeToLeagueJob (queue job)
- [ ] TemporaryPasswordService
- [ ] LeagueOnboardingService
```

### **B. Sistema de Passwords Temporales**

#### **B1. Gestión de Passwords Temporales**

```php
// Nueva funcionalidad:
- [ ] Tabla 'temporary_passwords'
- [ ] Middleware para forzar cambio de contraseña
- [ ] Validación de primera sesión
- [ ] Expiración automática de passwords temporales
```

### **C. Wizard de Configuración de Liga**

#### **C1. Pasos del Wizard**

```php
// Pasos requeridos:
1. [ ] Cambio de contraseña obligatorio
2. [ ] Configuración básica de la liga
3. [ ] Setup de categorías de la liga
4. [ ] Configuración de reglas de torneo
5. [ ] Creación de clubes iniciales (opcional)
6. [ ] Invitación a árbitros (opcional)
7. [ ] Finalización y activación de la liga
```

---

## 🎯 **PRIORIZACIÓN DE TAREAS**

### **🔴 PRIORIDAD CRÍTICA (Semana 1-2)**

1. **Limpieza total del frontend**
2. **Sistema de configuración inicial Superadmin**
3. **Sistema de onboarding para Ligas**
4. **Notificaciones de creación de liga**
5. **TournamentResource básico**

### **🟡 PRIORIDAD MEDIA (Semana 3-4)**

1. **MatchResource y control de partidos**
2. **Sistema de árbitros básico**
3. **Sistema de transferencias**
4. **Carnetización digital**

### **🟢 PRIORIDAD BAJA (Semana 5+)**

1. **Analytics avanzados**
2. **Reportes complejos**
3. **Optimizaciones de performance**
4. **Features adicionales**

---

## 📝 **CHECKLIST DE LIMPIEZA INMEDIATA**

### **Frontend Cleanup:**

- [ ] Eliminar carpetas React/Inertia
- [ ] Limpiar package.json
- [ ] Eliminar rutas de frontend
- [ ] Eliminar controllers de frontend
- [ ] Mantener solo rutas admin

### **Backend Review:**

- [ ] Auditar Resources de Filament existentes
- [ ] Verificar servicios implementados
- [ ] Revisar tests existentes
- [ ] Documentar APIs faltantes

### **Nuevas Implementaciones:**

- [ ] Sistema de configuración inicial
- [ ] Wizard de onboarding
- [ ] Notificaciones por email
- [ ] Gestión de contraseñas temporales

---

**🎯 Objetivo:** Tener un backend robusto 100% funcional con Filament como única interfaz, eliminando toda complejidad de frontend hasta que esté listo para implementar la solución definitiva.
