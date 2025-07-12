<?php

namespace App\Notifications;

use App\Models\MedicalCertificate;
use App\Enums\NotificationType;
use App\Enums\Priority;

class MedicalCertificateExpiryNotification extends BaseVolleyPassNotification
{
    private MedicalCertificate $certificate;
    private int $daysLeft;

    public function __construct(MedicalCertificate $certificate, int $daysLeft, string $recipientRole = 'player')
    {
        $this->certificate = $certificate;
        $this->daysLeft = $daysLeft;
        $this->type = NotificationType::Medical_Expiry;

        $this->priority = match(true) {
            $daysLeft <= 3 => Priority::Urgent,
            $daysLeft <= 7 => Priority::High,
            default => Priority::Medium
        };

        parent::__construct([
            'certificate_id' => $certificate->id,
            'expires_at' => $certificate->expires_at->format('Y-m-d'),
            'days_left' => $daysLeft,
            'player_name' => $certificate->player->user->full_name,
            'doctor_name' => $certificate->doctor_name,
            'medical_status' => $certificate->medical_status->getLabel(),
        ], $recipientRole);
    }

    protected function getSubject(): string
    {
        return "🏥 Certificado médico vence en {$this->daysLeft} días";
    }

    protected function getMainMessage(): string
    {
        return "Tu certificado médico deportivo vence en {$this->daysLeft} días.";
    }

    protected function getDetailMessage(): string
    {
        return sprintf(
            "Jugadora: %s\nMédico: %s\nEstado actual: %s\nVence: %s",
            $this->data['player_name'],
            $this->data['doctor_name'],
            $this->data['medical_status'],
            $this->data['expires_at']
        );
    }

    protected function getActionText(): string
    {
        return 'Ir a Dashboard';
    }

    protected function getActionUrl($notifiable): string
    {
        return route('dashboard');
    }

    protected function getAdditionalData(): array
    {
        return [
            'Debes renovar tu certificado médico antes del vencimiento.',
            'Sin certificado válido, tu carnet será suspendido automáticamente.',
            'Contacta a un médico deportivo autorizado para la renovación.'
        ];
    }
}
