<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Evaluation extends Model
{
    protected $fillable = [
        'week_id', 'title', 'instructions', 'file_path', 'time_limit',
        'opens_at', 'closes_at', 'max_score', 'max_attempts',
        'show_results', 'status',
    ];

    protected $casts = [
        'opens_at'     => 'datetime',
        'closes_at'    => 'datetime',
        'show_results' => 'boolean',
        'max_score'    => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(EvaluationQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(EvaluationAttempt::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function attemptsFor(int $userId)
    {
        return $this->attempts()->where('user_id', $userId)->orderBy('attempt_number')->get();
    }

    public function activeAttemptFor(int $userId): ?EvaluationAttempt
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('status', 'in_progress')
            ->latest()
            ->first();
    }

    public function isOpen(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }
        if ($this->opens_at && $this->opens_at->isFuture()) {
            return false;
        }
        if ($this->closes_at && $this->closes_at->isPast()) {
            return false;
        }
        return true;
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'published' => ['label' => 'Publicada', 'class' => 'bg-emerald-100 text-emerald-700'],
            'closed'    => ['label' => 'Cerrada',   'class' => 'bg-red-100 text-red-700'],
            default     => ['label' => 'Borrador',  'class' => 'bg-gray-100 text-gray-500'],
        };
    }

    public function getTimeLimitLabelAttribute(): string
    {
        if (!$this->time_limit) {
            return 'Sin límite';
        }
        return $this->time_limit . ' minutos';
    }

    public function getOpenWindowAttribute(): string
    {
        if (!$this->opens_at && !$this->closes_at) {
            return 'Siempre disponible';
        }
        $from = $this->opens_at?->format('d/m/Y H:i') ?? 'Ahora';
        $to   = $this->closes_at?->format('d/m/Y H:i') ?? 'Sin fecha límite';
        return "{$from} — {$to}";
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }
}
