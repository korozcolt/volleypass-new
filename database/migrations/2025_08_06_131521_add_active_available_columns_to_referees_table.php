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
        Schema::table('referees', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('status');
            $table->boolean('is_available')->default(true)->after('is_active');
            
            // Add indexes
            $table->index(['status', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index(['is_active', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referees', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_active']);
            $table->dropIndex(['category', 'is_active']);
            $table->dropIndex(['is_active', 'is_available']);
            
            $table->dropColumn(['is_active', 'is_available']);
        });
    }
};
