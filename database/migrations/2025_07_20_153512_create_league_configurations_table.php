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
        Schema::create('league_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->onDelete('cascade');
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->nullable()->index();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->string('validation_rules')->nullable();
            $table->text('default_value')->nullable();
            $table->timestamps();

            // Índices compuestos
            $table->unique(['league_id', 'key']);
            $table->index(['league_id', 'group']);
            $table->index(['league_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_configurations');
    }
};
