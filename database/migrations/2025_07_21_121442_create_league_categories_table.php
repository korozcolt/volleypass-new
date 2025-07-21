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
        Schema::create('league_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->string('name', 100); // Mini, Pre-Mini, Infantil, etc.
            $table->string('code', 20)->nullable(); // Código corto para la categoría
            $table->text('description')->nullable();
            $table->enum('gender', ['male', 'female', 'mixed'])->default('mixed');
            $table->integer('min_age');
            $table->integer('max_age');
            $table->json('special_rules')->nullable(); // Reglas especiales en JSON
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // Para ordenamiento
            $table->string('color', 7)->nullable(); // Color hex para UI
            $table->string('icon', 50)->nullable(); // Icono para UI
            $table->timestamps();

            // Índices optimizados para consultas frecuentes
            $table->index(['league_id', 'is_active']);
            $table->index(['league_id', 'gender', 'is_active']);
            $table->index(['league_id', 'min_age', 'max_age']);
            $table->index(['league_id', 'sort_order']);

            // Índice único para evitar duplicados de nombre por liga
            $table->unique(['league_id', 'name']);

            // Índice único para código por liga (si se especifica)
            $table->unique(['league_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_categories');
    }
};
