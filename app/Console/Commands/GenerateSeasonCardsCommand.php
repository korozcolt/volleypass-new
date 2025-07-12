<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GenerateSeasonCards;

class GenerateSeasonCardsCommand extends Command
{
    protected $signature = 'volleypass:generate-season-cards
                           {season? : Año de la temporada}
                           {--all : Incluir jugadoras no elegibles}';

    protected $description = 'Generar carnets para una nueva temporada';

    public function handle(): int
    {
        $season = $this->argument('season') ?? now()->year;
        $onlyEligible = !$this->option('all');

        if (!$this->confirm("¿Generar carnets para la temporada {$season}?")) {
            $this->info('Operación cancelada');
            return self::SUCCESS;
        }

        $this->info("Generando carnets para temporada {$season}...");

        try {
            GenerateSeasonCards::dispatch($season, $onlyEligible);

            $this->info('Job de generación de carnets encolado exitosamente');
            $this->info('Puedes seguir el progreso en los logs');

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error generando carnets: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
