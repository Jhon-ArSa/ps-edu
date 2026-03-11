<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')
                  ->constrained('evaluation_attempts')
                  ->cascadeOnDelete();
            $table->foreignId('question_id')
                  ->constrained('evaluation_questions')
                  ->cascadeOnDelete();
            $table->json('selected_options')->nullable(); // array of option IDs
            $table->text('text_answer')->nullable();      // para tipo short
            $table->decimal('score', 4, 1)->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_answers');
    }
};
