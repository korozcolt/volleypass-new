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
        Schema::table('awards', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('slug')->unique()->after('name');
            $table->text('description')->nullable()->after('slug');
            $table->string('type')->after('description'); // individual, team, seasonal, tournament
            $table->string('category')->nullable()->after('type'); // mvp, top_scorer, best_defense, etc.
            $table->string('icon')->nullable()->after('category');
            $table->string('color', 7)->default('#FFD700')->after('icon');
            $table->integer('points_value')->default(0)->after('color');
            $table->boolean('generates_certificate')->default(true)->after('points_value');
            $table->json('criteria')->nullable()->after('generates_certificate');
            $table->boolean('requires_approval')->default(false)->after('criteria');
            $table->boolean('is_active')->default(true)->after('requires_approval');
            $table->integer('display_order')->default(0)->after('is_active');
            $table->json('metadata')->nullable()->after('display_order');
            $table->softDeletes()->after('updated_at');
            
            // Indexes
            $table->index(['type', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'name', 'slug', 'description', 'type', 'category', 'icon', 'color',
                'points_value', 'generates_certificate', 'criteria', 'requires_approval',
                'is_active', 'display_order', 'metadata'
            ]);
        });
    }
};
