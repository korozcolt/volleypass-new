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
        Schema::create('player_season_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('league_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('season')->default(2025);
            
            // Estadísticas agregadas de ataque
            $table->integer('total_attacks')->default(0);
            $table->integer('total_attack_kills')->default(0);
            $table->integer('total_attack_errors')->default(0);
            $table->decimal('avg_attack_percentage', 5, 2)->default(0);
            
            // Estadísticas agregadas de servicio
            $table->integer('total_serves')->default(0);
            $table->integer('total_service_aces')->default(0);
            $table->integer('total_service_errors')->default(0);
            $table->decimal('avg_service_percentage', 5, 2)->default(0);
            
            // Estadísticas agregadas de recepción
            $table->integer('total_receptions')->default(0);
            $table->integer('total_reception_errors')->default(0);
            $table->decimal('avg_reception_percentage', 5, 2)->default(0);
            
            // Estadísticas agregadas de bloqueo
            $table->integer('total_blocks')->default(0);
            $table->integer('total_block_kills')->default(0);
            $table->integer('total_block_errors')->default(0);
            
            // Estadísticas agregadas de defensa
            $table->integer('total_digs')->default(0);
            $table->integer('total_dig_errors')->default(0);
            
            // Estadísticas generales de temporada
            $table->integer('matches_played')->default(0);
            $table->integer('sets_played')->default(0);
            $table->integer('total_points_scored')->default(0);
            $table->integer('total_minutes_played')->default(0);
            $table->decimal('avg_points_per_match', 5, 2)->default(0);
            
            // Rankings y posiciones
            $table->integer('league_ranking')->nullable();
            $table->integer('category_ranking')->nullable();
            $table->decimal('performance_rating', 5, 2)->default(0);
            
            // Metadatos de temporada
            $table->date('season_start')->nullable();
            $table->date('season_end')->nullable();
            $table->json('achievements')->nullable();
            $table->json('season_highlights')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Índices y constraints
            $table->unique(['player_id', 'season', 'team_id']);
            $table->index(['season', 'league_id']);
            $table->index(['performance_rating', 'season']);
            $table->index(['league_ranking', 'category_ranking']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_season_statistics');
    }
};
