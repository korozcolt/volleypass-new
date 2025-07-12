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
        Schema::create('team_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('player_id');

            // Información específica del equipo
            $table->string('jersey_number', 3)->nullable(); // Número específico en este equipo
            $table->string('position', 30)->nullable(); // Posición en este equipo específico
            $table->boolean('is_captain')->default(false);

            // Fechas de participación
            $table->date('joined_at');
            $table->date('left_at')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('team_id')->references('id')->on('teams');
            $table->foreign('player_id')->references('id')->on('players');

            // Índices y constraints
            $table->unique(['team_id', 'player_id', 'joined_at']); // Una jugadora por equipo por período
            $table->unique(['team_id', 'jersey_number', 'left_at']); // Números únicos por equipo activo
            $table->index(['team_id', 'is_captain']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_players');
    }
};
