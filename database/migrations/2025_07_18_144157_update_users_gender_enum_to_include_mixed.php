<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para SQLite, necesitamos recrear la tabla con el nuevo enum
        if (config('database.default') === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('gender');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->enum('gender', ['male', 'female', 'mixed'])->nullable()->after('birth_date');
            });
        } else {
            // Para MySQL/PostgreSQL
            DB::statement("ALTER TABLE users MODIFY COLUMN gender ENUM('male', 'female', 'mixed')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Para SQLite, necesitamos recrear la tabla con el enum original
        if (config('database.default') === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('gender');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
            });
        } else {
            // Para MySQL/PostgreSQL
            DB::statement("ALTER TABLE users MODIFY COLUMN gender ENUM('male', 'female')");
        }
    }
};
