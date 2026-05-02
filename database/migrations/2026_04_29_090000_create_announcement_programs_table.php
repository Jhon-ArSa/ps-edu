<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_programs', function (Blueprint $table) {
            $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->primary(['announcement_id', 'program_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_programs');
    }
};
