<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // Nombre que aparece en la cabecera de la libreta
            $table->string('name');

            // task/evaluation = auto-generado por Juan/Jhon
            // participation/oral/final/other = ingresado manualmente por el docente
            $table->enum('type', ['task', 'evaluation', 'participation', 'oral', 'final', 'other'])
                  ->default('other');

            // Para ítems automáticos: id del Task o Evaluation que lo originó
            $table->unsignedBigInteger('reference_id')->nullable();

            // Peso para el promedio ponderado (0 = sin peso, se usa promedio simple)
            $table->decimal('weight', 5, 2)->default(0);

            // Escala máxima (generalmente 20 en el sistema peruano)
            $table->decimal('max_score', 5, 1)->default(20.0);

            // Posición en la tabla (0-based)
            $table->unsignedTinyInteger('order')->default(0);

            $table->timestamps();

            // Un Task/Evaluation no puede tener dos ítems en el mismo curso
            $table->unique(['course_id', 'type', 'reference_id']);

            $table->index(['course_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_items');
    }
};
