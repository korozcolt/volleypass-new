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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();

            // Información personal extendida
            $table->string('nickname', 50)->nullable();
            $table->text('bio')->nullable();
            $table->date('joined_date')->nullable(); // Fecha de ingreso al deporte

            // Información de contacto de emergencia
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relationship', 50)->nullable();

            // Información médica básica
            $table->string('blood_type', 5)->nullable(); // A+, O-, etc.
            $table->text('allergies')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('medications')->nullable();

            // Información adicional
            $table->string('t_shirt_size', 10)->nullable(); // XS, S, M, L, XL, XXL
            $table->json('social_media')->nullable(); // Instagram, Facebook, etc.
            $table->text('notes')->nullable(); // Notas administrativas

            // Configuraciones de privacidad
            $table->boolean('show_phone')->default(false);
            $table->boolean('show_email')->default(false);
            $table->boolean('show_address')->default(false);

            $table->string('whatsapp_number', 20)->nullable();
            $table->string('fcm_token', 255)->nullable();
            $table->json('app_settings')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Índices
            $table->index('blood_type');
            $table->index('joined_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
