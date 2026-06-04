<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ForumReport extends Model
{
    protected $fillable = [
        'user_id', 'reportable_type', 'reportable_id',
        'reason', 'description', 'status',
        'reviewed_by', 'admin_notes', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    // ── Helper Methods ────────────────────────────────────────────────────────

    public function markAsReviewed(int $reviewerId, string $notes = null): void
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_by' => $reviewerId,
            'admin_notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    public function markAsResolved(int $reviewerId, string $notes = null): void
    {
        $this->update([
            'status' => 'resolved',
            'reviewed_by' => $reviewerId,
            'admin_notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    public function dismiss(int $reviewerId, string $notes = null): void
    {
        $this->update([
            'status' => 'dismissed',
            'reviewed_by' => $reviewerId,
            'admin_notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }
}
