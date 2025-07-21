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
        Schema::create('card_generation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_card_id')->nullable()->constrained()->onDelete('set null');

            // Información del proceso
            $table->string('card_number', 20)->nullable();
            $table->string('status', 30); // pending, validating, generating, completed, failed
            $table->integer('processing_time_ms')->nullable(); // Tiempo en milisegundos
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Información adicional del proceso

            // Información de auditoría
            $table->string('generated_by', 20)->default('system_auto'); // system_auto, manual, etc.
            $table->foreignId('triggered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Información de validaciones
            $table->json('validation_results')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();

            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['player_id', 'status']);
            $table->index(['league_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('card_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_generation_logs');
    }
};
