<?php
// config/cors.php - CREAR archivo de configuración CORS

return [
    /*
    |--------------------------------------------------------------------------
    | CORS Mode
    |--------------------------------------------------------------------------
    |
    | Determina cómo manejar CORS para la API:
    |
    | 'open' - Permite cualquier origen (*)
    | 'restrictive' - Solo localhost y APP_URL
    | 'custom' - Usa la lista 'allowed_origins'
    |
    */
    'mode' => env('CORS_MODE', 'open'),

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Lista de orígenes permitidos cuando mode = 'custom'
    | Usar ['*'] para permitir todo
    | Dejar vacío [] para denegar todo
    |
    */
    'allowed_origins' => [
        // ✅ Se pueden agregar desde .env usando CORS_ALLOWED_ORIGINS
        // O configurar aquí directamente según necesidad
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Métodos HTTP permitidos para requests CORS
    |
    */
    'allowed_methods' => [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'OPTIONS'
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Headers permitidos en requests CORS
    |
    */
    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'X-App-Version',
        'X-Device-ID',
        'X-XSRF-TOKEN',
        'Accept',
        'Origin',
        'User-Agent'
    ],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers que el frontend puede leer
    |
    */
    'exposed_headers' => [
        'X-API-Type',
        'X-RateLimit-Remaining',
        'X-RateLimit-Limit'
    ],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Tiempo en segundos que el navegador puede cachear la respuesta preflight
    |
    */
    'max_age' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Si se permiten credentials (cookies, authorization headers)
    | Se determina automáticamente según el origen
    |
    */
    'supports_credentials' => env('CORS_SUPPORTS_CREDENTIALS', true),
];
