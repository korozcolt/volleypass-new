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
        Schema::table('clubs', function (Blueprint $table) {
            // First, migrate data from Spanish columns to English columns
            // Only update if English column is null and Spanish column has data
            
            // Migrate nombre to name if name is null
            DB::statement('UPDATE clubs SET name = nombre WHERE name IS NULL AND nombre IS NOT NULL');
            
            // Migrate nombre_corto to short_name if short_name is null
            DB::statement('UPDATE clubs SET short_name = nombre_corto WHERE short_name IS NULL AND nombre_corto IS NOT NULL');
            
            // Migrate departamento_id to department_id if department_id is null
            DB::statement('UPDATE clubs SET department_id = departamento_id WHERE department_id IS NULL AND departamento_id IS NOT NULL');
            
            // Migrate direccion to address if address is null
            DB::statement('UPDATE clubs SET address = direccion WHERE address IS NULL AND direccion IS NOT NULL');
            
            // Migrate telefono to phone if phone is null
            DB::statement('UPDATE clubs SET phone = telefono WHERE phone IS NULL AND telefono IS NOT NULL');
            
            // Migrate fundacion to foundation_date if foundation_date is null
            DB::statement('UPDATE clubs SET foundation_date = fundacion WHERE foundation_date IS NULL AND fundacion IS NOT NULL');
        });
        
        Schema::table('clubs', function (Blueprint $table) {
            // Drop foreign key constraints first if they exist
            $foreignKeys = DB::select("PRAGMA foreign_key_list(clubs)");
            foreach ($foreignKeys as $fk) {
                if ($fk->from === 'departamento_id') {
                    $table->dropForeign(['departamento_id']);
                    break;
                }
            }
        });
        
        // Drop indexes if they exist using raw SQL
        $indexes = DB::select("PRAGMA index_list(clubs)");
        $indexNames = array_column($indexes, 'name');
        
        if (in_array('clubs_es_federado_index', $indexNames)) {
            DB::statement('DROP INDEX clubs_es_federado_index');
        }
        if (in_array('clubs_tipo_federacion_index', $indexNames)) {
            DB::statement('DROP INDEX clubs_tipo_federacion_index');
        }
        if (in_array('clubs_vencimiento_federacion_index', $indexNames)) {
            DB::statement('DROP INDEX clubs_vencimiento_federacion_index');
        }
        if (in_array('clubs_departamento_id_index', $indexNames)) {
            DB::statement('DROP INDEX clubs_departamento_id_index');
        }
        
        Schema::table('clubs', function (Blueprint $table) {
            // Drop Spanish columns
            $table->dropColumn([
                'nombre',
                'nombre_corto',
                'departamento_id',
                'direccion',
                'telefono',
                'fundacion'
            ]);
        });
        
        Schema::table('clubs', function (Blueprint $table) {
            // Rename Spanish federation fields to English
            $table->renameColumn('es_federado', 'is_federated');
            $table->renameColumn('tipo_federacion', 'federation_type');
            $table->renameColumn('codigo_federacion', 'federation_code');
            $table->renameColumn('vencimiento_federacion', 'federation_expires_at');
            $table->renameColumn('observaciones_federacion', 'federation_notes');
            
            // Add indexes for renamed columns
            $table->index('is_federated');
            $table->index('federation_type');
            $table->index('federation_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            // Rename English federation fields back to Spanish
            $table->renameColumn('is_federated', 'es_federado');
            $table->renameColumn('federation_type', 'tipo_federacion');
            $table->renameColumn('federation_code', 'codigo_federacion');
            $table->renameColumn('federation_expires_at', 'vencimiento_federacion');
            $table->renameColumn('federation_notes', 'observaciones_federacion');
        });
        
        Schema::table('clubs', function (Blueprint $table) {
            // Re-add Spanish columns
            $table->string('nombre', 150)->nullable()->after('name');
            $table->string('nombre_corto', 50)->nullable()->after('short_name');
            $table->unsignedBigInteger('departamento_id')->nullable()->after('city_id');
            $table->string('direccion')->nullable()->after('address');
            $table->string('telefono', 20)->nullable()->after('phone');
            $table->date('fundacion')->nullable()->after('foundation_date');
            
            // Re-add indexes
            $table->index('es_federado');
            $table->index('tipo_federacion');
            $table->index('vencimiento_federacion');
            $table->index('departamento_id');
            
            // Re-add foreign key
            $table->foreign('departamento_id')->references('id')->on('departments');
        });
    }
};