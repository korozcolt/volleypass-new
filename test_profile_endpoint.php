<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\UserProfileController;
use App\Models\User;
use App\Models\UserProfile;

// Crear una instancia de la aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Obtener el primer usuario
    $user = User::first();
    
    if (!$user) {
        echo "❌ No se encontraron usuarios en la base de datos\n";
        exit(1);
    }
    
    echo "✅ Usuario encontrado: {$user->name} ({$user->email})\n";
    
    // Verificar si tiene perfil
    $profile = $user->profile;
    if (!$profile) {
        echo "📝 Creando perfil para el usuario...\n";
        UserProfile::create([
            'user_id' => $user->id,
            'nickname' => 'TestUser',
            'bio' => 'Usuario de prueba',
        ]);
        $user->refresh();
    }
    
    // Simular una request autenticada
    $request = Request::create('/api/v1/users/profile', 'GET');
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    // Crear instancia del controlador
    $controller = new UserProfileController();
    
    // Ejecutar el método show
    $response = $controller->show($request);
    
    echo "\n📊 Respuesta del endpoint:\n";
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
    
    $data = json_decode($response->getContent(), true);
    
    if ($data && $data['success']) {
        echo "\n✅ El endpoint /users/profile funciona correctamente\n";
        echo "👤 Tipo de usuario: " . ($data['data']['user_type'] ?? 'N/A') . "\n";
        echo "📧 Email: " . ($data['data']['email'] ?? 'N/A') . "\n";
        echo "📱 Teléfono: " . ($data['data']['phone'] ?? 'N/A') . "\n";
        
        if (isset($data['data']['player_info'])) {
            echo "🏐 Es jugador - Información adicional incluida\n";
        } else {
            echo "👥 Usuario regular - Sin información de jugador\n";
        }
    } else {
        echo "❌ Error en la respuesta del endpoint\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}