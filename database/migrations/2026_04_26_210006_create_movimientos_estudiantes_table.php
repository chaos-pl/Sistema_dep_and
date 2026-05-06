<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->foreignId('grupo_origen_id')->nullable()->constrained('grupos')->nullOnDelete();
            $table->foreignId('grupo_destino_id')->nullable()->constrained('grupos')->nullOnDelete();
            $table->text('motivo');
            $table->foreignId('realizado_por')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_estudiantes');
    }
};
