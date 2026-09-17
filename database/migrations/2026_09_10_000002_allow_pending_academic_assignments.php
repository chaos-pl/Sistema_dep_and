<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->unsignedBigInteger('grupo_id')->nullable()->change();
        });
        Schema::table('grupos', function (Blueprint $table) {
            $table->unsignedBigInteger('tutor_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Comprobar ambas tablas antes de tocar ninguna; no inventar asignaciones.
        if (DB::table('estudiantes')->whereNull('grupo_id')->exists()
            || DB::table('grupos')->whereNull('tutor_id')->exists()) {
            throw new RuntimeException('No se puede revertir mientras existan estudiantes sin grupo o grupos sin tutor.');
        }
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->unsignedBigInteger('grupo_id')->nullable(false)->change();
        });
        Schema::table('grupos', function (Blueprint $table) {
            $table->unsignedBigInteger('tutor_id')->nullable(false)->change();
        });
    }
};
