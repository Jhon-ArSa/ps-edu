<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Maestría en Didáctica de la Educación Superior
            $table->string('code', 20)->unique();            // MDES
            $table->string('degree_type', 50);               // maestria, doctorado, segunda_especialidad, diplomado
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration_semesters')->default(6); // 6 = 3 años
            $table->unsignedSmallInteger('total_credits')->nullable();
            $table->string('resolution')->nullable();        // Resolución de creación (SUNEDU/universidad)
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
