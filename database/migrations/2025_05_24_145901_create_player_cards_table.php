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
        Schema::create('player_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');

            // Identificación del carnet
            $table->string('card_number', 20)->unique(); // VP-2025-001234
            $table->string('qr_code', 128)->unique(); // Hash único para QR
            $table->string('verification_token', 64)->unique(); // Token adicional de seguridad

            // Estado y validez
            $table->string('status', 30)->default('active'); // usando enum CardStatus
            $table->date('issued_at');
            $table->date('expires_at');
            $table->integer('season')->default(2025); // Temporada deportiva

            // Información médica al momento de emisión
            $table->string('medical_status', 30); // Estado médico al generar
            $table->date('medical_check_date')->nullable();
            $table->unsignedBigInteger('medical_approved_by')->nullable();

            // Información administrativa
            $table->unsignedBigInteger('issued_by'); // Quien generó el carnet
            $table->unsignedBigInteger('approved_by')->nullable(); // Aprobación final
            $table->timestamp('approved_at')->nullable();

            // Configuración de restricciones
            $table->json('restrictions')->nullable(); // Restricciones específicas
            $table->json('card_design_data')->nullable(); // Datos para diseño personalizado

            // Metadatos de verificación
            $table->timestamp('last_verified_at')->nullable();
            $table->unsignedInteger('verification_count')->default(0);
            $table->json('verification_locations')->nullable(); // Últimas ubicaciones

            // Control de versiones
            $table->integer('version')->default(1);
            $table->unsignedBigInteger('replaces_card_id')->nullable(); // Carnet que reemplaza
            $table->string('replacement_reason')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('issued_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->foreign('medical_approved_by')->references('id')->on('users');
            $table->foreign('replaces_card_id')->references('id')->on('player_cards');

            // Índices críticos para verificación rápida
            $table->index('qr_code'); // Búsqueda por QR
            $table->index('verification_token'); // Verificación adicional
            $table->index(['player_id', 'status', 'expires_at']);
            $table->index(['status', 'expires_at']); // Carnets activos/vencidos
            $table->index(['season', 'status']);
            $table->index('card_number'); // Búsqueda por número
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_cards');
    }
};
