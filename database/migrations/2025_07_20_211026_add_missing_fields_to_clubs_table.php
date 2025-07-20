<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            // Agregar campos faltantes si no existen
            if (!Schema::hasColumn('clubs', 'country_id')) {
                $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('clubs', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('clubs', 'founded_date')) {
                $table->date('founded_date')->nullable();
            }
            if (!Schema::hasColumn('clubs', 'settings')) {
                $table->json('settings')->nullable();
            }
            if (!Schema::hasColumn('clubs', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->dropColumn(['founded_date', 'settings', 'notes']);
        });
    }
};
