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
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('club_id')->nullable();

            // Información profesional
            $table->string('license_number', 50)->nullable();
            $table->string('license_level', 50)->nullable(); // Nivel 1, 2, 3, etc.
            $table->string('specialization', 100)->nullable(); // Juvenil, Mayores, etc.
            $table->integer('experience_years')->default(0);

            // Estado
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('club_id')->references('id')->on('clubs');

            // Índices
            $table->index(['club_id', 'status']);
            $table->index('license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};
