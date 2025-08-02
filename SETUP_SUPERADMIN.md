# 🔧 TAREAS: SISTEMA DE SETUP SUPERADMIN

## 📋 **OBJETIVO**

Crear un sistema de configuración inicial paso a paso para el superadmin, que permita configurar toda la información base del sistema de manera gráfica e intuitiva.

---

## 🎯 **TAREA 1: CREAR ESTRUCTURA DE CONFIGURACIÓN DEL SISTEMA**

### **1.1 Crear Modelo de Configuración del Sistema**

```php
// php artisan make:model SystemConfiguration -m

// Campos requeridos:
- app_name (string)
- app_logo (media library)
- primary_color (string) 
- secondary_color (string)
- contact_email (string)
- contact_phone (string)
- address (text)
- currency (string, default: COP)
- timezone (string, default: America/Bogota)
- volleyball_rules (json) // Reglas por defecto
- setup_completed (boolean, default: false)
- created_by (unsignedBigInteger, foreign key users)
```

### **1.2 Crear Seeder de Categorías Por Defecto**

```php
// database/seeders/DefaultCategoriesSeeder.php

// Categorías estándar de voleibol:
1. Pre-Infantil (8-10 años)
2. Infantil (11-12 años) 
3. Pre-Juvenil (13-14 años)
4. Juvenil (15-16 años)
5. Junior (17-19 años)
6. Mayor (20+ años)
7. Master (35+ años)

// Cada categoría con:
- name, min_age, max_age, gender, special_rules
```

### **1.3 Crear Sistema de Estados de Setup**

```php
// app/Enums/SetupStatus.php

enum SetupStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress'; 
    case Completed = 'completed';
    case RequiresUpdate = 'requires_update';
}
```

---

## 🎯 **TAREA 2: CREAR WIZARD DE CONFIGURACIÓN INICIAL**

### **2.1 Crear SystemSetupResource (Filament)**

```php
// app/Filament/Resources/SystemSetupResource.php

// Funcionalidades:
- Solo accesible por superadmin
- Wizard de múltiples pasos
- Validación en cada paso
- Preview en tiempo real
- Guardado automático por pasos
```

### **2.2 Diseñar Pasos del Wizard**

#### **Paso 1: Información Básica de la Aplicación**

```php
- Nombre de la aplicación
- Logo principal (upload con preview)
- Slogan/descripción corta
- Colores primarios (color picker)
```

#### **Paso 2: Información de Contacto**

```php
- Email de contacto principal
- Teléfono de contacto
- Dirección física
- Redes sociales (opcional)
```

#### **Paso 3: Configuración Regional**

```php
- Zona horaria
- Moneda (COP por defecto)
- Formato de fecha
- Idioma del sistema
```

#### **Paso 4: Reglas de Voleibol Por Defecto**

```php
- Sets por partido (3 o 5)
- Puntos por set (25 o configurado)
- Tiempo entre sets
- Reglas de rotación
- Reglas de sustitución
```

#### **Paso 5: Categorías Por Defecto**

```php
- Lista de categorías estándar
- Posibilidad de editar edades
- Habilitar/deshabilitar categorías
- Reglas especiales por categoría
```

#### **Paso 6: Configuración de Usuarios Administrativos**

```php
- Crear usuario administrador adicional (opcional)
- Configurar permisos iniciales
- Setup de notificaciones
```

#### **Paso 7: Revisión y Finalización**

```php
- Preview de toda la configuración
- Confirmación final
- Activación del sistema
```

### **2.3 Crear Middleware de Setup Obligatorio**

```php
// app/Http/Middleware/RequireSystemSetup.php

// Redirigir al wizard si setup no está completo
// Excepto rutas del wizard y logout
```

---

## 🎯 **TAREA 3: INTERFACES GRÁFICAS DEL WIZARD**

### **3.1 Crear Componentes Filament Personalizados**

```php
// app/Filament/Components/SetupWizard/

- WizardStepCard.php (Card para cada paso)
- ColorPicker.php (Selector de colores)
- LogoUploader.php (Upload con preview)
- ConfigurationPreview.php (Preview en tiempo real)
- ProgressIndicator.php (Indicador de progreso)
```

