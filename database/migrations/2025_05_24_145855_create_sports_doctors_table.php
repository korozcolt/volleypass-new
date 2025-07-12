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
        Schema::create('sports_doctors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();

            // Información profesional
            $table->string('medical_license', 50)->unique();
            $table->string('specialization', 100)->nullable(); // Medicina Deportiva, Fisioterapia, etc.
            $table->string('institution', 150)->nullable(); // Clínica/Hospital donde trabaja

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

            // Índices
            $table->index('medical_license');
            $table->index('specialization');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sports_doctors');
    }
};
