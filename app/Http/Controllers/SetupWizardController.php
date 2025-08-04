<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\SetupState;
use App\Models\SystemConfiguration;
use App\Models\Category;
use App\Models\User;
use App\Enums\SetupStatus;
use App\Services\SystemConfigurationService;
use Spatie\Permission\Models\Role;

class SetupWizardController extends Controller
{
    protected $configService;

    public function __construct(SystemConfigurationService $configService)
    {
        $this->middleware('auth');
        $this->middleware('role:superadmin');
        $this->configService = $configService;
    }

    /**
     * Mostrar el wizard de configuración
     */
    public function index()
    {
        $currentStep = SetupState::getNextStep();
        $progress = SetupState::getOverallProgress();
        $steps = SetupState::getSetupSteps();
        
        return view('setup.wizard.index', compact('currentStep', 'progress', 'steps'));
    }

    /**
     * Mostrar un paso específico del wizard
     */
    public function showStep($step)
    {
        $steps = SetupState::getSetupSteps();
        
        if (!isset($steps[$step])) {
            return redirect()->route('setup.wizard');
        }

        $stepData = SetupState::where('step', $step)->first();
        $data = $stepData ? $stepData->data : [];
        
        $progress = SetupState::getOverallProgress();
        
        return view("setup.wizard.steps.step{$step}", compact('step', 'data', 'progress', 'steps'));
    }

