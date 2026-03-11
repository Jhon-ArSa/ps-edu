<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'task_id', 'user_id', 'file_path', 'original_filename',
        'comments', 'submitted_at', 'status', 'score',
        'feedback', 'graded_at', 'graded_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at'    => 'datetime',
        'score'        => 'decimal:1',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // ── State checks ─────────────────────────────────────────────

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function canEdit(): bool
    {
        return !$this->isGraded() && !$this->task->isExpired();
    }

    // ── Accessors ────────────────────────────────────────────────

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Pendiente',
            'submitted' => 'Entregada',
            'graded'    => 'Calificada',
            default     => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'submitted' => ['label' => 'Entregada', 'class' => 'bg-blue-100 text-blue-700'],
            'graded'    => ['label' => 'Calificada', 'class' => 'bg-emerald-100 text-emerald-700'],
            default     => ['label' => 'Pendiente', 'class' => 'bg-gray-100 text-gray-500'],
        };
    }

    public function getScoreColorAttribute(): string
    {
        if ($this->score === null || $this->task->max_score == 0) return 'text-gray-600';
        $pct = ($this->score / $this->task->max_score) * 100;
        if ($pct >= 70) return 'text-emerald-600';
        if ($pct >= 50) return 'text-amber-600';
        return 'text-red-600';
    }
}
