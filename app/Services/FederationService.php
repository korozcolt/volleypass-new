<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Club;
use App\Models\Payment;
use App\Models\User;
use App\Enums\FederationStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class FederationService
{
    /**
     * Federar una jugadora con un pago específico
     */
    public function federatePlayer(Player $player, Payment $payment = null): bool
    {
        if ($payment) {
            if ($payment->status !== PaymentStatus::Verified) {
                throw new \Exception('El pago debe estar verificado para federar la jugadora');
            }

            if ($payment->type !== PaymentType::Federation) {
                throw new \Exception('El pago debe ser de tipo federación');
            }
        }

        $updateData = [
            'federation_status' => FederationStatus::Federated,
            'federation_date' => now(),
            'federation_expires_at' => now()->addYear(),
        ];

        if ($payment) {
            $updateData['federation_payment_id'] = $payment->id;
            $updateData['federation_notes'] = $this->addFederationNote(
                $player->federation_notes,
                "Federada con pago #{$payment->reference_number}"
            );
        } else {
            $updateData['federation_notes'] = $this->addFederationNote(
                $player->federation_notes,
                "Federada manualmente"
            );
        }

        $player->update($updateData);

        return true;
    }

    /**
     * Federar múltiples jugadoras de un club con un pago
     */
    public function federateClubPlayers(Club $club, Payment $payment, array $playerIds = []): array
    {
        if ($payment->status !== PaymentStatus::Verified) {
            throw new \Exception('El pago debe estar verificado');
        }

        $query = $club->players()->where('federation_status', '!=', FederationStatus::Federated);

        if (!empty($playerIds)) {
            $query->whereIn('id', $playerIds);
        }

        $players = $query->get();
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($players as $player) {
            try {
                $this->federatePlayer($player, $payment);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Jugadora {$player->user->full_name}: {$e->getMessage()}";
            }
        }

        return $results;
    }

    /**
     * Suspender federación de una jugadora
     */
    public function suspendPlayerFederation(Player $player, string $reason, User $suspendedBy): bool
    {
        $player->update([
            'federation_status' => FederationStatus::Suspended,
            'federation_notes' => $this->addFederationNote(
                $player->federation_notes,
                "SUSPENDIDA por {$suspendedBy->name}: {$reason}"
            ),
        ]);

        return true;
    }

    /**
     * Renovar federación de una jugadora
     */
    public function renewPlayerFederation(Player $player, Payment $payment): bool
    {
        if ($payment->status !== PaymentStatus::Verified) {
            throw new \Exception('El pago debe estar verificado para renovar la federación');
        }

        $newExpirationDate = $player->federation_expires_at && $player->federation_expires_at->isFuture()
            ? $player->federation_expires_at->addYear()
            : now()->addYear();

        $player->update([
            'federation_status' => FederationStatus::Federated,
            'federation_expires_at' => $newExpirationDate,
            'federation_payment_id' => $payment->id,
            'federation_notes' => $this->addFederationNote(
                $player->federation_notes,
                "Renovada hasta {$newExpirationDate->format('d/m/Y')} con pago #{$payment->reference_number}"
            ),
        ]);

        return true;
    }

    /**
     * Verificar y actualizar estados de federación vencidos
     */
    public function updateExpiredFederations(): array
    {
        $expiredPlayers = Player::where('federation_status', FederationStatus::Federated)
            ->where('federation_expires_at', '<', now())
            ->get();

        $updated = 0;
        foreach ($expiredPlayers as $player) {
            $player->update([
                'federation_status' => FederationStatus::Expired,
                'federation_notes' => $this->addFederationNote(
                    $player->federation_notes,
                    "Federación vencida el {$player->federation_expires_at->format('d/m/Y')}"
                ),
            ]);
            $updated++;
        }

        return [
            'updated' => $updated,
            'message' => "Se actualizaron {$updated} jugadoras con federación vencida"
        ];
    }

    /**
     * Obtener jugadoras próximas a vencer federación
     */
    public function getPlayersExpiringFederation(int $days = 30): Collection
    {
        return Player::where('federation_status', FederationStatus::Federated)
            ->whereBetween('federation_expires_at', [
                now(),
                now()->addDays($days)
            ])
            ->with(['user', 'currentClub'])
            ->get();
    }

    /**
     * Obtener estadísticas de federación por club
     */
    public function getClubFederationStats(Club $club): array
    {
        $players = $club->players();

        return [
            'total' => $players->count(),
            'federated' => $players->where('federation_status', FederationStatus::Federated)->count(),
            'not_federated' => $players->where('federation_status', FederationStatus::NotFederated)->count(),
            'pending_payment' => $players->whereIn('federation_status', [
                FederationStatus::PendingPayment,
                FederationStatus::PaymentSubmitted
            ])->count(),
            'expired' => $players->where('federation_status', FederationStatus::Expired)->count(),
            'suspended' => $players->where('federation_status', FederationStatus::Suspended)->count(),
        ];
    }

    /**
     * Obtener estadísticas generales de federación
     */
    public function getGeneralFederationStats(): array
    {
        $total = Player::count();

        return [
            'total_players' => $total,
            'federated' => Player::where('federation_status', FederationStatus::Federated)->count(),
            'not_federated' => Player::where('federation_status', FederationStatus::NotFederated)->count(),
            'pending_payment' => Player::whereIn('federation_status', [
                FederationStatus::PendingPayment,
                FederationStatus::PaymentSubmitted
            ])->count(),
            'expired' => Player::where('federation_status', FederationStatus::Expired)->count(),
            'suspended' => Player::where('federation_status', FederationStatus::Suspended)->count(),
            'expiring_soon' => $this->getPlayersExpiringFederation(30)->count(),
            'federation_percentage' => $total > 0 ? round((Player::where('federation_status', FederationStatus::Federated)->count() / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Verificar si una jugadora puede participar en torneos federados
     */
    public function canPlayInFederatedTournaments(Player $player): bool
    {
        return $player->federation_status === FederationStatus::Federated &&
               $player->federation_expires_at &&
               $player->federation_expires_at->isFuture() &&
               $player->is_eligible &&
               $player->medical_status->canPlay();
    }

    /**
     * Obtener jugadoras elegibles para torneos federados de un club
     */
    public function getEligibleFederatedPlayers(Club $club): Collection
    {
        return $club->players()
            ->where('federation_status', FederationStatus::Federated)
            ->where('federation_expires_at', '>', now())
            ->where('is_eligible', true)
            ->whereIn('medical_status', ['fit', 'restricted'])
            ->with(['user'])
            ->get();
    }

    /**
     * Procesar pago de federación pendiente
     */
    public function processPendingFederationPayment(Payment $payment): array
    {
        if ($payment->type !== PaymentType::Federation) {
            throw new \Exception('El pago debe ser de tipo federación');
        }

        if ($payment->status !== PaymentStatus::Verified) {
            throw new \Exception('El pago debe estar verificado');
        }

        // Buscar jugadoras del club con pago pendiente
        $pendingPlayers = Player::where('current_club_id', $payment->club_id)
            ->whereIn('federation_status', [
                FederationStatus::PendingPayment,
                FederationStatus::PaymentSubmitted,
                FederationStatus::NotFederated
            ])
            ->get();

        $results = ['processed' => 0, 'errors' => []];

        foreach ($pendingPlayers as $player) {
            try {
                $this->federatePlayer($player, $payment);
                $results['processed']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Error con {$player->user->full_name}: {$e->getMessage()}";
            }
        }

        return $results;
    }

    /**
     * Agregar nota a las notas de federación
     */
    private function addFederationNote(?string $existingNotes, string $newNote): string
    {
        $timestamp = now()->format('d/m/Y H:i');
        $formattedNote = "[{$timestamp}] {$newNote}";

        return $existingNotes
            ? $existingNotes . "\n" . $formattedNote
            : $formattedNote;
    }

    /**
     * Validar si un club puede federar jugadoras
     */
    public function canClubFederatePlayers(Club $club): array
    {
        $issues = [];

        if (!$club->is_active) {
            $issues[] = 'El club no está activo';
        }

        if (!$club->director_id) {
            $issues[] = 'El club no tiene director asignado';
        }

        if (!$club->league_id) {
            $issues[] = 'El club no está asociado a una liga';
        }

        return [
            'can_federate' => empty($issues),
            'issues' => $issues
        ];
    }
}
