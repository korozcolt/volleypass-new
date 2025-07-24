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
        Schema::create('tournament_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Grupo A, Grupo B, etc.
            $table->string('slug');
            $table->text('description')->nullable();
            $table->integer('group_number');
            $table->integer('max_teams')->default(4);
            $table->integer('current_teams')->default(0);
            $table->json('standings')->nullable(); // Tabla de posiciones
            $table->json('schedule')->nullable(); // Calendario de partidos
            $table->json('rules')->nullable(); // Reglas específicas del grupo
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->unique(['tournament_id', 'group_number']);
            $table->unique(['tournament_id', 'slug']);
            $table->index(['tournament_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_groups');
    }
};
