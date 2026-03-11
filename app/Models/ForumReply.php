<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumReply extends Model
{
    protected $fillable = ['topic_id', 'user_id', 'body'];

    protected $casts = ['deleted_at' => 'datetime'];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeVisible($query)
    {
        return $query->whereNull('deleted_at');
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

    // ── Soft delete manual ────────────────────────────────────────────────────

    public function softDelete(): void
    {
        $this->update(['deleted_at' => now()]);
        $this->topic->decrement('replies_count');
    }

    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }
}
