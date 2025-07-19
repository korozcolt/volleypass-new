<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemConfiguration;

class SystemConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $configurations = [
            // Configuraciones generales
            [
                'key' => 'app.name',
                'name' => 'Nombre de la Aplicación',
                'description' => 'Nombre principal de la aplicación',
                'value' => 'Sistema de Federación Deportiva',
                'type' => 'string',
                'group' => 'general',
                'is_public' => true,
                'is_editable' => true,
            ],
            [
                'key' => 'app.description',
                'name' => 'Descripción de la Aplicación',
                'description' => 'Descripción breve de la aplicación',
                'value' => 'Sistema integral para la gestión de federaciones deportivas',
                'type' => 'string',
                'group' => 'general',
                'is_public' => true,
                'is_editable' => true,
            ],
            [
                'key' => 'app.version',
                'name' => 'Versión de la Aplicación',
                'description' => 'Versión actual del sistema',
                'value' => '1.0.0',
                'type' => 'string',
                'group' => 'general',
                'is_public' => true,
                'is_editable' => false,
            ],

            // Configuraciones de federación
            [
                'key' => 'federation.annual_fee',
                'name' => 'Cuota Anual de Federación',
                'description' => 'Monto de la cuota anual para federarse',
                'value' => '50000',
                'type' => 'number',
                'group' => 'federation',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => 'required|numeric|min:0',
            ],
            [
                'key' => 'federation.card_validity_months',
                'name' => 'Validez de Carnet (meses)',
                'description' => 'Número de meses de validez del carnet de jugadora',
                'value' => '12',
                'type' => 'number',
                'group' => 'federation',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => 'required|numeric|min:1|max:24',
            ],
            [
                'key' => 'federation.auto_approve_payments',
                'name' => 'Auto-aprobar Pagos',
                'description' => 'Aprobar automáticamente los pagos de federación',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'federation',
                'is_public' => false,
                'is_editable' => true,
            ],

            // Configuraciones de notificaciones
            [
                'key' => 'notifications.email_enabled',
                'name' => 'Notificaciones por Email',
                'description' => 'Habilitar notificaciones por correo electrónico',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'is_public' => false,
                'is_editable' => true,
            ],
            [
                'key' => 'notifications.whatsapp_enabled',
                'name' => 'Notificaciones por WhatsApp',
                'description' => 'Habilitar notificaciones por WhatsApp',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notifications',
                'is_public' => false,
                'is_editable' => true,
            ],
            [
                'key' => 'notifications.admin_email',
                'name' => 'Email del Administrador',
                'description' => 'Email para notificaciones administrativas',
                'value' => 'admin@federacion.com',
                'type' => 'email',
                'group' => 'notifications',
                'is_public' => false,
                'is_editable' => true,
                'validation_rules' => 'required|email',
            ],

            // Configuraciones de seguridad
            [
                'key' => 'security.max_login_attempts',
                'name' => 'Máximo Intentos de Login',
                'description' => 'Número máximo de intentos de login antes del bloqueo',
                'value' => '5',
                'type' => 'number',
                'group' => 'security',
                'is_public' => false,
                'is_editable' => true,
                'validation_rules' => 'required|numeric|min:3|max:10',
            ],
            [
                'key' => 'security.session_timeout',
                'name' => 'Timeout de Sesión (minutos)',
                'description' => 'Tiempo de inactividad antes de cerrar sesión automáticamente',
                'value' => '120',
                'type' => 'number',
                'group' => 'security',
                'is_public' => false,
                'is_editable' => true,
                'validation_rules' => 'required|numeric|min:30|max:480',
            ],

            // Configuraciones de archivos
            [
                'key' => 'files.max_upload_size',
                'name' => 'Tamaño Máximo de Archivo (MB)',
                'description' => 'Tamaño máximo permitido para subir archivos',
                'value' => '10',
                'type' => 'number',
                'group' => 'files',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => 'required|numeric|min:1|max:100',
            ],
            [
                'key' => 'files.allowed_extensions',
                'name' => 'Extensiones Permitidas',
                'description' => 'Extensiones de archivo permitidas para subir',
                'value' => '["jpg", "jpeg", "png", "pdf", "doc", "docx"]',
                'type' => 'json',
                'group' => 'files',
                'is_public' => true,
                'is_editable' => true,
            ],

            // Configuraciones de mantenimiento
            [
                'key' => 'maintenance.mode',
                'name' => 'Modo Mantenimiento',
                'description' => 'Activar modo de mantenimiento del sistema',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'maintenance',
                'is_public' => true,
                'is_editable' => true,
            ],
            [
                'key' => 'maintenance.message',
                'name' => 'Mensaje de Mantenimiento',
                'description' => 'Mensaje a mostrar durante el mantenimiento',
                'value' => 'El sistema está en mantenimiento. Volveremos pronto.',
                'type' => 'string',
                'group' => 'maintenance',
                'is_public' => true,
                'is_editable' => true,
            ],
        ];

        foreach ($configurations as $config) {
            SystemConfiguration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
