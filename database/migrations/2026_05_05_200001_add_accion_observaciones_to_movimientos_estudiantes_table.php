<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos_estudiantes', function (Blueprint $table) {
            $table->string('accion', 50)->default('cambiado')->after('grupo_destino_id');
            $table->text('observaciones')->nullable()->after('motivo');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_estudiantes', function (Blueprint $table) {
            $table->dropColumn(['accion', 'observaciones']);
        });
    }
};
