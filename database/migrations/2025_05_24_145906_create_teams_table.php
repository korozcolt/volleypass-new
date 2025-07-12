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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id');
            $table->string('name', 100);

            // Clasificación del equipo
            $table->string('category', 30); // usando enum PlayerCategory
            $table->string('gender', 20); // usando enum Gender

            // Entrenador asignado
            $table->unsignedBigInteger('coach_id')->nullable();

            // Estado del equipo
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('club_id')->references('id')->on('clubs');
            $table->foreign('coach_id')->references('id')->on('coaches');

            // Índices
            $table->index(['club_id', 'category', 'gender']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
