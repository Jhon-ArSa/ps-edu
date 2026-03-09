<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);                         // "2026-I"
            $table->unsignedSmallInteger('year');
            $table->enum('period', ['I', 'II']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'closed', 'planned'])->default('planned');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['year', 'period']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
