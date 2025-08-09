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
        Schema::create('player_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('match_id')->nullable();
            $table->foreignId('tournament_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');

            // Estadísticas de ataque
            $table->integer('attacks')->default(0);
            $table->integer('attack_kills')->default(0);
            $table->integer('attack_errors')->default(0);
            $table->decimal('attack_percentage', 5, 2)->default(0);

            // Estadísticas de servicio
            $table->integer('serves')->default(0);
            $table->integer('service_aces')->default(0);
            $table->integer('service_errors')->default(0);
            $table->decimal('service_percentage', 5, 2)->default(0);

            // Estadísticas de recepción
            $table->integer('receptions')->default(0);
            $table->integer('reception_errors')->default(0);
            $table->decimal('reception_percentage', 5, 2)->default(0);

            // Estadísticas de bloqueo
            $table->integer('blocks')->default(0);
            $table->integer('block_kills')->default(0);
            $table->integer('block_errors')->default(0);

            // Estadísticas de defensa
            $table->integer('digs')->default(0);
            $table->integer('dig_errors')->default(0);

            // Estadísticas generales
            $table->integer('points_scored')->default(0);
            $table->integer('sets_played')->default(0);
            $table->integer('minutes_played')->default(0);

            // Metadatos
            $table->date('match_date')->nullable();
            $table->json('additional_stats')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['player_id', 'match_date']);
            $table->index(['tournament_id', 'player_id']);
            $table->index(['team_id', 'player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_statistics');
    }
};
