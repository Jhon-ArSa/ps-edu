<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('week_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->unsignedSmallInteger('time_limit')->nullable(); // minutos; null = sin límite
            $table->dateTime('opens_at')->nullable();
            $table->dateTime('closes_at')->nullable();
            $table->decimal('max_score', 4, 1)->default(20.0);
            $table->unsignedTinyInteger('max_attempts')->default(1);
            $table->boolean('show_results')->default(false);
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamps();

            $table->index(['week_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
