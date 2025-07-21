<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use App\Services\MigrationValidationService;
use Illuminate\Support\Facades\Log;

class GenerateMigrationReport extends Command
{
    protected $signature = 'categories:migration-report
                            {--league= : ID de liga específica para reportar}
                            {--format=json : Formato del reporte (json|csv|html)}
                            {--output= : Ruta de salida personalizada}';

    protected $description = 'Genera reportes detallados del estado de migración a categorías dinámicas';

    protected MigrationValidationService $migrationValidationService;

    public function __construct(MigrationValidationService $migrationValidationService)
    {
        parent::__construct();
        $this->migrationValidationService = $migrationValidationService;
    }

    public function handle(): int
    {
        $this->info('📊 Generando reporte de migración a categorías dinámicas');
        $this->newLine();

        try {
            $leagues = $this->getLeaguesForReport();

            if ($leagues->isEmpty()) {
                $this->warn('No se encontraron ligas para incluir en el reporte.');
                return Command::SUCCESS;
            }

            $this->info("📋 Generando reporte para {$leagues->count()} liga(s)...");

            $reportData = $this->generateReportData($leagues);
            $format = $this->option('format');
            $outputPath = $this->generateReport($reportData, $format);

            $this->info("✅ Reporte generado exitosamente: {$outputPath}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error generando reporte de migración: ' . $e->getMessage());
            Log::error('Error en generación de reporte de migración', [
                'error' => $e->getMessage()
            ]);
            return Command::FAILURE;
        }
    }

    protected function getLeaguesForReport()
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
            ->with(['categories', 'players.user', 'clubs'])
            ->get();
    }

    protected function generateReportData($leagues): array
    {
        $reportData = [
            'report_metadata' => [
                'generated_at' => now()->toISOString(),
                'total_leagues' => $leagues->count()
            ],
            'migration_summary' => [
                'total_leagues' => $leagues->count(),
                'leagues_with_dynamic_categories' => 0,
                'leagues_without_dynamic_categories' => 0,
                'total_players' => 0,
                'migration_completion_percentage' => 0
            ],
            'league_details' => []
        ];

        foreach ($leagues as $league) {
            $leagueData = $this->generateLeagueReportData($league);
            $reportData['league_details'][] = $leagueData;

            if ($leagueData['has_dynamic_categories']) {
                $reportData['migration_summary']['leagues_with_dynamic_categories']++;
            } else {
                $reportData['migration_summary']['leagues_without_dynamic_categories']++;
            }

            $reportData['migration_summary']['total_players'] += $leagueData['total_players'];
        }

        if ($reportData['migration_summary']['total_leagues'] > 0) {
            $reportData['migration_summary']['migration_completion_percentage'] = round(
                ($reportData['migration_summary']['leagues_with_dynamic_categories'] / $reportData['migration_summary']['total_leagues']) * 100,
                2
            );
        }

        return $reportData;
    }

    protected function generateLeagueReportData(League $league): array
    {
        $hasDynamicCategories = $league->hasCustomCategories();

        return [
            'league_id' => $league->id,
            'league_name' => $league->name,
            'league_location' => $league->full_location,
            'has_dynamic_categories' => $hasDynamicCategories,
            'migration_status' => $hasDynamicCategories ? 'migrated' : 'pending',
            'total_players' => $league->players()->count(),
            'total_clubs' => $league->clubs()->count(),
            'categories_count' => $hasDynamicCategories ? $league->getActiveCategories()->count() : 0,
            'integrity_status' => $hasDynamicCategories ?
                $this->migrationValidationService->verifyMigrationIntegrity($league)['overall_status'] :
                'N/A'
        ];
    }

    protected function generateReport(array $reportData, string $format): string
    {
        $timestamp = now()->format('Y-m-d-H-i-s');
        $outputPath = $this->option('output') ?: storage_path("reports/migration-report-{$timestamp}.{$format}");

        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        switch ($format) {
            case 'json':
                file_put_contents($outputPath, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                break;
            case 'csv':
                $this->generateCsvReport($reportData, $outputPath);
                break;
            case 'html':
                $this->generateHtmlReport($reportData, $outputPath);
                break;
            default:
                throw new \InvalidArgumentException("Formato no soportado: {$format}");
        }

        return $outputPath;
    }

    protected function generateCsvReport(array $reportData, string $outputPath): void
    {
        $handle = fopen($outputPath, 'w');

        $headers = [
            'Liga ID', 'Nombre Liga', 'Ubicación', 'Estado Migración',
            'Total Jugadoras', 'Total Clubes', 'Categorías Configuradas', 'Estado Integridad'
        ];
        fputcsv($handle, $headers);

        foreach ($reportData['league_details'] as $league) {
            $row = [
                $league['league_id'],
                $league['league_name'],
                $league['league_location'],
                $league['migration_status'],
                $league['total_players'],
                $league['total_clubs'],
                $league['categories_count'],
                $league['integrity_status']
            ];
            fputcsv($handle, $row);
        }

        fclose($handle);
    }

    protected function generateHtmlReport(array $reportData, string $outputPath): void
    {
        $metadata = $reportData['report_metadata'];
        $summary = $reportData['migration_summary'];
        $leagues = $reportData['league_details'];

        $html = "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Reporte de Migración - Categorías Dinámicas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .metric { background: #e9ecef; padding: 15px; border-radius: 5px; text-align: center; }
        .metric h3 { margin: 0 0 10px 0; color: #495057; }
        .metric .value { font-size: 24px; font-weight: bold; color: #007bff; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .status-migrated { color: #28a745; font-weight: bold; }
        .status-pending { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <div class='header'>
        <h1>📊 Reporte de Migración - Sistema de Categorías Dinámicas</h1>
        <p><strong>Generado:</strong> {$metadata['generated_at']}</p>
        <p><strong>Total de Ligas:</strong> {$metadata['total_leagues']}</p>
    </div>

    <div class='summary'>
        <div class='metric'>
            <h3>Ligas Migradas</h3>
            <div class='value'>{$summary['leagues_with_dynamic_categories']}</div>
        </div>
        <div class='metric'>
            <h3>Ligas Pendientes</h3>
            <div class='value'>{$summary['leagues_without_dynamic_categories']}</div>
        </div>
        <div class='metric'>
            <h3>% Completitud</h3>
            <div class='value'>{$summary['migration_completion_percentage']}%</div>
        </div>
        <div class='metric'>
            <h3>Total Jugadoras</h3>
            <div class='value'>{$summary['total_players']}</div>
        </div>
    </div>

    <h2>📋 Detalle por Liga</h2>
    <table>
        <thead>
            <tr>
                <th>Liga</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Jugadoras</th>
                <th>Clubes</th>
                <th>Categorías</th>
                <th>Integridad</th>
            </tr>
        </thead>
        <tbody>";

        foreach ($leagues as $league) {
            $statusClass = $league['migration_status'] === 'migrated' ? 'status-migrated' : 'status-pending';

            $html .= "
            <tr>
                <td><strong>{$league['league_name']}</strong></td>
                <td>{$league['league_location']}</td>
                <td class='{$statusClass}'>" . ucfirst($league['migration_status']) . "</td>
                <td>{$league['total_players']}</td>
                <td>{$league['total_clubs']}</td>
                <td>{$league['categories_count']}</td>
                <td>{$league['integrity_status']}</td>
            </tr>";
        }

        $html .= "
        </tbody>
    </table>
</body>
</html>";

        file_put_contents($outputPath, $html);
    }
}