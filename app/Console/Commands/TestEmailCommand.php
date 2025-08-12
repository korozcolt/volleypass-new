<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {--to= : Email address to send test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify mail configuration with Resend';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando prueba de envío de email...');

        // Obtener el email de destino
        $toEmail = $this->option('to') ?: config('mail.contact', env('MAIL_CONTACT'));

        if (!$toEmail) {
            $this->error('❌ No se encontró email de destino. Usa --to=email@example.com o configura MAIL_CONTACT en .env');
            return 1;
        }

        $this->info("📧 Enviando email de prueba a: {$toEmail}");

        try {
            // Enviar email de prueba
            Mail::raw(
                $this->getTestEmailContent(),
                function (Message $message) use ($toEmail) {
                    $message->to($toEmail)
                        ->subject('🧪 Email de Prueba - VolleyPass')
                        ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );

            $this->info('✅ Email enviado exitosamente!');
            $this->info('📬 Revisa tu bandeja de entrada en: ' . $toEmail);

            // Mostrar información de configuración
            $this->newLine();
            $this->info('📋 Configuración actual:');
            $this->table(
                ['Configuración', 'Valor'],
                [
                    ['MAIL_MAILER', config('mail.default')],
                    ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                    ['MAIL_FROM_NAME', config('mail.from.name')],
                    ['RESEND_KEY', env('RESEND_KEY') ? '✅ Configurado' : '❌ No configurado'],
                    ['MAIL_CONTACT', env('MAIL_CONTACT')],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar el email:');
            $this->error($e->getMessage());

            $this->newLine();
            $this->warn('🔧 Verifica tu configuración:');
            $this->warn('1. RESEND_KEY está configurado en .env');
            $this->warn('2. MAIL_MAILER=resend en .env');
            $this->warn('3. MAIL_FROM_ADDRESS tiene un dominio válido');

            return 1;
        }
    }

    /**
     * Get the test email content
     */
    private function getTestEmailContent(): string
    {
        return "
🏐 VolleyPass - Email de Prueba

¡Hola!

Este es un email de prueba para verificar que la configuración de Resend está funcionando correctamente.

📊 Información del sistema:
- Aplicación: " . config('app.name') . "
- Entorno: " . config('app.env') . "
- Fecha: " . now()->format('d/m/Y H:i:s') . "
- Mailer: " . config('mail.default') . "

Si recibes este email, significa que:
✅ La configuración de Resend está correcta
✅ El API key es válido
✅ El sistema puede enviar emails

¡Todo listo para continuar con el desarrollo! 🚀

---
Sistema de Gestión de Voleibol
VolleyPass
        ";
    }
}
