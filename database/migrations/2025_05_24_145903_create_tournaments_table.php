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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Configuración del torneo
            $table->string('type')->default('league'); // league, cup, friendly
            $table->string('format')->default('round_robin'); // round_robin, elimination, mixed
            $table->string('category')->nullable(); // Categoría de edad
            $table->string('gender')->nullable(); // male, female, mixed
            
            // Fechas
            $table->date('registration_start');
            $table->date('registration_end');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('season')->default(2025);
            
            // Configuración de participación
            $table->integer('max_teams')->nullable();
            $table->integer('min_teams')->default(2);
            $table->decimal('registration_fee', 10, 2)->nullable();
            $table->string('currency', 3)->default('COP');
            
            // Estado
            $table->string('status')->default('draft'); // draft, open, closed, active, finished, cancelled
            $table->boolean('is_public')->default(true);
            $table->boolean('requires_approval')->default(false);
            
            // Configuración de reglas
            $table->json('rules')->nullable(); // Reglas específicas del torneo
            $table->json('prizes')->nullable(); // Premios del torneo
            $table->json('settings')->nullable(); // Configuraciones adicionales
            
            // Organización
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->string('venue')->nullable();
            $table->text('venue_address')->nullable();
            $table->text('notes')->nullable();
            
            // Metadatos
            $table->integer('total_teams')->default(0);
            $table->integer('total_matches')->default(0);
            $table->json('statistics')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['league_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['category', 'gender']);
            $table->index(['season', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
