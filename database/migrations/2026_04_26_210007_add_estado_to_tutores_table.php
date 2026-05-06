<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tutores', function (Blueprint $table) {
            $table->string('estado')->default('activo')->after('numero_empleado');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('tutores', function (Blueprint $table) {
            $table->dropColumn('estado');
            $table->dropSoftDeletes();
        });
    }
};
