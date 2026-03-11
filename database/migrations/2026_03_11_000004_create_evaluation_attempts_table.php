<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('score', 4, 1)->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->timestamps();

            $table->unique(['evaluation_id', 'user_id', 'attempt_number']);
            $table->index(['user_id', 'status']);
            $table->index(['evaluation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_attempts');
    }
};
