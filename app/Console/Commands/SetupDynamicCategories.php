<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use App\Services\LeagueConfigurationService;
use App\Services\CategoryValidationService;
use Illuminate\Support\Facades\Log;

class SetupDynamicCategories extends Command
{
    protected $signature = 'categories:setup
                            {--league= : ID de liga específica para configurar}
                            {--all : Configurar todas las ligas sin categorías dinámicas}
                            {--force : Forzar configuración sin confirmación}
                            {--validate : Validar configuración después de crearla}';

    protected $description = 'Configura categorías dinámicas por defecto para ligas';

    protected LeagueConfigurationService $configService;
    protected CategoryValidationService $validationService;

    public function __construct(
        LeagueConfigurationService $configService,
        CategoryValidationService $validationService
    ) {
        parent::__construct();
        $this->configService = $configService;
        $this->validationService = $validationService;
    }

    public function handle(): int
    {
        $this->info('⚙️  Configurando categorías dinámicas por liga');
        $this->newLine();

        try {
            $leagues = $this->getLeaguesToSetup();

            if ($leagues->isEmpty()) {
                $this->info('No se encontraron ligas que necesiten configuración.');
                return Command::SUCCESS;
            }

            $this->showSetupSummary($leagues);

            if (!$this->option('force')) {
                if (!$this->confirm('¿Desea continuar con la configuración?')) {
                    $this->info('Configuración cancelada por el usuario.');
                    return Command::SUCCESS;
                }
            }

            $results = $this->executeSetup($leagues);
            $this->showSetupResults($results);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error durante la configuración: ' . $e->getMessage());
            Log::error('Error en configuración de categorías dinámicas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    protected function getLeaguesToSetup()
    {
        $leagueId = $this->option('league');
        $all = $this->option('all');

        if ($leagueId) {
            $league = League::find($leagueId);
            if (!$league) {
                $this->error("Liga con ID {$leagueId} no encontrada.");
                return collect();
            }
            return collect([$league]);
        }

        if ($all) {
            return League::active()
                ->whereDoesntHave('categories')
                ->with(['clubs.players'])
                ->get();
        }

        // Modo interactivo: mostrar ligas disponibles
        return $this->selectLeaguesInteractively();
    }

    protected function selectLeaguesInteractively()
    {
        $availableLeagues = League::active()
            ->whereDoesntHave('categories')
            ->get();

        if ($availableLeagues->isEmpty()) {
            $this->info('Todas las ligas activas ya tienen categorías dinámicas configuradas.');
            return collect();
        }

        $this->info('📋 Ligas disponibles para configurar:');
        $this->newLine();

        $choices = [];
        foreach ($availableLeagues as $index => $league) {
            $playersCount = $league->players()->count();
            $clubsCount = $league->clubs()->count();

            $this->line(($index + 1) . ". {$league->name}");
            $this->line("   📍 {$league->full_location}");
            $this->line("   🏢 {$clubsCount} clubes, 👥 {$playersCount} jugadoras");
            $this->newLine();

            $choices[$index + 1] = $league;
        }

        $this->line('0. Todas las ligas');
        $this->newLine();

        $selection = $this->ask('Seleccione las ligas a configurar (números separados por comas, o 0 para todas)');

        if ($selection === '0') {
            return $availableLeagues;
        }

        $selectedNumbers = array_map('trim', explode(',', $selection));
        $selectedLeagues = collect();

        foreach ($selectedNumbers as $number) {
            if (isset($choices[(int)$number])) {
                $selectedLeagues->push($choices[(int)$number]);
            }
        }

        return $selectedLeagues;
    }

    protected function showSetupSummary($leagues): void
    {
        $this->info('📊 RESUMEN DE CONFIGURACIÓN');
        $this->line('═══════════════════════════════════════');

        $totalPlayers = 0;
        $totalClubs = 0;

        foreach ($leagues as $league) {
            $playersCount = $league->players()->count();
            $clubsCount = $league->clubs()->count();

            $totalPlayers += $playersCount;
            $totalClubs += $clubsCount;

            $this->line("🏆 {$league->name}");
            $this->line("   📍 {$league->full_location}");
            $this->line("   🏢 Clubes: {$clubsCount}");
            $this->line("   👥 Jugadoras: {$playersCount}");

            if ($league->hasCustomCategories()) {
                $this->line("   ⚠️  Ya tiene categorías configuradas");
            } else {
                $this->line("   ✅ Listo para configurar");
            }
            $this->newLine();
        }

        $this->line('═══════════════════════════════════════');
        $this->info("📊 TOTALES: {$leagues->count()} ligas, {$totalClubs} clubes, {$totalPlayers} jugadoras");
        $this->newLine();
    }

    protected function executeSetup($leagues): array
    {
        $results = [
            'total_leagues' => $leagues->count(),
            'successful_setups' => 0,
            'failed_setups' => 0,
            'skipped_setups' => 0,
            'total_categories_created' => 0,
            'league_details' => [],
            'errors' => []
        ];

        $progressBar = $this->output->createProgressBar($leagues->count());
        $progressBar->setFormat('verbose');
        $progressBar->start();

        foreach ($leagues as $league) {
            try {
                $leagueResult = $this->setupLeague($league);
                $results['league_details'][$league->id] = $leagueResult;

                if ($leagueResult['success']) {
                    if ($leagueResult['skipped']) {
                        $results['skipped_setups']++;
                    } else {
                        $results['successful_setups']++;
                        $results['total_categories_created'] += $leagueResult['categories_created'];
                    }
                } else {
                    $results['failed_setups']++;
                    $results['errors'][] = "Liga {$league->name}: " . $leagueResult['error'];
                }
            } catch (\Exception $e) {
                $results['failed_setups']++;
                $results['errors'][] = "Liga {$league->name}: " . $e->getMessage();

                Log::error('Error configurando liga específica', [
                    'league_id' => $league->id,
                    'league_name' => $league->name,
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        return $results;
    }

    protected function setupLeague(League $league): array
    {
        $result = [
            'success' => false,
            'skipped' => false,
            'league_name' => $league->name,
            'categories_created' => 0,
            'validation_result' => null,
            'error' => null
        ];

        try {
            // Verificar si ya tiene categorías
            if ($league->hasCustomCategories()) {
                $result['success'] = true;
                $result['skipped'] = true;
                $result['error'] = 'Ya tiene categorías configuradas';
                return $result;
            }

            $this->info("⚙️  Configurando {$league->name}...");

            // Crear categorías por defecto
            $setupResult = $this->configService->createDefaultCategories($league);

            if (!$setupResult['success']) {
                $result['error'] = $setupResult['message'];
                return $result;
            }

            $result['categories_created'] = $setupResult['categories_created'];

            // Validar configuración si se solicita
            if ($this->option('validate')) {
                $this->info("✅ Validando configuración...");
                $validationResult = $this->configService->validateCategoryConfiguration($league);
                $result['validation_result'] = $validationResult;

                if (!$validationResult['valid']) {
                    $this->warn("⚠️  Configuración creada con advertencias para {$league->name}");
                    foreach ($validationResult['warnings'] as $warning) {
                        $this->line("   • {$warning}");
                    }
                }
            }

            $result['success'] = true;

            Log::info('Liga configurada exitosamente', [
                'league_id' => $league->id,
                'league_name' => $league->name,
                'categories_created' => $result['categories_created']
            ]);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();

            Log::error('Error configurando liga', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);
        }

        return $result;
    }

    protected function showSetupResults(array $results): void
    {
        $this->newLine();
        $this->info('🎉 RESULTADOS DE CONFIGURACIÓN');
        $this->line('═══════════════════════════════════════');

        // Resumen general
        $this->line("📊 Ligas procesadas: {$results['total_leagues']}");
        $this->line("✅ Configuradas exitosamente: {$results['successful_setups']}");
        $this->line("⏭️  Omitidas (ya configuradas): {$results['skipped_setups']}");
        $this->line("❌ Fallidas: {$results['failed_setups']}");
        $this->line("📝 Categorías creadas: {$results['total_categories_created']}");

        $this->newLine();

        // Detalles por liga
        if (!empty($results['league_details'])) {
            $this->info('📋 DETALLES POR LIGA:');
            foreach ($results['league_details'] as $leagueId => $detail) {
                $this->showLeagueSetupDetail($detail);
            }
        }

        // Errores generales
        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('❌ ERRORES ENCONTRADOS:');
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error}");
            }
        }

        $this->newLine();

        if ($results['successful_setups'] === $results['total_leagues']) {
            $this->info('🎉 ¡Configuración completada exitosamente!');
        } elseif ($results['successful_setups'] > 0) {
            $this->warn('⚠️  Configuración completada con algunos problemas.');
        } else {
            $this->error('❌ La configuración falló completamente.');
        }

        // Recomendaciones
        if ($results['successful_setups'] > 0) {
            $this->newLine();
            $this->info('💡 Próximos pasos recomendados:');
            $this->line('   • Ejecute la migración de jugadoras: php artisan categories:migrate-dynamic');
            $this->line('   • Valide el sistema: php artisan categories:validate');
            $this->line('   • Revise las configuraciones en el panel de administración');
            $this->line('   • Ajuste categorías según necesidades específicas de cada liga');
        }
    }

    protected function showLeagueSetupDetail(array $detail): void
    {
        if ($detail['skipped']) {
            $this->line("⏭️  {$detail['league_name']} - Ya configurada");
            return;
        }

        $status = $detail['success'] ? '✅' : '❌';
        $this->line("{$status} {$detail['league_name']}");

        if ($detail['success']) {
            $this->line("   📝 Categorías creadas: {$detail['categories_created']}");

            if ($detail['validation_result']) {
                $validation = $detail['validation_result'];
                if ($validation['valid']) {
                    $this->line("   ✅ Validación: Configuración válida");
                } else {
                    $this->line("   ⚠️  Validación: {" . count($validation['warnings']) . "} advertencias");
                }
            }
        } else {
            $this->line("   ❌ Error: {$detail['error']}");
        }

        $this->newLine();
    }
}
