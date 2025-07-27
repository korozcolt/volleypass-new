<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$appName = \App\Models\SystemConfiguration::getValue('app.name', 'VolleyPass');
$appDescription = \App\Models\SystemConfiguration::getValue('app.description', 'Sistema de gestión para la Liga de Voleibol de Sucre');
$favicon = \App\Models\SystemConfiguration::getValue('branding.favicon', '/favicon.ico');

echo json_encode([
    'name' => $appName . ' - Liga de Voleibol de Sucre',
    'short_name' => $appName,
    'description' => $appDescription,
    'start_url' => '/',
    'display' => 'standalone',
    'background_color' => '#ffffff',
    'theme_color' => '#3b82f6',
    'icons' => [
        [
            'src' => $favicon,
            'sizes' => 'any',
            'type' => 'image/x-icon'
        ],
        [
            'src' => '/favicon.svg',
            'sizes' => 'any',
            'type' => 'image/svg+xml'
        ]
    ]
], JSON_PRETTY_PRINT);
?>