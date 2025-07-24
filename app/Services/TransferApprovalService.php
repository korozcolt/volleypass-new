<?php

namespace App\Services;

use App\Models\PlayerTransfer;
use App\Models\Player;
use App\Models\Club;
use App\Models\User;
use App\Enums\TransferStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TransferApprovedNotification;
use App\Notifications\TransferRejectedNotification;
use App\Notifications\TransferRequestNotification;
use Illuminate\Validation\ValidationException;

class TransferApprovalService
{
    /**
     * Valida una solicitud de traspaso
     */
    public function validateTransferRequest(PlayerTransfer $transfer): array
    {
        $errors = [];
        $warnings = [];

        // Validar que la jugadora existe y está activa
        if (!$transfer->player || !$transfer->player->is_active) {
            $errors[] = 'La jugadora no existe o no está activa';
        }

        // Validar que los clubes existen
        if (!$transfer->fromClub || !$transfer->toClub) {
            $errors[] = 'Los clubes de origen y destino deben existir';
        }

        // Validar que no sea el mismo club
        if ($transfer->from_club_id === $transfer->to_club_id) {
            $errors[] = 'El club de origen y destino no pueden ser el mismo';
        }

        // Validar que la jugadora pertenece al club de origen
        if ($transfer->player && $transfer->player->current_club_id !== $transfer->from_club_id) {
            $errors[] = 'La jugadora no pertenece al club de origen especificado';
        }

        // Validar ventana de traspasos
        if (!$this->isTransferWindowOpen($transfer->league_id)) {
            $warnings[] = 'La ventana de traspasos está cerrada para esta liga';
        }

        // Validar límite de traspasos por temporada
        $transferCount = $this->getPlayerTransferCount($transfer->player_id, $transfer->league_id);
        $maxTransfers = league_config($transfer->league_id, 'max_transfers_per_season', 3);
        
        if ($transferCount >= $maxTransfers) {
            $errors[] = "La jugadora ha alcanzado el límite de {$maxTransfers} traspasos por temporada";
        }

        // Validar estado de federación si es requerido
        if ($this->requiresFederationValidation($transfer)) {
            if (!$transfer->player->is_federated) {
                $warnings[] = 'La jugadora no está federada';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'requires_league_approval' => $transfer->requiresLeagueApproval(),
        ];
    }

    /**
     * Procesa la aprobación de un traspaso
     */
    public function processTransferApproval(PlayerTransfer $transfer, User $approver, ?string $notes = null): bool
    {
        try {
            DB::beginTransaction();

            // Validar que el traspaso puede ser aprobado
            if (!$transfer->canBeApproved()) {
                throw new ValidationException('El traspaso no puede ser aprobado en su estado actual');
            }

            // Validar permisos del aprobador
            if (!$this->canUserApproveTransfer($approver, $transfer)) {
                throw new ValidationException('El usuario no tiene permisos para aprobar este traspaso');
            }

            // Realizar validaciones finales
            $validation = $this->validateTransferRequest($transfer);
            if (!$validation['valid']) {
                throw new ValidationException('El traspaso no cumple con los requisitos: ' . implode(', ', $validation['errors']));
            }

            // Aprobar el traspaso
            $transfer->update([
                'status' => TransferStatus::Approved,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'notes' => $notes ? ($transfer->notes ? $transfer->notes . "\n\n" . $notes : $notes) : $transfer->notes,
            ]);

            // Ejecutar el traspaso
            $this->executeTransfer($transfer);

            // Enviar notificaciones
            $this->notifyTransferParties($transfer, 'approved');

            // Log de la acción
            Log::info('Traspaso aprobado', [
                'transfer_id' => $transfer->id,
                'player_id' => $transfer->player_id,
                'from_club_id' => $transfer->from_club_id,
                'to_club_id' => $transfer->to_club_id,
                'approved_by' => $approver->id,
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al aprobar traspaso', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Procesa el rechazo de un traspaso
     */
    public function processTransferRejection(PlayerTransfer $transfer, User $approver, string $reason): bool
    {
        try {
            DB::beginTransaction();

            // Validar que el traspaso puede ser rechazado
            if (!$transfer->canBeApproved()) {
                throw new ValidationException('El traspaso no puede ser rechazado en su estado actual');
            }

            // Validar permisos del aprobador
            if (!$this->canUserApproveTransfer($approver, $transfer)) {
                throw new ValidationException('El usuario no tiene permisos para rechazar este traspaso');
            }

            // Rechazar el traspaso
            $transfer->update([
                'status' => TransferStatus::Rejected,
                'approved_by' => $approver->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            // Enviar notificaciones
            $this->notifyTransferParties($transfer, 'rejected');

            // Log de la acción
            Log::info('Traspaso rechazado', [
                'transfer_id' => $transfer->id,
                'player_id' => $transfer->player_id,
                'rejected_by' => $approver->id,
                'reason' => $reason,
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al rechazar traspaso', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Actualiza el club de la jugadora tras un traspaso aprobado
     */
    public function updatePlayerClub(PlayerTransfer $transfer): bool
    {
        try {
            DB::beginTransaction();

            $player = $transfer->player;
            $user = $player->user;

            // Actualizar el club actual de la jugadora
            $player->update([
                'current_club_id' => $transfer->to_club_id,
                'updated_at' => now(),
            ]);

            // Actualizar el club del usuario
            $user->update([
                'club_id' => $transfer->to_club_id,
                'updated_at' => now(),
            ]);

            // Marcar el traspaso como completado
            $transfer->update([
                'status' => TransferStatus::Completed,
                'effective_date' => now(),
            ]);

            // Log del cambio
            Log::info('Club de jugadora actualizado', [
                'player_id' => $player->id,
                'user_id' => $user->id,
                'from_club_id' => $transfer->from_club_id,
                'to_club_id' => $transfer->to_club_id,
                'transfer_id' => $transfer->id,
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar club de jugadora', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Envía notificaciones a las partes involucradas en el traspaso
     */
    public function notifyTransferParties(PlayerTransfer $transfer, string $action): void
    {
        try {
            $player = $transfer->player;
            $fromClub = $transfer->fromClub;
            $toClub = $transfer->toClub;
            $requester = $transfer->requestedBy;
            $approver = $transfer->approvedBy;

            switch ($action) {
                case 'requested':
                    // Notificar al administrador de la liga
                    if ($transfer->requiresLeagueApproval()) {
                        $leagueAdmins = User::whereHas('roles', function ($query) {
                            $query->where('name', 'Liga Admin');
                        })->get();

                        Notification::send($leagueAdmins, new TransferRequestNotification($transfer));
                    }

                    // Notificar al club de destino
                    $toClubDirectors = $toClub->directors()->get();
                    Notification::send($toClubDirectors, new TransferRequestNotification($transfer));
                    break;

                case 'approved':
                    // Notificar a la jugadora
                    Notification::send($player->user, new TransferApprovedNotification($transfer));

                    // Notificar al solicitante
                    if ($requester) {
                        Notification::send($requester, new TransferApprovedNotification($transfer));
                    }

                    // Notificar a ambos clubes
                    $fromClubDirectors = $fromClub->directors()->get();
                    $toClubDirectors = $toClub->directors()->get();
                    
                    Notification::send($fromClubDirectors, new TransferApprovedNotification($transfer));
                    Notification::send($toClubDirectors, new TransferApprovedNotification($transfer));
                    break;

                case 'rejected':
                    // Notificar a la jugadora
                    Notification::send($player->user, new TransferRejectedNotification($transfer));

                    // Notificar al solicitante
                    if ($requester) {
                        Notification::send($requester, new TransferRejectedNotification($transfer));
                    }

                    // Notificar al club de origen
                    $fromClubDirectors = $fromClub->directors()->get();
                    Notification::send($fromClubDirectors, new TransferRejectedNotification($transfer));
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Error al enviar notificaciones de traspaso', [
                'transfer_id' => $transfer->id,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Ejecuta el traspaso tras la aprobación
     */
    private function executeTransfer(PlayerTransfer $transfer): bool
    {
        return $this->updatePlayerClub($transfer);
    }

    /**
     * Verifica si la ventana de traspasos está abierta
     */
    private function isTransferWindowOpen(int $leagueId): bool
    {
        return is_transfer_window_open($leagueId);
    }

    /**
     * Obtiene el número de traspasos de una jugadora en la temporada actual
     */
    private function getPlayerTransferCount(int $playerId, int $leagueId): int
    {
        return PlayerTransfer::where('player_id', $playerId)
            ->where('league_id', $leagueId)
            ->whereIn('status', [TransferStatus::Approved, TransferStatus::Completed])
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Verifica si el traspaso requiere validación de federación
     */
    private function requiresFederationValidation(PlayerTransfer $transfer): bool
    {
        return league_config($transfer->league_id, 'require_federation_for_transfers', false);
    }

    /**
     * Verifica si un usuario puede aprobar un traspaso
     */
    private function canUserApproveTransfer(User $user, PlayerTransfer $transfer): bool
    {
        // Los administradores de liga pueden aprobar cualquier traspaso
        if ($user->hasRole('Liga Admin')) {
            return true;
        }

        // Los directores pueden aprobar traspasos dentro de su liga
        if ($user->hasRole('Director') && $user->club && $user->club->league_id === $transfer->league_id) {
            return true;
        }

        return false;
    }

    /**
     * Obtiene estadísticas de traspasos para una liga
     */
    public function getTransferStatistics(int $leagueId, ?int $year = null): array
    {
        $year = $year ?? now()->year;

        $query = PlayerTransfer::where('league_id', $leagueId)
            ->whereYear('created_at', $year);

        return [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', TransferStatus::Pending)->count(),
            'approved' => $query->clone()->where('status', TransferStatus::Approved)->count(),
            'rejected' => $query->clone()->where('status', TransferStatus::Rejected)->count(),
            'completed' => $query->clone()->where('status', TransferStatus::Completed)->count(),
            'cancelled' => $query->clone()->where('status', TransferStatus::Cancelled)->count(),
        ];
    }

    /**
     * Obtiene traspasos pendientes que requieren atención
     */
    public function getPendingTransfersRequiringAttention(int $leagueId): \Illuminate\Database\Eloquent\Collection
    {
        $timeoutDays = league_config($leagueId, 'transfer_timeout_days', 7);
        $timeoutDate = now()->subDays($timeoutDays);

        return PlayerTransfer::where('league_id', $leagueId)
            ->where('status', TransferStatus::Pending)
            ->where('created_at', '<=', $timeoutDate)
            ->with(['player.user', 'fromClub', 'toClub', 'requestedBy'])
            ->get();
    }
}