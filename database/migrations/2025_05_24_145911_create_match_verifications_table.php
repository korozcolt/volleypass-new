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
        Schema::create('match_verifications', function (Blueprint $table) {
            $table->id();

            // Información del partido/evento
            $table->string('event_name', 200); // Nombre del evento
            $table->string('event_type', 50)->default('match'); // match, tournament, training
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('venue', 200); // Lugar del evento

            // Equipos participantes
            $table->unsignedBigInteger('home_team_id')->nullable();
            $table->unsignedBigInteger('away_team_id')->nullable();
            $table->string('teams_description', 300)->nullable(); // Descripción libre si no hay equipos registrados

            // Información del verificador
            $table->unsignedBigInteger('verifier_id'); // Usuario verificador
            $table->timestamp('verification_started_at');
            $table->timestamp('verification_completed_at')->nullable();

            // Estadísticas de verificación
            $table->unsignedSmallInteger('total_players_verified')->default(0);
            $table->unsignedSmallInteger('approved_players')->default(0);
            $table->unsignedSmallInteger('rejected_players')->default(0);
            $table->unsignedSmallInteger('players_with_restrictions')->default(0);

            // Estado del proceso
            $table->string('status', 20)->default('in_progress'); // in_progress, completed, cancelled
            $table->text('verification_notes')->nullable();
            $table->json('summary_report')->nullable(); // Resumen detallado

            // Información técnica
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->ipAddress('ip_address');
            $table->json('device_info')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('home_team_id')->references('id')->on('teams');
            $table->foreign('away_team_id')->references('id')->on('teams');
            $table->foreign('verifier_id')->references('id')->on('users');

            // Índices para reporting
            $table->index(['event_date', 'event_type']);
            $table->index(['verifier_id', 'event_date']);
            $table->index(['status', 'event_date']);
            $table->index(['venue', 'event_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_verifications');
    }
};
