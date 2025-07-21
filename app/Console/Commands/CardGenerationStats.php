<?php

namespace App\Console\Commands;

use App\Models\League;
use App\Models\CardGenerationLog;
use App\Services\AutomaticCardGenerationService;
use App\Services\CardNumberingService;
use Illuminate\Console\Command;

class CardGenerationStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cards:stats
                            {--league= : Show stats for specific league ID}
                            {--days=30 : Number of days to analyze}
                            {--format=table : Output format (table, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show card generation statistics';

    public function __construct(
        private AutomaticCardGenerationService $cardGenerationService,
        private CardNumberingService $numberingService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $leagueId = $this->option('league');
        $format = $this->option('format');

        $this->info("📊 Estadísticas de Generación de Carnets (últimos {$days} días)");
        $this->newLine();

        try {
            $league = null;
            if ($leagueId) {
                $league = League::find($leagueId);
                if (!$league) {
                    $this->error("❌ Liga con ID {$leagueId} no encontrada");
                    return self::FAILURE;
                }
                $this->info("🏆 Liga: {$league->name}");
                $this->newLine();
            }

            // Obtener estadísticas generales
            $stats = $this->cardGenerationService->getGenerationStats($league, $days);

            if ($format === 'json') {
                $this->line(json_encode($stats, JSON_PRETTY_PRINT));
                return self::SUCCESS;
            }

            // Mostrar estadísticas en formato tabla
            $this->displayGeneralStats($stats);

            // Mostrar estadísticas por liga si no se especificó una liga
            if (!$league) {
                $this->displayLeagueStats($days);
            } else {
                $this->displayNumberingStats($league);
            }

            // Mostrar errores más comunes
            $this->displayErrorStats($days);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error obteniendo estadísticas: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    /**
     * Display general statistics
     */
    private function displayGeneralStats(array $stats): void
    {
        $this->info('📈 Estadísticas Generales');

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total de Generaciones', number_format($stats['total_generations'])],
                ['Generaciones Exitosas', number_format($stats['successful_generations'])],
                ['Generaciones Fallidas', number_format($stats['failed_generations'])],
                ['Tasa de Éxito', $stats['success_rate'] . '%'],
                ['Tiempo Promedio (ms)', number_format($stats['avg_processing_time'] ?? 0, 2)],
                ['Total de Reintentos', number_format($stats['total_retries'])],
                ['Jugadoras Únicas', number_format($stats['unique_players'])],
                ['Ligas Únicas', number_format($stats['unique_leagues'])],
            ]
        );

        $this->newLine();
    }

    /**
     * Display statistics by league
     */
    private function displayLeagueStats(int $days): void
    {
        $this->info('🏆 Estadísticas por Liga');

        $leagueStats = CardGenerationLog::where('created_at', '>=', now()->subDays($days))
            ->join('leagues', 'card_generation_logs.league_id', '=', 'leagues.id')
            ->selectRaw('
                leagues.name as league_name,
                COUNT(*) as total_generations,
                COUNT(CASE WHEN status = "completed" THEN 1 END) as successful,
                COUNT(CASE WHEN status = "failed" THEN 1 END) as failed,
                AVG(processing_time_ms) as avg_time
            ')
            ->groupBy('leagues.id', 'leagues.name')
            ->orderBy('total_generations', 'desc')
            ->get();

        if ($leagueStats->isEmpty()) {
            $this->warn('⚠️  No hay datos de generación por liga en el período especificado');
            return;
        }

        $tableData = $leagueStats->map(function ($stat) {
            $successRate = $stat->total_generations > 0
                ? round(($stat->successful / $stat->total_generations) * 100, 1)
                : 0;

            return [
                $stat->league_name,
                $stat->total_generations,
                $stat->successful,
                $stat->failed,
                $successRate . '%',
                number_format($stat->avg_time ?? 0, 0) . 'ms'
            ];
        })->toArray();

        $this->table(
            ['Liga', 'Total', 'Exitosas', 'Fallidas', 'Tasa Éxito', 'Tiempo Prom.'],
            $tableData
        );

        $this->newLine();
    }

    /**
     * Display numbering statistics for a specific league
     */
    private function displayNumberingStats(League $league): void
    {
        $this->info('🔢 Estadísticas de Numeración');

        $numberingStats = $this->numberingService->getNumberingStats($league);

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Código de Liga', $numberingStats['league_code']],
                ['Año Actual', $numberingStats['year']],
                ['Total de Carnets', number_format($numberingStats['total_cards'])],
                ['Próximo Secuencial', str_pad($numberingStats['next_sequential'], 6, '0', STR_PAD_LEFT)],
                ['Último Carnet', $numberingStats['last_card_number'] ?? 'N/A'],
                ['Capacidad Restante', number_format($numberingStats['capacity_remaining'])],
                ['Capacidad Usada', $numberingStats['capacity_percentage'] . '%'],
            ]
        );

        $this->newLine();
    }

    /**
     * Display error statistics
     */
    private function displayErrorStats(int $days): void
    {
        $this->info('❌ Errores Más Comunes');

        $errorStats = CardGenerationLog::getErrorStats($days);

        if (empty($errorStats)) {
            $this->info('✅ No hay errores registrados en el período especificado');
            return;
        }

        $tableData = collect($errorStats)->map(function ($count, $error) {
            return [
                \Illuminate\Support\Str::limit($error, 60),
                $count
            ];
        })->values()->toArray();

        $this->table(
            ['Error', 'Ocurrencias'],
            $tableData
        );
    }
}
