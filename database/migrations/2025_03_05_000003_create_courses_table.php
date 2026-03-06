<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 30)->unique();
            $table->text('description')->nullable();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('restrict');
            $table->string('program')->nullable();
            $table->unsignedTinyInteger('cycle')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->enum('semester', ['I', 'II'])->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
