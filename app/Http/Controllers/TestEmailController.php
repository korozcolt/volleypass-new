<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class TestEmailController extends Controller
{
    /**
     * Show the email test page
     */
    public function index()
    {
        return view('test-email.index', [
            'config' => $this->getMailConfig()
        ]);
    }

    /**
     * Test email sending via web endpoint
     */
    public function test(Request $request)
    {
        $toEmail = $request->get('to', config('mail.contact', env('MAIL_CONTACT')));

        if (!$toEmail) {
            return view('test-email.result', [
                'success' => false,
                'message' => 'No se encontró email de destino. Usa ?to=email@example.com o configura MAIL_CONTACT en .env',
                'error' => 'Email de destino no configurado',
                'config' => $this->getMailConfig()
            ]);
        }

        try {
            $startTime = microtime(true);

            // Enviar email de prueba
            Mail::raw(
                $this->getTestEmailContent(),
                function (Message $message) use ($toEmail) {
                    $message->to($toEmail)
                        ->subject('🧪 Email de Prueba - VolleyPass (Web Test)')
                        ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );

            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);

            return view('test-email.result', [
                'success' => true,
                'message' => 'Email enviado exitosamente!',
                'sent_to' => $toEmail,
                'sent_at' => now()->format('d/m/Y H:i:s'),
                'duration' => $duration,
                'config' => $this->getMailConfig()
            ]);
        } catch (\Exception $e) {
            return view('test-email.result', [
                'success' => false,
                'message' => 'Error al enviar el email',
                'error' => $e->getMessage(),
                'config' => $this->getMailConfig(),
                'suggestions' => [
                    'Verifica que RESEND_KEY está configurado en .env',
                    'Verifica que MAIL_MAILER=resend en .env',
                    'Verifica que MAIL_FROM_ADDRESS tiene un dominio válido y verificado en Resend',
                ]
            ]);
        }
    }

    /**
     * Execute the artisan command and return output
     */
    public function testCommand(Request $request)
    {
        $toEmail = $request->get('to');

        try {
            $startTime = microtime(true);

            // Crear un buffer para capturar la salida del comando
            $output = new BufferedOutput();

            // Ejecutar el comando artisan
            $exitCode = Artisan::call('email:test', [
                '--to' => $toEmail
            ], $output);

            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);

            // Obtener la salida del comando
            $commandOutput = $output->fetch();

            return view('test-email.command-result', [
                'success' => $exitCode === 0,
                'exit_code' => $exitCode,
                'command_output' => $commandOutput,
                'executed_at' => now()->format('d/m/Y H:i:s'),
                'duration' => $duration,
                'parameters' => [
                    'to' => $toEmail ?: config('mail.contact', env('MAIL_CONTACT'))
                ],
                'config' => $this->getMailConfig()
            ]);
        } catch (\Exception $e) {
            return view('test-email.command-result', [
                'success' => false,
                'message' => 'Error ejecutando el comando',
                'error' => $e->getMessage(),
                'executed_at' => now()->format('d/m/Y H:i:s'),
                'config' => $this->getMailConfig()
            ]);
        }
    }

    /**
     * Get mail configuration info
     */
    private function getMailConfig()
    {
        return [
            'mailer' => config('mail.default'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'contact_email' => config('mail.contact'),
            'resend_configured' => env('RESEND_KEY') ? true : false,
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
        ];
    }

    /**
     * Get the test email content
     */
    private function getTestEmailContent(): string
    {
        return "
🏐 VolleyPass - Email de Prueba (Web Endpoint)

¡Hola!

Este es un email de prueba enviado desde el endpoint web para verificar que la configuración de Resend está funcionando correctamente.

📊 Información del sistema:
- Aplicación: " . config('app.name') . "
- Entorno: " . config('app.env') . "
- Fecha: " . now()->format('d/m/Y H:i:s') . "
- Mailer: " . config('mail.default') . "
- Enviado desde: Endpoint Web (/test-email)

Si recibes este email, significa que:
✅ La configuración de Resend está correcta
✅ El API key es válido
✅ El sistema puede enviar emails desde endpoints web
✅ La integración web está funcionando

¡Todo listo para continuar con el desarrollo! 🚀

---
Sistema de Gestión de Voleibol
VolleyPass
        ";
    }
}
