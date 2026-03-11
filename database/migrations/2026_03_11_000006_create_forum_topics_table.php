<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->unsignedInteger('replies_count')->default(0);
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'is_pinned', 'created_at']);
            $table->index(['course_id', 'last_reply_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_topics');
    }
};
