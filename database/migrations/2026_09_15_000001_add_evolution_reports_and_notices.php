<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->timestamp('contexto_registrado_at')->nullable();
            // Identificadores y etiquetas históricos: no se eliminan al cambiar el catálogo.
            foreach (['grupo', 'carrera', 'ciclo'] as $dimension) {
                $table->unsignedBigInteger($dimension.'_aplicacion_id')->nullable()->index();
                $table->string($dimension.'_aplicacion_nombre')->nullable();
            }
        });
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('evento', 120)->unique();
            $table->string('tipo', 30);
            $table->foreignId('caso_atencion_id')->nullable()->constrained('casos_atencion')->cascadeOnDelete();
            $table->foreignId('evaluacion_id')->nullable()->constrained('evaluaciones')->cascadeOnDelete();
            $table->timestamp('leido_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'leido_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avisos');
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->dropColumn('contexto_registrado_at');
            foreach (['grupo', 'carrera', 'ciclo'] as $dimension) {
                $table->dropIndex([$dimension.'_aplicacion_id']);
                $table->dropColumn([$dimension.'_aplicacion_id', $dimension.'_aplicacion_nombre']);
            }
        });
    }
};
