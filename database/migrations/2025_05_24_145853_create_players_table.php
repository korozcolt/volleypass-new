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
        Schema::create('players', function (Blueprint $table) {
            $table->id(); // BIGINT auto-increment
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('current_club_id')->nullable();

            // Información deportiva
            $table->string('jersey_number', 3)->nullable();
            $table->string('position', 30); // usando enum PlayerPosition
            $table->string('category', 30); // usando enum PlayerCategory
            $table->decimal('height', 5, 2)->nullable(); // en metros
            $table->decimal('weight', 5, 2)->nullable(); // en kg
            $table->string('dominant_hand', 10)->default('right'); // right, left, both

            // Estado deportivo
            $table->string('status', 20)->default('active'); // active, inactive, injured, suspended
            $table->string('medical_status', 30)->default('fit'); // usando enum MedicalStatus
            $table->date('debut_date')->nullable();
            $table->date('retirement_date')->nullable();

            // Información adicional
            $table->text('notes')->nullable();
            $table->json('achievements')->nullable(); // Logros especiales
            $table->json('preferences')->nullable(); // Preferencias de la jugadora

            // Control de elegibilidad
            $table->boolean('is_eligible')->default(false); // Elegible para jugar
            $table->date('eligibility_checked_at')->nullable();
            $table->unsignedBigInteger('eligibility_checked_by')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('current_club_id')->references('id')->on('clubs');
            $table->foreign('eligibility_checked_by')->references('id')->on('users');

            // Índices
            $table->index(['current_club_id', 'status']);
            $table->index(['position', 'category']);
            $table->index('is_eligible');
            $table->index('medical_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
