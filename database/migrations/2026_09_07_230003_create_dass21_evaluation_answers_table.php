<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dass21_evaluation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')
                  ->constrained('dass21_evaluations')
                  ->cascadeOnDelete();
            $table->foreignId('question_id')
                  ->constrained('dass21_questions')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('score'); // 0-3
            $table->timestamps();

            $table->unique(['evaluation_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dass21_evaluation_answers');
    }
};
