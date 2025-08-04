<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PaymentService;
use Carbon\Carbon;

class GenerateMonthlyPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:generate-monthly {--month=} {--year=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar pagos mensuales automáticos para todos los jugadores activos';

    /**
     * Execute the console command.
     */
    public function handle(PaymentService $paymentService)
    {
        $month = $this->option('month') ?? now()->month;
        $year = $this->option('year') ?? now()->year;
        $force = $this->option('force');
        
        // Validar que sea el primer día del mes o que se use --force
        if (!$force && now()->day !== 1) {
            $this->error('Este comando solo se puede ejecutar el primer día del mes. Use --force para ejecutar en cualquier momento.');
            return 1;
        }
        
        $this->info("Generando pagos mensuales para {$this->getMonthName($month)} {$year}...");
        
        try {
            $createdPayments = $paymentService->createMonthlyPayments($month, $year);
            
            if ($createdPayments->isEmpty()) {
                $this->info('No se crearon nuevos pagos. Es posible que ya existan para este período.');
            } else {
                $this->info("Se crearon {$createdPayments->count()} pagos mensuales exitosamente.");
                
                // Mostrar resumen por club
                $paymentsByClub = $createdPayments->groupBy('club_id');
                $this->table(
                    ['Club', 'Jugadores', 'Total'],
                    $paymentsByClub->map(function ($payments, $clubId) {
                        $club = $payments->first()->club;
                        return [
                            $club->name,
                            $payments->count(),
                            '$' . number_format($payments->sum('amount'), 0, ',', '.')
                        ];
                    })->values()->toArray()
                );
            }
            
            // Marcar pagos vencidos
            $overdueCount = $paymentService->markOverduePayments();
            if ($overdueCount > 0) {
                $this->warn("Se marcaron {$overdueCount} pagos como vencidos.");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error al generar pagos mensuales: ' . $e->getMessage());
            return 1;
        }
    }
    
    private function getMonthName(int $month): string
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $months[$month] ?? 'Mes';
    }
}