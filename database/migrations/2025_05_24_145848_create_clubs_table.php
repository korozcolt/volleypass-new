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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id(); // BIGINT auto-increment
            $table->unsignedBigInteger('league_id');
            $table->string('name', 150);
            $table->string('short_name', 50)->nullable();
            $table->text('description')->nullable();

            // Ubicación
            $table->unsignedBigInteger('city_id');
            $table->text('address')->nullable();

            // Información de contacto
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('website')->nullable();

            // Información del club
            $table->date('foundation_date')->nullable();
            $table->string('colors', 100)->nullable(); // Colores del uniforme
            $table->text('history')->nullable();

            // Director del club
            $table->unsignedBigInteger('director_id')->nullable();

            // Configuraciones
            $table->string('status', 20)->default('active');
            $table->boolean('is_active')->default(true);
            $table->json('configurations')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('league_id')->references('id')->on('leagues');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('director_id')->references('id')->on('users');

            // Índices
            $table->index(['league_id', 'status']);
            $table->index('city_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
