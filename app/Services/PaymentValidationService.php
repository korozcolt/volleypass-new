<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Club;
use App\Models\League;
use App\Models\User;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;

class PaymentValidationService
{
    /**
     * Validar un pago de federación
     */
    public function validateFederationPayment(Payment $payment): array
    {
        $errors = [];
        $warnings = [];

        // Validaciones básicas
        if ($payment->type !== PaymentType::Federation) {
            $errors[] = 'El pago debe ser de tipo federación';
        }

        if ($payment->amount <= 0) {
            $errors[] = 'El monto debe ser mayor a cero';
        }

        if (empty($payment->reference_number)) {
            $errors[] = 'Debe tener número de referencia';
        }

        // Validar club
        if (!$payment->club_id) {
            $errors[] = 'Debe estar asociado a un club';
        } else {
            $club = $payment->club;
            if (!$club->is_active) {
                $errors[] = 'El club no está activo';
            }
            if (!$club->director_id) {
                $warnings[] = 'El club no tiene director asignado';
            }
        }

        // Validar liga
        if (!$payment->league_id) {
            $errors[] = 'Debe estar asociado a una liga';
        } else {
            $league = $payment->league;
            if (!$league->is_active) {
                $errors[] = 'La liga no está activa';
            }
        }

        // Validar duplicados
        $duplicatePayment = Payment::where('reference_number', $payment->reference_number)
            ->where('id', '!=', $payment->id)
            ->where('status', '!=', PaymentStatus::Rejected)
            ->first();

        if ($duplicatePayment) {
            $errors[] = 'Ya existe un pago con este número de referencia';
        }

        // Validar comprobantes
        if ($payment->getMedia('receipts')->isEmpty()) {
            $warnings[] = 'No se han subido comprobantes de pago';
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'can_approve' => empty($errors) && $payment->status === PaymentStatus::Pending,
        ];
    }

    /**
     * Aprobar un pago
     */
    public function approvePayment(Payment $payment, User $approver, string $notes = null): bool
    {
        $validation = $this->validateFederationPayment($payment);

        if (!$validation['can_approve']) {
            throw new \Exception('El pago no puede ser aprobado: ' . implode(', ', $validation['errors']));
        }

        $payment->update([
            'status' => PaymentStatus::Verified,
            'verified_at' => now(),
            'verified_by' => $approver->id,
            'notes' => $notes ? ($payment->notes ? $payment->notes . "\n\n" : '') . "APROBADO: {$notes}" : $payment->notes,
        ]);

        // Procesar federación automáticamente si es pago de federación
        if ($payment->type === PaymentType::Federation) {
            app(FederationService::class)->processPendingFederationPayment($payment);
        }

        return true;
    }

    /**
     * Rechazar un pago
     */
    public function rejectPayment(Payment $payment, User $rejector, string $reason): bool
    {
        $payment->update([
            'status' => PaymentStatus::Rejected,
            'verified_at' => now(),
            'verified_by' => $rejector->id,
            'notes' => ($payment->notes ? $payment->notes . "\n\n" : '') . "RECHAZADO: {$reason}",
        ]);

        return true;
    }

