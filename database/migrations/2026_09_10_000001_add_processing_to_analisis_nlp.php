<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analisis_nlp', function (Blueprint $table) {
            $table->string('estado_analisis', 20)->default('legacy')->index();
            $table->uuid('solicitud_id')->nullable();
            $table->timestamp('solicitado_at')->nullable();
            $table->timestamp('procesado_at')->nullable();
            $table->string('error_codigo', 40)->nullable();
            $table->decimal('probabilidad_beto', 5, 4)->nullable();
            $table->string('etiqueta_hibrida', 50)->nullable();
            $table->decimal('confianza_hibrida', 5, 4)->nullable();
        });
        // No es posible reconstruir la pareja etiqueta/confianza de los históricos.
        DB::table('analisis_nlp')->where('etiqueta_roberta', 'pendiente')->update(['estado_analisis' => 'fallido']);
    }

    public function down(): void
    {
        Schema::table('analisis_nlp', function (Blueprint $table) {
            $table->dropIndex(['estado_analisis']);
            $table->dropColumn(['estado_analisis', 'solicitud_id', 'solicitado_at', 'procesado_at', 'error_codigo', 'probabilidad_beto', 'etiqueta_hibrida', 'confianza_hibrida']);
        });
    }
};
