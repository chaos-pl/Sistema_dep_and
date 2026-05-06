<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->foreignId('ciclo_escolar_id')->nullable()->after('tutor_id')
                  ->constrained('ciclos_escolares')->nullOnDelete();
            $table->string('estado')->default('activo')->after('periodo');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropForeign(['ciclo_escolar_id']);
            $table->dropColumn(['ciclo_escolar_id', 'estado']);
            $table->dropSoftDeletes();
        });
    }
};
