<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclos_escolares', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // e.g. "2025-A", "2025-B"
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado')->default('activo'); // activo, inactivo, cerrado
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclos_escolares');
    }
};
