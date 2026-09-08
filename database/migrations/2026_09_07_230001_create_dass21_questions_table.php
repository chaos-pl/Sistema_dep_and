<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dass21_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instrument_id')
                  ->constrained('instrumentos')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('item_number');
            $table->text('statement');
            $table->string('dimension', 20); // depression, anxiety, stress
            $table->timestamps();

            $table->unique(['instrument_id', 'item_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dass21_questions');
    }
};
