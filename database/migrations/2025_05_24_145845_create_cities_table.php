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
        Schema::create('cities', function (Blueprint $table) {
            $table->id(); // BIGINT auto-increment
            $table->unsignedBigInteger('department_id');
            $table->string('name', 100);
            $table->string('code', 10)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->boolean('is_active')->default(true);

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');

            // Índices
            $table->unique(['department_id', 'code']);
            $table->index('is_active');
            $table->index('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
