<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');             // notifiable_type + notifiable_id
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Índice crítico: se consulta en CADA page load para el badge del header
            $table->index(['notifiable_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
