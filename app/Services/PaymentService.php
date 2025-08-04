<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Player;
use App\Models\Club;
use App\Models\League;
use App\Models\User;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Crear pagos mensuales automáticos para todos los jugadores activos
     */
    public function createMonthlyPayments(int $month = null, int $year = null): Collection
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        $monthYear = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        
        $createdPayments = collect();
        
        // Obtener todos los jugadores activos con club
        $players = Player::with(['user', 'currentClub'])
            ->whereHas('currentClub')
            ->where('status', 'active')
            ->get();
            
        foreach ($players as $player) {
            // Verificar si ya existe un pago para este mes
            $existingPayment = Payment::where('player_id', $player->id)
                ->where('club_id', $player->current_club_id)
                ->where('type', PaymentType::MonthlyFee)
                ->where('month_year', $monthYear)
                ->first();
                
            if (!$existingPayment) {
                $payment = $this->createMonthlyPayment($player, $month, $year);
                $createdPayments->push($payment);
            }
        }
        
        return $createdPayments;
    }
    
    /**
     * Crear un pago mensual individual
     */
    public function createMonthlyPayment(Player $player, int $month, int $year): Payment
    {
        $monthYear = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $dueDate = Carbon::create($year, $month, 15); // Vence el 15 de cada mes
        
        // Obtener el monto de la mensualidad del club (esto debería estar en configuración del club)
        $amount = $player->currentClub->monthly_fee ?? 50000; // Valor por defecto
        
        return Payment::create([
            'player_id' => $player->id,
            'club_id' => $player->current_club_id,
            'user_id' => $player->user_id,
            'payer_type' => Player::class,
            'payer_id' => $player->id,
            'receiver_type' => Club::class,
            'receiver_id' => $player->current_club_id,
            'type' => PaymentType::MonthlyFee,
            'amount' => $amount,
            'currency' => 'COP',
            'status' => PaymentStatus::Pending,
            'payment_date' => Carbon::create($year, $month, 1),
            'due_date' => $dueDate,
            'month_year' => $monthYear,
            'is_recurring' => true,
            'description' => "Mensualidad {$this->getMonthName($month)} {$year}",
        ]);
    }
    
    /**
     * Crear un pago de club a liga
     */
    public function createClubToLeaguePayment(
        Club $club, 
        League $league, 
        float $amount, 
        PaymentType $type, 
        string $description,
        Carbon $dueDate = null
    ): Payment {
        return Payment::create([
            'club_id' => $club->id,
            'league_id' => $league->id,
            'payer_type' => Club::class,
            'payer_id' => $club->id,
            'receiver_type' => League::class,
            'receiver_id' => $league->id,
            'type' => $type,
            'amount' => $amount,
            'currency' => 'COP',
            'status' => PaymentStatus::Pending,
            'payment_date' => now(),
            'due_date' => $dueDate ?? now()->addDays(30),
            'description' => $description,
        ]);
    }
    
    /**
     * Crear un pago de jugador a club
     */
    public function createPlayerToClubPayment(
        Player $player, 
        Club $club, 
        float $amount, 
        PaymentType $type, 
        string $description,
        Carbon $dueDate = null
    ): Payment {
        return Payment::create([
            'player_id' => $player->id,
            'club_id' => $club->id,
            'user_id' => $player->user_id,
            'payer_type' => Player::class,
            'payer_id' => $player->id,
            'receiver_type' => Club::class,
            'receiver_id' => $club->id,
            'type' => $type,
            'amount' => $amount,
            'currency' => 'COP',
            'status' => PaymentStatus::Pending,
            'payment_date' => now(),
            'due_date' => $dueDate ?? now()->addDays(15),
            'description' => $description,
        ]);
    }
    
    /**
     * Subir comprobante de pago
     */
    public function uploadPaymentProof(Payment $payment, $file): Payment
    {
        // Guardar el archivo usando Spatie Media Library
        $payment->addMediaFromRequest('receipt')
            ->toMediaCollection('receipts');
            
        // Cambiar estado a "por verificación"
        $payment->markAsUnderVerification();
        
        return $payment->fresh();
    }
    
    /**
     * Verificar un pago
     */
    public function verifyPayment(Payment $payment, User $verifier, bool $approved, string $notes = null): Payment
    {
        if ($approved) {
            $payment->complete($verifier);
        } else {
            $payment->reject($verifier, $notes ?? 'Pago rechazado');
        }
        
        return $payment->fresh();
    }
    
    /**
     * Obtener pagos pendientes de un jugador
     */
    public function getPlayerPendingPayments(Player $player): Collection
    {
        return Payment::where('player_id', $player->id)
            ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::UnderVerification])
            ->orderBy('due_date')
            ->get();
    }
    
    /**
     * Verificar si un jugador está al día con sus pagos
     */
    public function isPlayerUpToDate(Player $player): bool
    {
        $pendingPayments = $this->getPlayerPendingPayments($player);
        $overduePayments = $pendingPayments->filter(function ($payment) {
            return $payment->due_date < now();
        });
        
        return $overduePayments->isEmpty();
    }
    
    /**
     * Obtener estado de cuenta de un jugador
     */
    public function getPlayerAccountStatus(Player $player): array
    {
        $allPayments = Payment::where('player_id', $player->id)
            ->orderBy('payment_date', 'desc')
            ->get();
            
        $pendingPayments = $allPayments->where('status', PaymentStatus::Pending);
        $overduePayments = $pendingPayments->filter(fn($p) => $p->due_date < now());
        $completedPayments = $allPayments->where('status', PaymentStatus::Completed);
        
        return [
            'is_up_to_date' => $overduePayments->isEmpty(),
            'total_pending' => $pendingPayments->sum('amount'),
            'total_overdue' => $overduePayments->sum('amount'),
            'total_paid' => $completedPayments->sum('amount'),
            'pending_count' => $pendingPayments->count(),
            'overdue_count' => $overduePayments->count(),
            'last_payment' => $completedPayments->first(),
            'next_due_payment' => $pendingPayments->sortBy('due_date')->first(),
        ];
    }
    
    /**
     * Obtener resumen de pagos de un club
     */
    public function getClubPaymentsSummary(Club $club): array
    {
        $playerPayments = Payment::where('club_id', $club->id)
            ->where('type', PaymentType::PlayerToClub)
            ->get();
            
        $leaguePayments = Payment::where('club_id', $club->id)
            ->where('type', PaymentType::ClubToLeague)
            ->get();
            
        return [
            'monthly_income' => $playerPayments->where('status', PaymentStatus::Completed)
                ->where('month_year', now()->format('Y-m'))
                ->sum('amount'),
            'pending_from_players' => $playerPayments->whereIn('status', [PaymentStatus::Pending, PaymentStatus::UnderVerification])
                ->sum('amount'),
            'pending_to_league' => $leaguePayments->whereIn('status', [PaymentStatus::Pending, PaymentStatus::UnderVerification])
                ->sum('amount'),
            'players_up_to_date' => $this->getPlayersUpToDateCount($club),
            'players_with_debt' => $this->getPlayersWithDebtCount($club),
        ];
    }
    
    /**
     * Marcar pagos como vencidos
     */
    public function markOverduePayments(): int
    {
        return Payment::where('due_date', '<', now())
            ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::UnderVerification])
            ->update(['status' => PaymentStatus::Overdue]);
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
    
    private function getPlayersUpToDateCount(Club $club): int
    {
        return Player::where('current_club_id', $club->id)
            ->whereDoesntHave('payments', function ($query) {
                $query->where('due_date', '<', now())
                      ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::UnderVerification, PaymentStatus::Overdue]);
            })
            ->count();
    }
    
    private function getPlayersWithDebtCount(Club $club): int
    {
        return Player::where('current_club_id', $club->id)
            ->whereHas('payments', function ($query) {
                $query->where('due_date', '<', now())
                      ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::UnderVerification, PaymentStatus::Overdue]);
            })
            ->count();
    }
}