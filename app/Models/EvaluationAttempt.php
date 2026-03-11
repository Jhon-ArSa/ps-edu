<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class EvaluationAttempt extends Model
{
    protected $fillable = [
        'evaluation_id', 'user_id', 'started_at', 'submitted_at',
        'score', 'status', 'attempt_number', 'file_path', 'original_filename',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'score'        => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class, 'attempt_id');
    }

    // ── State checks ──────────────────────────────────────────────────────────

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'graded']);
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function isTimerExpired(): bool
    {
        if (!$this->evaluation->time_limit || !$this->started_at) {
            return false;
        }
        return $this->started_at->addMinutes($this->evaluation->time_limit)->isPast();
    }

    public function hasUngradedShortAnswers(): bool
    {
        return $this->answers()
            ->whereHas('question', fn ($q) => $q->where('type', 'short'))
            ->whereNull('score')
            ->exists();
    }

    public function getRemainingSeconds(): int
    {
        if (!$this->evaluation->time_limit || !$this->started_at) {
            return 0;
        }
        $expiresAt = $this->started_at->addMinutes($this->evaluation->time_limit);
        return max(0, (int) now()->diffInSeconds($expiresAt, false));
    }

    // ── Auto-grading ──────────────────────────────────────────────────────────

    /**
     * Calificar automáticamente las preguntas que lo permiten y
     * actualizar estado del intento.
     */
    public static function autoGrade(self $attempt): void
    {
        $attempt->load(['answers.question.options', 'evaluation.questions']);

        $totalScore = 0.0;

        foreach ($attempt->answers as $answer) {
            $question = $answer->question;

            if (!$question->isAutoGraded()) {
                continue;
            }

            $selectedIds = $answer->selected_options ?? [];
            $score       = $question->autoScore($selectedIds);
            $isCorrect   = $score >= $question->points;

            $answer->update([
                'score'      => $score,
                'is_correct' => $isCorrect,
            ]);

            $totalScore += $score;
        }

        // Sumar puntajes de respuestas cortas ya calificadas
        $totalScore += $attempt->answers()
            ->whereHas('question', fn ($q) => $q->where('type', 'short'))
            ->whereNotNull('score')
            ->sum('score');

        $hasShort  = $attempt->evaluation->questions->where('type', 'short')->isNotEmpty();
        $newStatus = $hasShort ? 'submitted' : 'graded';

        // Normalizar score a escala vigesimal (max_score de la evaluación)
        $totalPoints = $attempt->evaluation->questions->sum('points');
        $normalScore = $totalPoints > 0
            ? round(($totalScore / $totalPoints) * $attempt->evaluation->max_score, 1)
            : 0.0;

        $attempt->update([
            'score'        => $normalScore,
            'status'       => $newStatus,
            'submitted_at' => $attempt->submitted_at ?? now(),
        ]);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'in_progress' => ['label' => 'En progreso', 'class' => 'bg-amber-100 text-amber-700'],
            'submitted'   => ['label' => 'Entregada',   'class' => 'bg-blue-100 text-blue-700'],
            'graded'      => ['label' => 'Calificada',  'class' => 'bg-emerald-100 text-emerald-700'],
            default       => ['label' => $this->status, 'class' => 'bg-gray-100 text-gray-500'],
        };
    }

    public function getScoreColorClassAttribute(): string
    {
        if ($this->score === null) return 'text-gray-400';
        if ($this->score < 11)    return 'text-red-600 font-semibold';
        if ($this->score < 14)    return 'text-amber-600 font-semibold';
        return 'text-emerald-600 font-semibold';
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }
}
