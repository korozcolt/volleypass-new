<?php

if (!function_exists('system_config')) {
    /**
     * Obtener una configuración del sistema
     */
    function system_config(string $key, $default = null)
    {
        return app('system.config')->get($key, $default);
    }
}

if (!function_exists('set_system_config')) {
    /**
     * Establecer una configuración del sistema
     */
    function set_system_config(string $key, $value): bool
    {
        return app('system.config')->set($key, $value);
    }
}

if (!function_exists('system_config_group')) {
    /**
     * Obtener configuraciones por grupo
     */
    function system_config_group(string $group): array
    {
        return app('system.config')->getByGroup($group);
    }
}

if (!function_exists('app_name')) {
    /**
     * Obtener el nombre de la aplicación desde configuraciones del sistema
     */
    function app_name(): string
    {
        return system_config('app.name', config('app.name', 'VolleyPass'));
    }
}

if (!function_exists('app_description')) {
    /**
     * Obtener la descripción de la aplicación
     */
    function app_description(): string
    {
        return system_config('app.description', 'Sistema de Gestión Deportiva');
    }
}

if (!function_exists('app_version')) {
    /**
     * Obtener la versión de la aplicación
     */
    function app_version(): string
    {
        return system_config('app.version', '1.0.0');
    }
}

if (!function_exists('federation_fee')) {
    /**
     * Obtener la cuota de federación
     */
    function federation_fee(): float
    {
        return (float) system_config('federation.annual_fee', 50000);
    }
}

if (!function_exists('card_validity_months')) {
    /**
     * Obtener los meses de validez del carnet
     */
    function card_validity_months(): int
    {
        return (int) system_config('federation.card_validity_months', 12);
    }
}

if (!function_exists('max_upload_size')) {
    /**
     * Obtener el tamaño máximo de subida en MB
     */
    function max_upload_size(): int
    {
        return (int) system_config('files.max_upload_size', 10);
    }
}

if (!function_exists('allowed_file_extensions')) {
    /**
     * Obtener las extensiones de archivo permitidas
     */
    function allowed_file_extensions(): array
    {
        $extensions = system_config('files.allowed_extensions', ['jpg', 'jpeg', 'png', 'pdf']);
        return is_array($extensions) ? $extensions : json_decode($extensions, true) ?? ['jpg', 'jpeg', 'png', 'pdf'];
    }
}

if (!function_exists('is_maintenance_mode')) {
    /**
     * Verificar si está en modo mantenimiento
     */
    function is_maintenance_mode(): bool
    {
        return (bool) system_config('maintenance.mode', false);
    }
}

if (!function_exists('maintenance_message')) {
    /**
     * Obtener el mensaje de mantenimiento
     */
    function maintenance_message(): string
    {
        return system_config('maintenance.message', 'El sistema está en mantenimiento. Volveremos pronto.');
    }
}
