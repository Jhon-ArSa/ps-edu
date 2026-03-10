<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // null = sin nota registrada aún
            $table->decimal('score', 4, 1)->nullable();
            $table->text('comments')->nullable();

            // Quién registró / calificó
            $table->unsignedBigInteger('graded_by')->nullable();
            $table->foreign('graded_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();

            $table->timestamps();

            // Un alumno solo tiene una nota por ítem
            $table->unique(['grade_item_id', 'user_id']);

            $table->index(['user_id', 'grade_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
