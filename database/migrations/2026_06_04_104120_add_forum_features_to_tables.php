<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nuevas características para forum_topics
        Schema::table('forum_topics', function (Blueprint $table) {
            $table->unsignedInteger('views_count')->default(0)->after('replies_count');
            $table->unsignedInteger('likes_count')->default(0)->after('views_count');
            $table->boolean('is_resolved')->default(false)->after('is_closed');
            $table->foreignId('best_reply_id')->nullable()->after('is_resolved')->constrained('forum_replies')->nullOnDelete();
            $table->timestamp('edited_at')->nullable()->after('last_reply_at');
            
            $table->index(['course_id', 'is_resolved']);
            $table->index(['course_id', 'views_count']);
        });

        // Nuevas características para forum_replies
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->unsignedInteger('likes_count')->default(0)->after('body');
            $table->boolean('is_best_answer')->default(false)->after('likes_count');
            $table->timestamp('edited_at')->nullable()->after('deleted_at');
        });

        // Tabla de likes (reacciones)
        Schema::create('forum_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('likeable'); // forum_topics o forum_replies
            $table->timestamps();

            $table->unique(['user_id', 'likeable_type', 'likeable_id']);
        });

        // Tabla de reportes
        Schema::create('forum_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // quien reporta
            $table->morphs('reportable'); // forum_topics o forum_replies
            $table->enum('reason', ['spam', 'inappropriate', 'offensive', 'other']);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_reports');
        Schema::dropIfExists('forum_likes');

        Schema::table('forum_replies', function (Blueprint $table) {
            $table->dropColumn(['likes_count', 'is_best_answer', 'edited_at']);
        });

        Schema::table('forum_topics', function (Blueprint $table) {
            $table->dropForeign(['best_reply_id']);
            $table->dropColumn(['views_count', 'likes_count', 'is_resolved', 'best_reply_id', 'edited_at']);
        });
    }
};
