<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qr_scan_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_card_id')->nullable(); // Puede ser null si QR inválido
            $table->unsignedBigInteger('player_id')->nullable(); // Para logs incluso con QR inválido

            // Información del escaneo
            $table->string('qr_code_scanned', 128); // QR que se escaneó
            $table->string('scan_result', 20); // success, warning, error
            $table->string('verification_status', 30); // apta, no_apta, restriccion

            // Contexto del escaneo
            $table->unsignedBigInteger('scanned_by'); // Usuario verificador
            $table->string('scan_location', 200)->nullable(); // Lugar del partido
            $table->string('event_type', 50)->default('match'); // match, training, tournament
            $table->unsignedBigInteger('match_id')->nullable(); // Si es en un partido específico

            // Información técnica del escaneo
            $table->ipAddress('ip_address');
            $table->string('user_agent', 500)->nullable();
            $table->json('device_info')->nullable(); // Info del dispositivo móvil
            $table->decimal('latitude', 10, 8)->nullable(); // Geolocalización
            $table->decimal('longitude', 11, 8)->nullable();

            // Resultado de la verificación
            $table->json('verification_response'); // Respuesta completa del sistema
            $table->text('additional_notes')->nullable(); // Notas del verificador
            $table->boolean('manual_override')->default(false); // Si se forzó el resultado
            $table->text('override_reason')->nullable();

            // Información de timing
            $table->timestamp('scanned_at'); // Momento exacto del escaneo
            $table->unsignedSmallInteger('response_time_ms')->nullable(); // Tiempo de respuesta

            // Auditoría y troubleshooting
            $table->json('debug_info')->nullable(); // Para debugging
            $table->string('app_version', 20)->nullable(); // Versión de la app verificadora
            $table->timestamps();

            // Foreign keys
            $table->foreign('player_card_id')->references('id')->on('player_cards');
            $table->foreign('player_id')->references('id')->on('players');
            $table->foreign('scanned_by')->references('id')->on('users');

            // Índices para análisis y reporting
            $table->index(['player_id', 'scanned_at']);
            $table->index(['scanned_by', 'scanned_at']);
            $table->index(['scan_result', 'verification_status']);
            $table->index('qr_code_scanned'); // Para detectar intentos repetidos
            $table->index(['event_type', 'scanned_at']);
            $table->index(['latitude', 'longitude']); // Para análisis geográfico
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_scan_logs');
    }
};
