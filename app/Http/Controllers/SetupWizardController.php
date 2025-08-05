<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetupWizardController extends Controller
{
    /**
     * Mostrar el wizard de configuración inicial
     */
    public function index()
    {
        // Verificar si el sistema ya está configurado
        if ($this->isSystemConfigured()) {
            return redirect('/admin')->with('info', 'El sistema ya está configurado.');
        }

        return view('setup.wizard');
    }

    /**
     * Procesar un paso del wizard
     */
    public function processStep(Request $request)
    {
        $step = $request->input('step');
        $data = $request->input('data', []);

        try {
            switch ($step) {
                case 1:
                    return $this->processGeneralConfig($data);
                case 2:
                    return $this->processLeagueConfig($data);
                case 3:
                    return $this->processGeographyConfig($data);
                case 4:
                    return $this->processCategoriesConfig($data);
                case 5:
                    return $this->processSecurityConfig($data);
                case 6:
                    return $this->processDatabaseConfig($data);
                case 7:
                    return $this->completeSetup($data);
                default:
                    return response()->json(['error' => 'Paso inválido'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error en wizard paso ' . $step . ': ' . $e->getMessage());
            return response()->json(['error' => 'Error procesando el paso'], 500);
        }
    }

    /**
     * Verificar si el sistema está configurado
     */
    private function isSystemConfigured(): bool
    {
        return Cache::get('system_configured', false) || 
               DB::table('settings')->where('key', 'system_configured')->value('value') === 'true';
    }

    /**
     * Procesar configuración general
     */
    private function processGeneralConfig(array $data)
    {
        // Validar datos generales
        $validated = validator($data, [
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'timezone' => 'required|string',
            'locale' => 'required|string|in:es,en',
        ])->validate();

        // Guardar configuración general
        $this->saveStepData('general_config', $validated);

        return response()->json(['success' => true, 'message' => 'Configuración general guardada']);
    }

    /**
     * Procesar configuración de liga
     */
    private function processLeagueConfig(array $data)
    {
        $validated = validator($data, [
            'league_name' => 'required|string|max:255',
            'league_code' => 'required|string|max:10|unique:leagues,code',
            'league_description' => 'nullable|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
        ])->validate();

        $this->saveStepData('league_config', $validated);

        return response()->json(['success' => true, 'message' => 'Configuración de liga guardada']);
    }

    /**
     * Procesar configuración geográfica
     */
    private function processGeographyConfig(array $data)
    {
        $validated = validator($data, [
            'departments' => 'required|array|min:1',
            'departments.*.name' => 'required|string|max:255',
            'departments.*.cities' => 'required|array|min:1',
            'departments.*.cities.*.name' => 'required|string|max:255',
        ])->validate();

        $this->saveStepData('geography_config', $validated);

        return response()->json(['success' => true, 'message' => 'Configuración geográfica guardada']);
    }

    /**
     * Procesar configuración de categorías
     */
    private function processCategoriesConfig(array $data)
    {
        $validated = validator($data, [
            'categories' => 'required|array|min:1',
            'categories.*.name' => 'required|string|max:255',
            'categories.*.min_age' => 'required|integer|min:0',
            'categories.*.max_age' => 'required|integer|min:0',
            'categories.*.gender' => 'required|in:M,F,mixed',
        ])->validate();

        $this->saveStepData('categories_config', $validated);

        return response()->json(['success' => true, 'message' => 'Configuración de categorías guardada']);
    }

    /**
     * Procesar configuración de seguridad
     */
    private function processSecurityConfig(array $data)
    {
        $validated = validator($data, [
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:8',
            'enable_2fa' => 'boolean',
            'session_timeout' => 'required|integer|min:30',
        ])->validate();

        // No guardar la contraseña en texto plano
        $validated['admin_password'] = bcrypt($validated['admin_password']);
        
        $this->saveStepData('security_config', $validated);

        return response()->json(['success' => true, 'message' => 'Configuración de seguridad guardada']);
    }

    /**
     * Procesar configuración de base de datos
     */
    private function processDatabaseConfig(array $data)
    {
        $validated = validator($data, [
            'seed_sample_data' => 'boolean',
            'create_demo_clubs' => 'boolean',
            'create_demo_players' => 'boolean',
        ])->validate();

        $this->saveStepData('database_config', $validated);

        return response()->json(['success' => true, 'message' => 'Configuración de base de datos guardada']);
    }

    /**
     * Completar la configuración
     */
    private function completeSetup(array $data)
    {
        DB::beginTransaction();
        
        try {
            // Obtener todos los datos guardados
            $allData = $this->getAllStepData();
            
            // Crear la liga principal
            $this->createMainLeague($allData['league_config'] ?? []);
            
            // Crear departamentos y ciudades
            $this->createGeography($allData['geography_config'] ?? []);
            
            // Crear categorías
            $this->createCategories($allData['categories_config'] ?? []);
            
            // Crear usuario administrador
            $this->createAdminUser($allData['security_config'] ?? []);
            
            // Configurar datos de muestra si se solicitó
            if (($allData['database_config']['seed_sample_data'] ?? false)) {
                $this->seedSampleData($allData['database_config']);
            }
            
            // Marcar el sistema como configurado
            $this->markSystemAsConfigured();
            
            // Limpiar datos temporales
            $this->clearStepData();
            
            DB::commit();
            
            return response()->json([
                'success' => true, 
                'message' => 'Configuración completada exitosamente',
                'redirect' => '/admin'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completando setup: ' . $e->getMessage());
            return response()->json(['error' => 'Error completando la configuración'], 500);
        }
    }

    /**
     * Guardar datos de un paso
     */
    private function saveStepData(string $step, array $data)
    {
        Cache::put("wizard_step_{$step}", $data, now()->addHours(24));
    }

    /**
     * Obtener todos los datos de los pasos
     */
    private function getAllStepData(): array
    {
        return [
            'general_config' => Cache::get('wizard_step_general_config', []),
            'league_config' => Cache::get('wizard_step_league_config', []),
            'geography_config' => Cache::get('wizard_step_geography_config', []),
            'categories_config' => Cache::get('wizard_step_categories_config', []),
            'security_config' => Cache::get('wizard_step_security_config', []),
            'database_config' => Cache::get('wizard_step_database_config', []),
        ];
    }

    /**
     * Limpiar datos temporales
     */
    private function clearStepData()
    {
        $steps = ['general_config', 'league_config', 'geography_config', 'categories_config', 'security_config', 'database_config'];
        foreach ($steps as $step) {
            Cache::forget("wizard_step_{$step}");
        }
    }

    /**
     * Crear la liga principal
     */
    private function createMainLeague(array $config)
    {
        if (empty($config)) return;
        
        // Implementar creación de liga
        // DB::table('leagues')->insert($config);
    }

    /**
     * Crear geografía
     */
    private function createGeography(array $config)
    {
        if (empty($config)) return;
        
        // Implementar creación de departamentos y ciudades
    }

    /**
     * Crear categorías
     */
    private function createCategories(array $config)
    {
        if (empty($config)) return;
        
        // Implementar creación de categorías
    }

    /**
     * Crear usuario administrador
     */
    private function createAdminUser(array $config)
    {
        if (empty($config)) return;
        
        // Implementar creación de usuario admin
    }

    /**
     * Sembrar datos de muestra
     */
    private function seedSampleData(array $config)
    {
        // Implementar seeding de datos de muestra
    }

    /**
     * Marcar sistema como configurado
     */
    private function markSystemAsConfigured()
    {
        Cache::put('system_configured', true, now()->addYears(10));
        
        // También en base de datos
        DB::table('settings')->updateOrInsert(
            ['key' => 'system_configured'],
            ['value' => 'true', 'updated_at' => now()]
        );
    }
}