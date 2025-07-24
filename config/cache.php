<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache store that will be used by the
    | framework. This connection is utilized if another isn't explicitly
    | specified when running a cache operation inside the application.
    |
    */

    'default' => env('CACHE_STORE', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "array", "database", "file", "memcached",
    |                    "redis", "dynamodb", "octane", "null"
    |
    */

    'stores' => [

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CACHE_CONNECTION'),
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
            'lock_table' => env('DB_CACHE_LOCK_TABLE'),
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        // VolleyPass specific cache stores
        'categories' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'prefix' => 'volleypass_categories',
            'ttl' => 3600, // 1 hour
        ],

        'players' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'prefix' => 'volleypass_players',
            'ttl' => 1800, // 30 minutes
        ],

        'tournaments' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'prefix' => 'volleypass_tournaments',
            'ttl' => 1800, // 30 minutes
        ],

        'federation' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'prefix' => 'volleypass_federation',
            'ttl' => 900, // 15 minutes
        ],

        'qr_verification' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'prefix' => 'volleypass_qr',
            'ttl' => 300, // 5 minutes
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, and DynamoDB cache
    | stores, there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'volleypass'), '_').'_cache_'),

    /*
    |--------------------------------------------------------------------------
    | VolleyPass Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Custom cache configuration for VolleyPass specific needs including
    | TTL settings, cache tags support, and fallback mechanisms.
    |
    */

    'volleypass' => [
        'ttl' => [
            'categories' => env('CACHE_TTL_CATEGORIES', 3600), // 1 hour
            'players' => env('CACHE_TTL_PLAYERS', 1800), // 30 minutes
            'tournaments' => env('CACHE_TTL_TOURNAMENTS', 1800), // 30 minutes
            'federation' => env('CACHE_TTL_FEDERATION', 900), // 15 minutes
            'qr_verification' => env('CACHE_TTL_QR', 300), // 5 minutes
            'dashboard' => env('CACHE_TTL_DASHBOARD', 600), // 10 minutes
        ],
        'tags_enabled' => env('CACHE_TAGS_ENABLED', true),
        'fallback_to_database' => env('CACHE_FALLBACK_DB', true),
        'performance_monitoring' => env('CACHE_PERFORMANCE_MONITORING', true),
    ],

];
