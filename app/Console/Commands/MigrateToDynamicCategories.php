<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use App\Models\Player;
use App\Models\LeagueCategory;
use App\Services\LeagueConfigurationService;
use App\Services\CategoryAssignmentService;
use App\Services\CategoryValidationService;
use App\Services\MigrationValidationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateToDynamicCategories extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'categories:migrate-dynamic
                            {--league= : ID de liga específica para migrar}
                            {--dry-run : Ejecutar sin hacer cambios reales}
                            {--force : Forzar migración sin confirmación}
                            {--rollback : Revertir migración previa}';

    /**
     * The console command description.
     */
    protected $description = 'Migra jugadoras existentes al sistema de categorías dinámicas por liga';

    protected LeagueConfigurationService $configService;
    protected CategoryAssignmentService $assignmentService;
    protected CategoryValidationService $validationService;
    protected MigrationValidationService $migrationValidationService;

    public function __construct(
        LeagueConfigurationService $configService,
        CategoryAssignmentService $assignmentService,
        CategoryValidationService $validationService,
        MigrationValidationService $migrationValidationService
    ) {
        parent::__construct();
        $this->configService = $configService;
        $this->assignmentService = $assignmentService;
        $this->validationService = $validationService;
        $this->migrationValidationService = $migrationValidationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Iniciando migración al sistema de categorías dinámicas');
        $this->newLine();

        try {
            // Verificar si es rollback
            if ($this->option('rollback')) {
                return $this->handleRollback();
            }

            // Obtener ligas a migrar
            $leagues = $this->getLeaguesToMigrate();

            if ($leagues->isEmpty()) {
                $this->warn('No se encontraron ligas para migrar.');
                return Command::SUCCESS;
            }

            // Mostrar resumen pre-migración
            $this->showPreMigrationSummary($leagues);

            // Confirmar ejecución si no es dry-run ni force
            if (!$this->option('dry-run') && !$this->option('force')) {
                if (!$this->confirm('¿Desea continuar con la migración?')) {
                    $this->info('Migración cancelada por el usuario.');
                    return Command::SUCCESS;
                }
            }

            // Ejecutar migración
            $results = $this->executeMigration($leagues);

            // Mostrar resultados
            $this->showMigrationResults($results);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error durante la migración: ' . $e->getMessage());
            Log::error('Error en migración de categorías dinámicas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Obtiene las ligas que necesitan migración
     */
    protected function getLeaguesToMigrate()
    {
        $leagueId = $this->option('league');

        if ($leagueId) {
            $league = League::find($leagueId);
            if (!$league) {
                $this->error("Liga con ID {$leagueId} no encontrada.");
                return collect();
            }
            return collect([$league]);
        }

        // Obtener todas las ligas activas que no tienen categorías dinámicas
        return League::active()
            ->whereDoesntHave('categories')
            ->with(['clubs.players.user'])
            ->get();
    }

    /**
     * Muestra resumen antes de la migración
     */
    protected function showPreMigrationSummary($leagues): void
    {
        $this->info('📊 RESUMEN PRE-MIGRACIÓN');
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

            // Mostrar distribución actual de categorías
            $categoryStats = $this->getCurrentCategoryDistribution($league);
            if (!empty($categoryStats)) {
                $this->line("   📈 Distribución actual:");
                foreach ($categoryStats as $category => $count) {
                    $this->line("      • {$category}: {$count}");
                }
            }
            $this->newLine();
        }

        $this->line('═══════════════════════════════════════');
        $this->info("📊 TOTALES: {$leagues->count()} ligas, {$totalClubs} clubes, {$totalPlayers} jugadoras");

        if ($this->option('dry-run')) {
            $this->warn('🔍 MODO DRY-RUN: No se realizarán cambios reales');
        }

        $this->newLine();
    }

    /**
     * Obtiene distribución actual de categorías para una liga
     */
    protected function getCurrentCategoryDistribution(League $league): array
    {
        return $league->players()
            ->join('users', 'players.user_id', '=', 'users.id')
            ->where('users.status', 'active')
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Ejecuta la migración para todas las ligas
     */
    protected function executeMigration($leagues): array
    {
        $results = [
            'total_leagues' => $leagues->count(),
            'successful_leagues' => 0,
            'failed_leagues' => 0,
            'total_players_migrated' => 0,
            'total_categories_created' => 0,
            'league_details' => [],
            'errors' => []
        ];

        $progressBar = $this->output->createProgressBar($leagues->count());
        $progressBar->setFormat('verbose');
        $progressBar->start();

        foreach ($leagues as $league) {
            try {
                $leagueResult = $this->migrateLeague($league);

                $results['league_details'][$league->id] = $leagueResult;

                if ($leagueResult['success']) {
                    $results['successful_leagues']++;
                    $results['total_players_migrated'] += $leagueResult['players_migrated'];
                    $results['total_categories_created'] += $leagueResult['categories_created'];
                } else {
                    $results['failed_leagues']++;
                    $results['errors'][] = "Liga {$league->name}: " . $leagueResult['error'];
                }
            } catch (\Exception $e) {
                $results['failed_leagues']++;
                $results['errors'][] = "Liga {$league->name}: " . $e->getMessage();

                Log::error('Error migrando liga específica', [
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

    /**
     * Migra una liga específica
     */
    protected function migrateLeague(League $league): array
    {
        $result = [
            'success' => false,
            'league_name' => $league->name,
            'categories_created' => 0,
            'players_migrated' => 0,
            'players_with_issues' => 0,
            'validation_errors' => [],
            'error' => null
        ];

        try {
            DB::transaction(function () use ($league, &$result) {
                // Paso 1: Crear categorías por defecto
                $this->info("📝 Creando categorías por defecto para {$league->name}...");

                if (!$this->option('dry-run')) {
                    $categoryResult = $this->configService->createDefaultCategories($league);

                    if (!$categoryResult['success']) {
                        throw new \Exception($categoryResult['message']);
                    }

                    $result['categories_created'] = $categoryResult['categories_created'];
                } else {
                    $result['categories_created'] = 7; // Número esperado de categorías por defecto
                }

                // Paso 2: Validar configuración creada
                $this->info("✅ Validando configuración de categorías...");

                if (!$this->option('dry-run')) {
                    $validation = $this->configService->validateCategoryConfiguration($league);

                    if (!$validation['valid']) {
                        $result['validation_errors'] = $validation['errors'];
                        throw new \Exception('Configuración de categorías inválida: ' . implode(', ', $validation['errors']));
                    }
                }

                // Paso 3: Migrar jugadoras existentes
                $this->info("👥 Migrando jugadoras existentes...");

                $players = $league->players()->with('user')->get();
                $migratedCount = 0;
                $issuesCount = 0;

                foreach ($players as $player) {
                    try {
                        if ($this->migratePlayer($player)) {
                            $migratedCount++;
                        } else {
                            $issuesCount++;
                        }
                    } catch (\Exception $e) {
                        $issuesCount++;
                        Log::warning('Error migrando jugadora individual', [
                            'player_id' => $player->id,
                            'league_id' => $league->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                $result['players_migrated'] = $migratedCount;
                $result['players_with_issues'] = $issuesCount;

                // Paso 4: Verificación post-migración
                $this->info("🔍 Verificando integridad post-migración...");

                if (!$this->option('dry-run')) {
                    $verificationResult = $this->migrationValidationService->verifyMigrationIntegrity($league);

                    if ($verificationResult['overall_status'] === 'critical') {
                        throw new \Exception('Verificación post-migración falló: ' . implode(', ', array_column($verificationResult['issues_found'], 'message')));
                    }

                    if ($verificationResult['overall_status'] === 'warning') {
                        $result['validation_warnings'] = array_column($verificationResult['issues_found'], 'message');
                    }
                }

                $result['success'] = true;
            });
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();

            Log::error('Error en migración de liga', [
                'league_id' => $league->id,
                'error' => $e->getMessage()
            ]);
        }

        return $result;
    }

    /**
     * Migra una jugadora individual
     */
    protected function migratePlayer(Player $player): bool
    {
        try {
            if ($this->option('dry-run')) {
                // En modo dry-run, solo simular la asignación
                $newCategory = $this->assignmentService->assignAutomaticCategory($player);
                return $newCategory !== null;
            }

            $currentCategory = $player->category?->value ?? $player->category;
            $newCategory = $this->assignmentService->assignAutomaticCategory($player);

            if (!$newCategory) {
                Log::warning('No se pudo asignar categoría a jugadora', [
                    'player_id' => $player->id,
                    'current_category' => $currentCategory,
                    'age' => $player->user->age,
                    'gender' => $player->user->gender
                ]);
                return false;
            }

            // Solo actualizar si la categoría cambió
            if ($currentCategory !== $newCategory) {
                $player->update(['category' => $newCategory]);

                Log::info('Jugadora migrada exitosamente', [
                    'player_id' => $player->id,
                    'old_category' => $currentCategory,
                    'new_category' => $newCategory
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error migrando jugadora', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }



    /**
     * Muestra los resultados de la migración
     */
    protected function showMigrationResults(array $results): void
    {
        $this->newLine();
        $this->info('🎉 RESULTADOS DE LA MIGRACIÓN');
        $this->line('═══════════════════════════════════════');

        // Resumen general
        $this->line("📊 Ligas procesadas: {$results['total_leagues']}");
        $this->line("✅ Exitosas: {$results['successful_leagues']}");
        $this->line("❌ Fallidas: {$results['failed_leagues']}");
        $this->line("👥 Jugadoras migradas: {$results['total_players_migrated']}");
        $this->line("📝 Categorías creadas: {$results['total_categories_created']}");

        $this->newLine();

        // Detalles por liga
        if (!empty($results['league_details'])) {
            $this->info('📋 DETALLES POR LIGA:');
            foreach ($results['league_details'] as $leagueId => $detail) {
                $status = $detail['success'] ? '✅' : '❌';
                $this->line("{$status} {$detail['league_name']}");

                if ($detail['success']) {
                    $this->line("   📝 Categorías: {$detail['categories_created']}");
                    $this->line("   👥 Jugadoras: {$detail['players_migrated']}");
                    if ($detail['players_with_issues'] > 0) {
                        $this->line("   ⚠️  Con problemas: {$detail['players_with_issues']}");
                    }
                } else {
                    $this->line("   ❌ Error: {$detail['error']}");
                }
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

        if ($results['successful_leagues'] === $results['total_leagues']) {
            $this->info('🎉 ¡Migración completada exitosamente!');
        } elseif ($results['successful_leagues'] > 0) {
            $this->warn('⚠️  Migración completada con algunos errores.');
        } else {
            $this->error('❌ La migración falló completamente.');
        }

        if (!$this->option('dry-run')) {
            $this->newLine();
            $this->info('💡 Recomendaciones post-migración:');
            $this->line('   • Verifique las categorías asignadas en el panel de administración');
            $this->line('   • Revise jugadoras con problemas de categorización');
            $this->line('   • Considere ajustar configuraciones de categorías según necesidades específicas');
            $this->line('   • Ejecute validaciones periódicas con: php artisan categories:validate');
        }
    }

    /**
     * Maneja el rollback de la migración
     */
    protected function handleRollback(): int
    {
        $this->warn('🔄 INICIANDO ROLLBACK DE MIGRACIÓN');
        $this->newLine();

        if (!$this->option('force')) {
            $this->error('⚠️  ADVERTENCIA: El rollback eliminará todas las configuraciones de categorías dinámicas');
            $this->error('   y restaurará las categorías basadas en el enum original.');
            $this->newLine();

            if (!$this->confirm('¿Está seguro de que desea continuar con el rollback?')) {
                $this->info('Rollback cancelado.');
                return Command::SUCCESS;
            }
        }

        try {
            $leagues = League::whereHas('categories')->with('categories', 'players')->get();

            if ($leagues->isEmpty()) {
                $this->info('No se encontraron ligas con categorías dinámicas para revertir.');
                return Command::SUCCESS;
            }

            $this->info("🔄 Revirtiendo {$leagues->count()} ligas...");

            $progressBar = $this->output->createProgressBar($leagues->count());
            $progressBar->start();

            $totalPlayersReverted = 0;
            $totalCategoriesDeleted = 0;

            foreach ($leagues as $league) {
                DB::transaction(function () use ($league, &$totalPlayersReverted, &$totalCategoriesDeleted) {
                    // Revertir jugadoras a categorías basadas en enum
                    $players = $league->players()->with('user')->get();

                    foreach ($players as $player) {
                        $age = $player->user->age ?? 19;
                        $traditionalCategory = $this->getTraditionalCategoryForAge($age);

                        if ($traditionalCategory && !$this->option('dry-run')) {
                            $player->update(['category' => $traditionalCategory]);
                            $totalPlayersReverted++;
                        }
                    }

                    // Eliminar configuraciones de categorías dinámicas
                    $categoriesCount = $league->categories()->count();

                    if (!$this->option('dry-run')) {
                        $league->categories()->delete();
                    }

                    $totalCategoriesDeleted += $categoriesCount;
                });

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            $this->info('✅ Rollback completado exitosamente:');
            $this->line("   👥 Jugadoras revertidas: {$totalPlayersReverted}");
            $this->line("   🗑️  Configuraciones eliminadas: {$totalCategoriesDeleted}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error durante el rollback: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Obtiene categoría tradicional basada en edad
     */
    protected function getTraditionalCategoryForAge(int $age): ?string
    {
        return match (true) {
            $age >= 8 && $age <= 10 => 'Mini',
            $age >= 11 && $age <= 12 => 'Pre_Mini',
            $age >= 13 && $age <= 14 => 'Infantil',
            $age >= 15 && $age <= 16 => 'Cadete',
            $age >= 17 && $age <= 18 => 'Juvenil',
            $age >= 35 => 'Masters',
            $age >= 19 && $age <= 34 => 'Mayores',
            default => 'Mayores'
        };
    }
}
