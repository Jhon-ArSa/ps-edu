<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends Model
{
    use SoftDeletes;

    protected $fillable = ['topic_id', 'user_id', 'body', 'likes_count', 'is_best_answer', 'edited_at'];

    protected $casts = [
        'deleted_at' => 'datetime',
        'edited_at' => 'datetime',
        'is_best_answer' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Permissions ───────────────────────────────────────────────────────────

    public function canDelete(User $user): bool
    {
        if ($this->user_id === $user->id) return true;

        // Course teacher can delete any reply
        if ($user->role === 'docente') {
            return $this->topic->course->teacher_id === $user->id;
        }

        return false;
    }
}
