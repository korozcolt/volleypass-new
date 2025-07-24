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
        Schema::create('player_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('award_id')->constrained()->onDelete('cascade');
            $table->foreignId('tournament_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
            
            // Información del premio
            $table->date('awarded_date');
            $table->integer('season')->default(2025);
            $table->string('category')->nullable(); // MVP, Mejor Atacante, etc.
            $table->integer('position')->nullable(); // 1er lugar, 2do lugar, etc.
            
            // Contexto del premio
            $table->string('competition_level')->nullable(); // Local, Regional, Nacional
            $table->text('achievement_description')->nullable();
            $table->json('statistics_snapshot')->nullable(); // Stats al momento del premio
            
            // Certificación
            $table->foreignId('awarded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('certificate_number')->nullable()->unique();
            $table->string('certificate_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Metadatos
            $table->decimal('points_earned', 8, 2)->default(0); // Puntos para ranking
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['player_id', 'awarded_date']);
            $table->index(['award_id', 'season']);
            $table->index(['tournament_id', 'category']);
            $table->index(['competition_level', 'position']);
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_awards');
    }
};
