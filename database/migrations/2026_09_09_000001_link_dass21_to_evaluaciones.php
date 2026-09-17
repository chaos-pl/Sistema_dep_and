<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dass21_evaluations', function (Blueprint $table) {
            // Nullable durante la conversión de resultados anteriores.
            $table->foreignId('evaluacion_id')->nullable()->unique()
                ->constrained('evaluaciones')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dass21_evaluations', function (Blueprint $table) {
            $table->dropForeign(['evaluacion_id']);
            $table->dropUnique(['evaluacion_id']);
            $table->dropColumn('evaluacion_id');
        });
    }
};