### **3.2 Crear Estilos Personalizados**

```css
// resources/css/setup-wizard.css

- Diseño del wizard paso a paso
- Transiciones suaves
- Indicadores visuales
- Responsive design
- Elementos de preview
```

### **3.3 Implementar Validaciones por Paso**

```php
// app/Http/Requests/SystemSetup/

- BasicInfoRequest.php
- ContactInfoRequest.php
- RegionalConfigRequest.php
- VolleyballRulesRequest.php
- CategoriesConfigRequest.php
```

---

## 🎯 **TAREA 4: SERVICIOS DE CONFIGURACIÓN**

### **4.1 Crear SystemConfigurationService**

```php
// app/Services/SystemConfigurationService.php

// Métodos:
- createInitialConfiguration()
- updateConfigurationStep()
- validateStepData()
- completeSetup()
- getSetupProgress()
- resetConfiguration()
```

### **4.2 Crear DefaultDataService**

```php
// app/Services/DefaultDataService.php

// Métodos:
- seedDefaultCategories()
- createDefaultRoles()
- setupDefaultPermissions()
- createSystemSettings()
- validateDefaults()
```

### **4.3 Crear ConfigurationValidationService**

```php
// app/Services/ConfigurationValidationService.php

// Métodos:
- validateBasicInfo()
- validateContactInfo()
- validateRegionalConfig()
- validateVolleyballRules()
- validateCategories()
- validateComplete()
```

---

## 🎯 **TAREA 5: SISTEMA DE GUARDADO Y RECUPERACIÓN**

### **5.1 Implementar Auto-Save**

```php
// Guardado automático en cada paso
// Recuperación de progreso al volver
// Sistema de drafts
```

### **5.2 Crear Sistema de Backup de Configuración**

```php
// Backup automático antes de cambios
// Restauración de configuración anterior
// Export/Import de configuraciones
```

### **5.3 Implementar Validación Cruzada**

```php
// Validar consistencia entre pasos
// Detectar conflictos de configuración
// Sugerir mejores prácticas
```

---

## 🎯 **TAREA 6: DASHBOARD POST-SETUP**

### **6.1 Crear Dashboard de Configuración**

```php
// Vista resumen de toda la configuración
// Enlaces rápidos para editar cada sección
// Estado de salud del sistema
// Métricas de configuración
```

### **6.2 Implementar Sistema de Alerts**

```php
// Alertas por configuración incompleta
// Sugerencias de mejora
// Recordatorios de actualización
```

---

## 🎯 **TAREA 7: TESTING Y VALIDACIÓN**

### **7.1 Crear Tests del Sistema de Setup**

```php
// tests/Feature/SystemSetup/

- SetupWizardTest.php
- ConfigurationValidationTest.php  
- DefaultDataTest.php
- PermissionsTest.php
```

### **7.2 Crear Factories para Testing**

```php
// database/factories/

- SystemConfigurationFactory.php
- CategoryFactory.php (con defaults)
```

---

## 📊 **MÉTRICAS DE ÉXITO**

### **Funcionales:**

- [ ] Wizard completable en menos de 10 minutos
- [ ] Todos los pasos con validación clara
- [ ] Preview en tiempo real funcional
- [ ] Auto-save sin pérdida de datos
- [ ] Sistema accesible solo por superadmin

### **Técnicas:**

- [ ] Cobertura de tests > 90%
- [ ] Validaciones exhaustivas
- [ ] Manejo de errores robusto
- [ ] Performance óptima
- [ ] Código bien documentado

### **UX:**

- [ ] Interfaz intuitiva y clara
- [ ] Flujo lógico y natural
- [ ] Feedback visual inmediato
- [ ] Diseño responsive
- [ ] Accesibilidad completa

---

## ⏱️ **ESTIMACIÓN DE TIEMPO**

- **Tarea 1-2:** 3-4 días (Estructura y Wizard básico)
- **Tarea 3:** 2-3 días (Interfaces gráficas)
- **Tarea 4-5:** 2-3 días (Servicios y guardado)
- **Tarea 6-7:** 2 días (Dashboard y testing)

**Total estimado: 9-12 días de desarrollo**
