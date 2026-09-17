<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('casos_atencion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->nullable()->unique()->constrained('evaluaciones')->cascadeOnDelete();
            $table->foreignId('analisis_nlp_id')->nullable()->unique()->constrained('analisis_nlp')->cascadeOnDelete();
            $table->foreignId('psicologo_id')->nullable()->constrained('psicologos')->nullOnDelete();
            $table->enum('estado', ['pendiente', 'asignado', 'seguimiento', 'cerrado'])->default('pendiente')->index();
            $table->timestamp('asignado_at')->nullable();
            $table->timestamp('cerrado_at')->nullable();
            $table->unsignedInteger('version')->default(0);
            $table->timestamps();
        });
        Schema::create('seguimientos_caso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_atencion_id')->constrained('casos_atencion')->cascadeOnDelete();
            $table->foreignId('autor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('responsable_id')->nullable()->constrained('psicologos')->nullOnDelete();
            $table->string('tipo', 30);
            $table->text('nota')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimientos_caso');
        Schema::dropIfExists('casos_atencion');
    }
};
