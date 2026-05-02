<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_courses', function (Blueprint $table) {
            $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->primary(['announcement_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_courses');
    }
};
