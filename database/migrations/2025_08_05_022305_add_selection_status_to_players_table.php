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
        Schema::table('players', function (Blueprint $table) {
            $table->enum('selection_status', ['NONE', 'PRESELECCION', 'SELECCION'])->default('NONE')->after('status');
            $table->timestamp('selection_date')->nullable()->after('selection_status');
            $table->text('selection_notes')->nullable()->after('selection_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['selection_status', 'selection_date', 'selection_notes']);
        });
    }
};