    /**
     * Procesar un paso del wizard
     */
    public function processStep(Request $request, $step)
    {
        $validator = $this->validateStep($request, $step);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Guardar datos del paso
            $this->saveStepData($step, $request->all());
            
            // Aplicar configuraciones si es necesario
            $this->applyStepConfigurations($step, $request->all());
            
            DB::commit();
            
            // Determinar siguiente paso
            $nextStep = $this->getNextStep($step);
            
            if ($nextStep) {
                return redirect()->route('setup.wizard.step', $nextStep)
                    ->with('success', 'Paso completado correctamente.');
            } else {
                // Completar setup
                $this->completeSetup();
                return redirect()->route('dashboard')
                    ->with('success', '¡Configuración inicial completada exitosamente!');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el paso: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Validar datos de un paso específico
     */
    private function validateStep(Request $request, $step)
    {
        $rules = match($step) {
            1 => [
                'app_name' => 'required|string|max:255',
                'app_description' => 'nullable|string|max:500',
                'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            ],
            2 => [
                'contact_email' => 'required|email',
                'contact_phone' => 'required|string|max:20',
                'contact_address' => 'required|string|max:255',
                'website_url' => 'nullable|url',
                'social_facebook' => 'nullable|url',
                'social_instagram' => 'nullable|url',
                'social_twitter' => 'nullable|url',
            ],
            3 => [
                'federation_name' => 'required|string|max:255',
                'federation_code' => 'required|string|max:10|unique:system_configurations,value',
                'annual_fee' => 'required|numeric|min:0',
                'card_validity_months' => 'required|integer|min:1|max:60',
            ],
            4 => [
                'set_duration' => 'required|integer|min:15|max:60',
                'timeout_duration' => 'required|integer|min:30|max:300',
                'max_substitutions' => 'required|integer|min:6|max:12',
                'points_to_win_set' => 'required|integer|min:15|max:30',
                'points_difference_to_win' => 'required|integer|min:2|max:5',
            ],
            5 => [
                'categories' => 'required|array|min:1',
                'categories.*.name' => 'required|string|max:100',
                'categories.*.min_age' => 'required|integer|min:5|max:100',
                'categories.*.max_age' => 'required|integer|min:5|max:100',
                'categories.*.gender' => 'required|in:male,female,mixed',
            ],
            6 => [
                'admin_name' => 'required|string|max:255',
                'admin_email' => 'required|email|unique:users,email',
                'admin_phone' => 'required|string|max:20',
                'admin_password' => 'required|string|min:8|confirmed',
            ],
            7 => [
                'confirm_setup' => 'required|accepted',
            ],
            default => []
        };

        return Validator::make($request->all(), $rules);
    }

    /**
     * Guardar datos del paso
     */
    private function saveStepData($step, $data)
    {
        SetupState::updateOrCreate(
            ['step' => $step],
            [
                'status' => SetupStatus::Completed,
                'data' => $data,
                'completed_at' => now(),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );
    }

    /**
     * Aplicar configuraciones del paso
     */
    private function applyStepConfigurations($step, $data)
    {
        switch($step) {
            case 1:
                $this->applyGeneralConfigurations($data);
                break;
            case 2:
                $this->applyContactConfigurations($data);
                break;
            case 3:
                $this->applyFederationConfigurations($data);
                break;
            case 4:
                $this->applyRulesConfigurations($data);
                break;
            case 5:
                $this->createDefaultCategories($data);
                break;
            case 6:
                $this->createAdminUser($data);
                break;
        }
    }

    /**
     * Aplicar configuraciones generales
     */
    private function applyGeneralConfigurations($data)
    {
        $this->configService->set('app.name', $data['app_name']);
        $this->configService->set('app.description', $data['app_description'] ?? '');
        $this->configService->set('branding.primary_color', $data['primary_color']);
        $this->configService->set('branding.secondary_color', $data['secondary_color']);
        
        // Manejar logo si se subió
        if (isset($data['logo'])) {
            // Aquí iría la lógica para subir y guardar el logo
            // $logoPath = $data['logo']->store('logos', 'public');
            // $this->configService->set('branding.logo', $logoPath);
        }
    }

    /**
     * Aplicar configuraciones de contacto
     */
    private function applyContactConfigurations($data)
    {
        $this->configService->set('contact.email', $data['contact_email']);
        $this->configService->set('contact.phone', $data['contact_phone']);
        $this->configService->set('contact.address', $data['contact_address']);
        $this->configService->set('contact.website', $data['website_url'] ?? '');
        $this->configService->set('social.facebook', $data['social_facebook'] ?? '');
        $this->configService->set('social.instagram', $data['social_instagram'] ?? '');
        $this->configService->set('social.twitter', $data['social_twitter'] ?? '');
    }

    /**
     * Aplicar configuraciones de federación
     */
    private function applyFederationConfigurations($data)
    {
        $this->configService->set('federation.name', $data['federation_name']);
        $this->configService->set('federation.code', $data['federation_code']);
        $this->configService->set('federation.annual_fee', $data['annual_fee']);
        $this->configService->set('federation.card_validity_months', $data['card_validity_months']);
    }

    /**
     * Aplicar configuraciones de reglas
     */
    private function applyRulesConfigurations($data)
    {
        $this->configService->set('volleyball.set_duration', $data['set_duration']);
        $this->configService->set('volleyball.timeout_duration', $data['timeout_duration']);
        $this->configService->set('volleyball.max_substitutions', $data['max_substitutions']);
        $this->configService->set('volleyball.points_to_win_set', $data['points_to_win_set']);
        $this->configService->set('volleyball.points_difference_to_win', $data['points_difference_to_win']);
    }

    /**
     * Crear categorías por defecto
     */
    private function createDefaultCategories($data)
    {
        foreach ($data['categories'] as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'min_age' => $categoryData['min_age'],
                'max_age' => $categoryData['max_age'],
                'gender' => $categoryData['gender'],
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);
        }
    }

    /**
     * Crear usuario administrador
     */
    private function createAdminUser($data)
    {
        $user = User::create([
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'phone' => $data['admin_phone'],
            'password' => bcrypt($data['admin_password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');
    }

    /**
     * Obtener siguiente paso
     */
    private function getNextStep($currentStep)
    {
        $steps = array_keys(SetupState::getSetupSteps());
        $currentIndex = array_search($currentStep, $steps);
        
        return $currentIndex !== false && isset($steps[$currentIndex + 1]) 
            ? $steps[$currentIndex + 1] 
            : null;
    }

    /**
     * Completar setup
     */
    private function completeSetup()
    {
        // Marcar setup como completado
        SetupState::updateOrCreate(
            ['step' => 'completed'],
            [
                'status' => SetupStatus::Completed,
                'data' => ['completed_at' => now()],
                'completed_at' => now(),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );

        // Limpiar cache de configuraciones
        $this->configService->clearCache();
    }

    /**
     * Reiniciar setup (solo para desarrollo)
     */
    public function reset()
    {
        if (app()->environment('production')) {
            abort(403, 'No permitido en producción');
        }

        SetupState::truncate();
        
        return redirect()->route('setup.wizard')
            ->with('success', 'Setup reiniciado correctamente.');
    }
}