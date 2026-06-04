<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ForumTopic extends Model
{
    protected $fillable = [
        'course_id', 'user_id', 'title', 'body',
        'is_pinned', 'is_closed', 'is_resolved', 'best_reply_id',
        'replies_count', 'views_count', 'likes_count', 'last_reply_at', 'edited_at',
    ];

    protected $casts = [
        'is_pinned'     => 'boolean',
        'is_closed'     => 'boolean',
        'is_resolved'   => 'boolean',
        'last_reply_at' => 'datetime',
        'edited_at'     => 'datetime',
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
            ->orderBy('created_at');
    }

    public function bestReply(): BelongsTo
    {
        return $this->belongsTo(ForumReply::class, 'best_reply_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(ForumReport::class, 'reportable');
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

    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('body', 'like', "%{$search}%");
        });
    }

    // ── Permissions ───────────────────────────────────────────────────────────

    public function canReply(User $user): bool
    {
        if ($this->is_closed) return false;

        // Solo estudiantes matriculados pueden responder
        if ($user->role === 'alumno') {
            return $this->course->students()->where('users.id', $user->id)->exists();
        }

        return false;
    }

    public function canDelete(User $user): bool
    {
        // Solo el autor puede eliminar su tema
        return $this->user_id === $user->id;
    }

    public function canEdit(User $user): bool
    {
        // Solo el autor puede editar su tema
        return $this->user_id === $user->id;
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function hasBeenReportedBy(User $user): bool
    {
        return $this->reports()->where('user_id', $user->id)->exists();
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function toggleLike(User $user): bool
    {
        $like = $this->likes()->where('user_id', $user->id)->first();
        
        if ($like) {
            $like->delete();
            $this->decrement('likes_count');
            return false; // unliked
        } else {
            $this->likes()->create(['user_id' => $user->id]);
            $this->increment('likes_count');
            return true; // liked
        }
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function markAsResolved(int $bestReplyId = null): void
    {
        $this->update([
            'is_resolved' => true,
            'best_reply_id' => $bestReplyId,
        ]);

        if ($bestReplyId) {
            ForumReply::where('id', $bestReplyId)->update(['is_best_answer' => true]);
        }
    }

    public function unmarkAsResolved(): void
    {
        if ($this->best_reply_id) {
            ForumReply::where('id', $this->best_reply_id)->update(['is_best_answer' => false]);
        }

        $this->update([
            'is_resolved' => false,
            'best_reply_id' => null,
        ]);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): array
    {
        if ($this->is_resolved) {
            return ['label' => '✓ Resuelto', 'class' => 'bg-emerald-100 text-emerald-700'];
        }
        if ($this->is_closed) {
            return ['label' => 'Cerrado', 'class' => 'bg-red-100 text-red-700'];
        }
        if ($this->is_pinned) {
            return ['label' => 'Fijado', 'class' => 'bg-amber-100 text-amber-700'];
        }
        return ['label' => 'Activo', 'class' => 'bg-blue-100 text-blue-700'];
    }

    public function getLastActivityAttribute(): string
    {
        $date = $this->last_reply_at ?? $this->created_at;
        return $date?->diffForHumans() ?? '—';
    }

    public function getIsEditedAttribute(): bool
    {
        return $this->edited_at && $this->edited_at->gt($this->created_at);
    }
}
