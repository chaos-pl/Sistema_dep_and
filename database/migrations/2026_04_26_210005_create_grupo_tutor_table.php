<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupo_tutor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained('tutores')->cascadeOnDelete();
            $table->foreignId('ciclo_escolar_id')->nullable()->constrained('ciclos_escolares')->nullOnDelete();
            $table->string('estado')->default('activo');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['grupo_id', 'tutor_id', 'ciclo_escolar_id'], 'grupo_tutor_ciclo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_tutor');
    }
};
