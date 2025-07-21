<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use App\Models\Player;
use App\Services\CategoryValidationService;
use App\Services\CategoryAssignmentService;
use Illuminate\Support\Facades\Log;

class ValidateCategoriesSystem extends Command
{
    protected $signature = 'categories:validate
                            {--league= : ID de liga específica para validar}
                            {--fix : Intentar corregir automáticamente los problemas encontrados}
                            {--report : Generar reporte detallado}';

    protected $description = 'Valida la integridad del sistema de categorías dinámicas';

    protected CategoryValidationService $validationService;
    protected CategoryAssignmentService $assignmentService;

    public function __construct(
        CategoryValidationService $validationService,
        CategoryAssignmentService $assignmentService
    ) {
        parent::__construct();
        $this->validationService = $validationService;
        $this->assignmentService = $assignmentService;
    }

    public function handle(): int
    {
        $this->info('🔍 Iniciando validación del sistema de categorías');
        $this->newLine();

        try {
            $leagues = $this->getLeaguesToValidate();

            if ($leagues->isEmpty()) {
                $this->warn('No se encontraron ligas para validar.');
                return Command::SUCCESS;
            }

            $this->info("📊 Validando {$leagues->count()} liga(s)...");
            $this->newLine();

            $results = $this->executeValidation($leagues);
            $this->showValidationResults($results);

            if ($this->option('report')) {
                $this->generateDetailedReport($results);
            }

            $hasErrors = collect($results['league_details'])->some(function ($detail) {
                return !empty($detail['errors']) || !empty($detail['critical_issues']);
            });

            return $hasErrors ? Command::FAILURE : Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error durante la validación: ' . $e->getMessage());
            Log::error('Error en validación de categorías', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    protected function getLeaguesToValidate()
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

        return League::active()
            ->with(['categories', 'players.user'])
            ->get();
    }

    protected function executeValidation($leagues): array
    {
        $results = [
            'total_leagues' => $leagues->count(),
            'leagues_with_dynamic_categories' => 0,
            'leagues_with_errors' => 0,
            'leagues_with_warnings' => 0,
            'total_players_validated' => 0,
            'total_issues_found' => 0,
            'total_issues_fixed' => 0,
            'league_details' => []
        ];

        $progressBar = $this->output->createProgressBar($leagues->count());
        $progressBar->setFormat('verbose');
        $progressBar->start();

        foreach ($leagues as $league) {
            try {
                $leagueResult = $this->validateLeague($league);
                $results['league_details'][$league->id] = $leagueResult;

                if ($leagueResult['has_dynamic_categories']) {
                    $results['leagues_with_dynamic_categories']++;
                }

                if (!empty($leagueResult['errors']) || !empty($leagueResult['critical_issues'])) {
                    $results['leagues_with_errors']++;
                }

                if (!empty($leagueResult['warnings'])) {
                    $results['leagues_with_warnings']++;
                }

                $results['total_players_validated'] += $leagueResult['players_validated'];
                $results['total_issues_found'] += $leagueResult['issues_found'];
                $results['total_issues_fixed'] += $leagueResult['issues_fixed'];
            } catch (\Exception $e) {
                $results['league_details'][$league->id] = [
                    'league_name' => $league->name,
                    'validation_error' => $e->getMessage(),
                    'has_dynamic_categories' => false,
                    'errors' => [],
                    'warnings' => [],
                    'critical_issues' => [],
                    'players_validated' => 0,
                    'issues_found' => 0,
                    'issues_fixed' => 0
                ];

                Log::error('Error validando liga específica', [
                    'league_id' => $league->id,
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        return $results;
    }

    protected function validateLeague(League $league): array
    {
        $result = [
            'league_name' => $league->name,
            'has_dynamic_categories' => $league->hasCustomCategories(),
            'categories_count' => $league->categories()->count(),
            'players_validated' => 0,
            'issues_found' => 0,
            'issues_fixed' => 0,
            'errors' => [],
            'warnings' => [],
            'critical_issues' => [],
            'player_issues' => [],
            'category_stats' => []
        ];

        if ($result['has_dynamic_categories']) {
            $configValidation = $this->validationService->generateValidationReport($league);

            $result['errors'] = $configValidation['validations']['age_ranges']['errors'] ?? [];
            $result['warnings'] = array_merge(
                $configValidation['validations']['age_ranges']['warnings'] ?? [],
                $configValidation['validations']['gender_overlaps']['warnings'] ?? [],
                $configValidation['validations']['special_rules']['warnings'] ?? []
            );

            if ($configValidation['overall_status'] === 'invalid') {
                $result['critical_issues'] = $configValidation['summary']['critical_issues'];
            }
        }

        $playerValidation = $this->validateLeaguePlayers($league);
        $result['players_validated'] = $playerValidation['total_players'];
        $result['player_issues'] = $playerValidation['issues'];
        $result['issues_found'] += count($playerValidation['issues']);

        if ($this->option('fix') && !empty($playerValidation['issues'])) {
            $fixResults = $this->fixPlayerIssues($league, $playerValidation['issues']);
            $result['issues_fixed'] = $fixResults['fixed_count'];
        }

        $result['category_stats'] = $this->generateCategoryStats($league);

        return $result;
    }

    protected function validateLeaguePlayers(League $league): array
    {
        $players = $league->players()->with('user')->get();
        $issues = [];

        foreach ($players as $player) {
            $playerIssues = $this->validatePlayer($player);
            if (!empty($playerIssues)) {
                $issues[] = [
                    'player_id' => $player->id,
                    'player_name' => $player->user->full_name,
                    'current_category' => $player->category?->value ?? $player->category,
                    'age' => $player->user->age,
                    'gender' => $player->user->gender,
                    'issues' => $playerIssues
                ];
            }
        }

        return [
            'total_players' => $players->count(),
            'issues' => $issues
        ];
    }

    protected function validatePlayer(Player $player): array
    {
        $issues = [];
        $age = $player->user->age ?? 0;
        $gender = $player->user->gender ?? 'unknown';
        $currentCategory = $player->category?->value ?? $player->category;

        if (empty($currentCategory)) {
            $issues[] = 'Sin categoría asignada';
        }

        if ($age < 8 || $age > 100) {
            $issues[] = "Edad inválida para competir ({$age} años)";
        }

        if (!in_array($gender, ['male', 'female'])) {
            $issues[] = "Género inválido o no especificado ({$gender})";
        }

        if (!empty($currentCategory) && $age >= 8 && $age <= 100) {
            $suggestedCategory = $this->assignmentService->assignAutomaticCategory($player);

            if ($suggestedCategory && $suggestedCategory !== $currentCategory) {
                $issues[] = "Categoría incorrecta (actual: {$currentCategory}, sugerida: {$suggestedCategory})";
            }
        }

        $league = $player->currentClub->league;
        if ($league->hasCustomCategories()) {
            $dynamicCategory = $league->findCategoryForPlayer($age, $gender);
            if ($dynamicCategory && $dynamicCategory->name !== $currentCategory) {
                $issues[] = "No usa categoría dinámica configurada (debería ser: {$dynamicCategory->name})";
            }
        }

        return $issues;
    }

    protected function fixPlayerIssues(League $league, array $playerIssues): array
    {
        $fixedCount = 0;
        $failedCount = 0;

        $this->info("🔧 Intentando corregir {" . count($playerIssues) . "} problemas de jugadoras...");

        foreach ($playerIssues as $issue) {
            try {
                $player = Player::find($issue['player_id']);
                if (!$player) {
                    $failedCount++;
                    continue;
                }

                $newCategory = $this->assignmentService->assignAutomaticCategory($player);

                if ($newCategory && $newCategory !== $issue['current_category']) {
                    $player->update(['category' => $newCategory]);
                    $fixedCount++;

                    Log::info('Problema de categoría corregido automáticamente', [
                        'player_id' => $player->id,
                        'old_category' => $issue['current_category'],
                        'new_category' => $newCategory
                    ]);
                } else {
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
                Log::warning('Error corrigiendo problema de jugadora', [
                    'player_id' => $issue['player_id'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'fixed_count' => $fixedCount,
            'failed_count' => $failedCount
        ];
    }

    protected function generateCategoryStats(League $league): array
    {
        $stats = [];

        if ($league->hasCustomCategories()) {
            foreach ($league->getActiveCategories() as $category) {
                $categoryStats = $category->getPlayerStats();
                $stats[$category->name] = $categoryStats;
            }
        } else {
            $stats = $league->getPlayersStatsByCategory();
        }

        return $stats;
    }

    protected function showValidationResults(array $results): void
    {
        $this->info('📊 RESULTADOS DE VALIDACIÓN');
        $this->line('═══════════════════════════════════════');

        $this->line("🏆 Ligas validadas: {$results['total_leagues']}");
        $this->line("⚙️  Con categorías dinámicas: {$results['leagues_with_dynamic_categories']}");
        $this->line("❌ Con errores: {$results['leagues_with_errors']}");
        $this->line("⚠️  Con advertencias: {$results['leagues_with_warnings']}");
        $this->line("👥 Jugadoras validadas: {$results['total_players_validated']}");
        $this->line("🔍 Problemas encontrados: {$results['total_issues_found']}");

        if ($this->option('fix')) {
            $this->line("🔧 Problemas corregidos: {$results['total_issues_fixed']}");
        }

        $this->newLine();

        $this->info('📋 DETALLES POR LIGA:');
        foreach ($results['league_details'] as $leagueId => $detail) {
            $this->showLeagueValidationDetail($detail);
        }

        $this->newLine();
        if ($results['leagues_with_errors'] === 0) {
            $this->info('✅ ¡Todas las ligas pasaron la validación!');
        } else {
            $this->warn("⚠️  Se encontraron problemas en {$results['leagues_with_errors']} liga(s).");
            $this->line('   Revise los detalles arriba y considere usar --fix para corregir automáticamente.');
        }
    }

    protected function showLeagueValidationDetail(array $detail): void
    {
        $hasErrors = !empty($detail['errors']) || !empty($detail['critical_issues']);
        $hasWarnings = !empty($detail['warnings']);
        $hasPlayerIssues = !empty($detail['player_issues']);

        $status = $hasErrors ? '❌' : ($hasWarnings || $hasPlayerIssues ? '⚠️' : '✅');

        $this->line("{$status} {$detail['league_name']}");

        if (isset($detail['validation_error'])) {
            $this->line("   💥 Error de validación: {$detail['validation_error']}");
            return;
        }

        $this->line("   📊 Categorías: {$detail['categories_count']} | Jugadoras: {$detail['players_validated']}");

        if (!empty($detail['critical_issues'])) {
            $this->line("   🚨 PROBLEMAS CRÍTICOS:");
            foreach ($detail['critical_issues'] as $issue) {
                $this->line("      • {$issue}");
            }
        }

        if (!empty($detail['errors'])) {
            $this->line("   ❌ ERRORES:");
            foreach ($detail['errors'] as $error) {
                $this->line("      • {$error}");
            }
        }

        if (!empty($detail['warnings'])) {
            $warningsToShow = array_slice($detail['warnings'], 0, 3);
            $this->line("   ⚠️  ADVERTENCIAS:");
            foreach ($warningsToShow as $warning) {
                $this->line("      • {$warning}");
            }
            if (count($detail['warnings']) > 3) {
                $remaining = count($detail['warnings']) - 3;
                $this->line("      ... y {$remaining} más");
            }
        }

        if (!empty($detail['player_issues'])) {
            $issuesToShow = array_slice($detail['player_issues'], 0, 3);
            $this->line("   👥 PROBLEMAS DE JUGADORAS:");
            foreach ($issuesToShow as $issue) {
                $issueText = implode(', ', $issue['issues']);
                $this->line("      • {$issue['player_name']}: {$issueText}");
            }
            if (count($detail['player_issues']) > 3) {
                $remaining = count($detail['player_issues']) - 3;
                $this->line("      ... y {$remaining} más");
            }
        }

        if ($this->option('fix') && $detail['issues_fixed'] > 0) {
            $this->line("   🔧 Problemas corregidos: {$detail['issues_fixed']}");
        }

        $this->newLine();
    }

    protected function generateDetailedReport(array $results): void
    {
        $this->info('📄 Generando reporte detallado...');

        $reportPath = storage_path('logs/categories-validation-' . now()->format('Y-m-d-H-i-s') . '.json');

        $reportData = [
            'validation_date' => now()->toISOString(),
            'command_options' => [
                'league' => $this->option('league'),
                'fix' => $this->option('fix'),
                'report' => $this->option('report')
            ],
            'summary' => [
                'total_leagues' => $results['total_leagues'],
                'leagues_with_dynamic_categories' => $results['leagues_with_dynamic_categories'],
                'leagues_with_errors' => $results['leagues_with_errors'],
                'leagues_with_warnings' => $results['leagues_with_warnings'],
                'total_players_validated' => $results['total_players_validated'],
                'total_issues_found' => $results['total_issues_found'],
                'total_issues_fixed' => $results['total_issues_fixed']
            ],
            'league_details' => $results['league_details']
        ];

        file_put_contents($reportPath, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("📄 Reporte guardado en: {$reportPath}");
    }
}