<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->string('instrument', 10);
            $table->json('answers');
            $table->unsignedInteger('version')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamps();
            $table->unique(['estudiante_id', 'instrument']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_drafts');
    }
};
