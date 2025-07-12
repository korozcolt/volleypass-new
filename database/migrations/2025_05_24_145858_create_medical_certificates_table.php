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
        Schema::create('medical_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');

            // Información del certificado
            $table->string('certificate_type', 50)->default('sports_aptitude'); // aptitud_deportiva, especializado
            $table->string('certificate_number', 100)->nullable(); // Número del certificado médico
            $table->date('issue_date'); // Fecha de emisión
            $table->date('expires_at'); // Fecha de vencimiento

            // Información del médico/institución
            $table->string('doctor_name', 200);
            $table->string('doctor_license', 50); // Registro médico
            $table->string('medical_institution', 200);
            $table->string('institution_address', 300)->nullable();

            // Estado médico determinado
            $table->string('medical_status', 30); // usando enum MedicalStatus
            $table->text('medical_observations')->nullable();
            $table->json('restrictions')->nullable(); // Restricciones específicas
            $table->json('recommendations')->nullable(); // Recomendaciones médicas

            // Información específica del examen
            $table->decimal('blood_pressure_systolic', 5, 2)->nullable();
            $table->decimal('blood_pressure_diastolic', 5, 2)->nullable();
            $table->unsignedSmallInteger('heart_rate')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->json('additional_tests')->nullable(); // Exámenes adicionales

            // Estado administrativo
            $table->string('status', 20)->default('pending'); // usando enum DocumentStatus
            $table->unsignedBigInteger('reviewed_by')->nullable(); // Médico deportivo que revisó
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            // Archivos asociados
            $table->string('certificate_file_path', 500)->nullable(); // Archivo PDF/imagen
            $table->string('file_hash', 64)->nullable(); // Para integridad

            // Control de versiones y seguimiento
            $table->integer('version')->default(1);
            $table->boolean('is_current')->default(true); // Es el certificado vigente
            $table->unsignedBigInteger('replaces_certificate_id')->nullable();

            // Alertas y notificaciones
            $table->boolean('expiry_notification_sent')->default(false);
            $table->timestamp('expiry_notification_at')->nullable();

            // Auditoría
            $table->unsignedBigInteger('uploaded_by');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users');
            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->foreign('replaces_certificate_id')->references('id')->on('medical_certificates');

            // Índices para consultas frecuentes
            $table->index(['player_id', 'is_current', 'expires_at']);
            $table->index(['expires_at', 'status']); // Para alertas de vencimiento
            $table->index(['medical_status', 'expires_at']);
            $table->index('doctor_license'); // Para validar médicos
            $table->index(['certificate_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_certificates');
    }
};
