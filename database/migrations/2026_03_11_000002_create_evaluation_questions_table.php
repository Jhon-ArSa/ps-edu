<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['multiple_one', 'multiple_many', 'true_false', 'short']);
            $table->text('text');
            $table->decimal('points', 4, 1)->default(1.0);
            $table->text('explanation')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();

            $table->index(['evaluation_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_questions');
    }
};
