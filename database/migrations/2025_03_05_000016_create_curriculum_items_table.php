<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mention_id')->nullable()->constrained()->cascadeOnDelete();
            $table->smallInteger('semester_number');          // 0 = propedéutico, 1-6 = semestres normales
            $table->string('course_name');
            $table->unsignedSmallInteger('credits')->nullable();
            $table->boolean('is_elective')->default(false);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_items');
    }
};