    /**
     * Obtener pagos pendientes de validación
     */
    public function getPendingPayments(League $league = null): Collection
    {
        $query = Payment::where('status', PaymentStatus::Pending)
            ->with(['club', 'league', 'user']);

        if ($league) {
            $query->where('league_id', $league->id);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * Obtener estadísticas de pagos
     */
    public function getPaymentStats(League $league = null, PaymentType $type = null): array
    {
        $query = Payment::query();

        if ($league) {
            $query->where('league_id', $league->id);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $total = $query->count();
        $pending = (clone $query)->where('status', PaymentStatus::Pending)->count();
        $verified = (clone $query)->where('status', PaymentStatus::Verified)->count();
        $rejected = (clone $query)->where('status', PaymentStatus::Rejected)->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'verified' => $verified,
            'rejected' => $rejected,
            'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 2) : 0,
            'approval_rate' => ($verified + $rejected) > 0 ? round(($verified / ($verified + $rejected)) * 100, 2) : 0,
        ];
    }

    /**
     * Validar monto de pago según configuración de liga
     */
    public function validatePaymentAmount(Payment $payment): array
    {
        $errors = [];
        $warnings = [];

        if (!$payment->league) {
            return ['errors' => ['Liga no encontrada'], 'warnings' => []];
        }

        $league = $payment->league;
        $expectedAmount = null;

        // Obtener monto esperado según tipo de pago
        switch ($payment->type) {
            case PaymentType::Federation:
                $expectedAmount = $league->getConfiguration('federation_fee');
                break;
            case PaymentType::Registration:
                $expectedAmount = $league->getConfiguration('registration_fee');
                break;
            case PaymentType::Tournament:
                $expectedAmount = $league->getConfiguration('tournament_fee');
                break;
        }

        if ($expectedAmount && $payment->amount != $expectedAmount) {
            if ($payment->amount < $expectedAmount) {
                $errors[] = "Monto insuficiente. Esperado: {$expectedAmount}, Recibido: {$payment->amount}";
            } else {
                $warnings[] = "Monto superior al esperado. Esperado: {$expectedAmount}, Recibido: {$payment->amount}";
            }
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'expected_amount' => $expectedAmount,
        ];
    }

    /**
     * Procesar archivo de comprobante
     */
    public function processReceiptFile(Payment $payment, UploadedFile $file): bool
    {
        // Validar tipo de archivo
        $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Tipo de archivo no permitido. Solo se permiten imágenes (JPG, PNG) y PDF.');
        }

        // Validar tamaño (máximo 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('El archivo es demasiado grande. Máximo 5MB permitido.');
        }

        // Guardar archivo
        $payment->addMediaFromRequest('receipt')
            ->usingFileName($file->getClientOriginalName())
            ->toMediaCollection('receipts');

        return true;
    }

    /**
     * Generar reporte de pagos
     */
    public function generatePaymentReport(League $league, \DateTime $startDate, \DateTime $endDate): array
    {
        $payments = Payment::where('league_id', $league->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['club', 'user'])
            ->get();

        $report = [
            'period' => [
                'start' => $startDate->format('d/m/Y'),
                'end' => $endDate->format('d/m/Y'),
            ],
            'league' => $league->name,
            'summary' => [
                'total_payments' => $payments->count(),
                'total_amount' => $payments->sum('amount'),
                'by_status' => [
                    'pending' => $payments->where('status', PaymentStatus::Pending)->count(),
                    'verified' => $payments->where('status', PaymentStatus::Verified)->count(),
                    'rejected' => $payments->where('status', PaymentStatus::Rejected)->count(),
                ],
                'by_type' => [],
            ],
            'details' => [],
        ];

        // Agrupar por tipo
        foreach (PaymentType::cases() as $type) {
            $typePayments = $payments->where('type', $type);
            $report['summary']['by_type'][$type->value] = [
                'count' => $typePayments->count(),
                'amount' => $typePayments->sum('amount'),
            ];
        }

        // Detalles por club
        foreach ($payments->groupBy('club_id') as $clubId => $clubPayments) {
            $club = $clubPayments->first()->club;
            $report['details'][] = [
                'club' => $club->name,
                'payments' => $clubPayments->count(),
                'amount' => $clubPayments->sum('amount'),
                'pending' => $clubPayments->where('status', PaymentStatus::Pending)->count(),
                'verified' => $clubPayments->where('status', PaymentStatus::Verified)->count(),
                'rejected' => $clubPayments->where('status', PaymentStatus::Rejected)->count(),
            ];
        }

        return $report;
    }

    /**
     * Validar integridad de datos de pago
     */
    public function validatePaymentIntegrity(Payment $payment): array
    {
        $issues = [];

        // Verificar relaciones
        if ($payment->club_id && !$payment->club) {
            $issues[] = 'Club asociado no existe';
        }

        if ($payment->league_id && !$payment->league) {
            $issues[] = 'Liga asociada no existe';
        }

        if ($payment->user_id && !$payment->user) {
            $issues[] = 'Usuario asociado no existe';
        }

        if ($payment->verified_by && !$payment->verifier) {
            $issues[] = 'Usuario verificador no existe';
        }

        // Verificar consistencia de datos
        if ($payment->verified_at && !$payment->verified_by) {
            $issues[] = 'Pago marcado como verificado pero sin verificador';
        }

        if ($payment->verified_by && !$payment->verified_at) {
            $issues[] = 'Verificador asignado pero sin fecha de verificación';
        }

        if ($payment->status === PaymentStatus::Verified && !$payment->verified_at) {
            $issues[] = 'Estado verificado pero sin fecha de verificación';
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
        ];
    }
}
