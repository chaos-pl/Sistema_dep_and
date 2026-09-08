<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dass21_evaluations', function (Blueprint $table) {
            $table->id();

            // Reemplazamos user_id por codigo_anonimo
            $table->string('codigo_anonimo');

            $table->foreignId('instrument_id')
                ->constrained('instrumentos')
                ->cascadeOnDelete();

            // Depresión
            $table->unsignedTinyInteger('depression_raw')->default(0);
            $table->unsignedTinyInteger('depression_score')->default(0);
            $table->string('depression_level', 30)->default('Normal');

            // Ansiedad
            $table->unsignedTinyInteger('anxiety_raw')->default(0);
            $table->unsignedTinyInteger('anxiety_score')->default(0);
            $table->string('anxiety_level', 30)->default('Normal');

            // Estrés
            $table->unsignedTinyInteger('stress_raw')->default(0);
            $table->unsignedTinyInteger('stress_score')->default(0);
            $table->string('stress_level', 30)->default('Normal');

            $table->string('max_severity_level', 30)->default('Normal');

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Actualizamos el índice para búsquedas rápidas por estudiante
            $table->index(['codigo_anonimo', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dass21_evaluations');
    }
};
