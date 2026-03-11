<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumTopic extends Model
{
    protected $fillable = [
        'course_id', 'user_id', 'title', 'body',
        'is_pinned', 'is_closed', 'replies_count', 'last_reply_at',
    ];

    protected $casts = [
        'is_pinned'     => 'boolean',
        'is_closed'     => 'boolean',
        'last_reply_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'topic_id')
            ->whereNull('deleted_at')
            ->orderBy('created_at');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_closed', false);
    }

    // ── Permissions ───────────────────────────────────────────────────────────

    public function canReply(User $user): bool
    {
        if ($this->is_closed) return false;

        // Teacher of the course
        if ($user->role === 'docente' && $this->course->teacher_id === $user->id) {
            return true;
        }

        // Enrolled active student
        if ($user->role === 'alumno') {
            return $this->course->students()->where('users.id', $user->id)->exists();
        }

        return false;
    }

    public function canDelete(User $user): bool
    {
        // Author or course teacher
        return $this->user_id === $user->id
            || ($user->role === 'docente' && $this->course->teacher_id === $user->id);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): array
    {
        if ($this->is_closed) {
            return ['label' => 'Cerrado', 'class' => 'bg-red-100 text-red-700'];
        }
        if ($this->is_pinned) {
            return ['label' => 'Fijado', 'class' => 'bg-amber-100 text-amber-700'];
        }
        return ['label' => 'Activo', 'class' => 'bg-emerald-100 text-emerald-700'];
    }

    public function getLastActivityAttribute(): string
    {
        $date = $this->last_reply_at ?? $this->created_at;
        return $date?->diffForHumans() ?? '—';
    }
}
