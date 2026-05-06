<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            $table->string('clave')->nullable()->after('nombre');
            $table->string('estado')->default('activo')->after('clave');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            $table->dropColumn(['clave', 'estado']);
            $table->dropSoftDeletes();
        });
    }
};
