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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id(); // BIGINT auto-increment
            $table->string('name', 150);
            $table->string('short_name', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();

            // Ubicación
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('city_id')->nullable();

            // Configuración
            $table->string('status', 20)->default('active');
            $table->date('foundation_date')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();

            // Configuraciones específicas
            $table->json('configurations')->nullable();
            $table->boolean('is_active')->default(true);

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('city_id')->references('id')->on('cities');

            // Índices
            $table->index(['status', 'is_active']);
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leagues');
    }
};
