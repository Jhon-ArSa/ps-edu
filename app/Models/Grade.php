<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'grade_item_id', 'user_id', 'score', 'comments', 'graded_by', 'graded_at',
    ];

    protected $casts = [
        'graded_at' => 'datetime',
        'score'     => 'float',
    ];

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function gradeItem(): BelongsTo
    {
        return $this->belongsTo(GradeItem::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Retorna la clase CSS de color según la nota (escala vigesimal peruana).
     */
    public function getScoreColorClassAttribute(): string
    {
        if ($this->score === null) return 'text-gray-400';
        if ($this->score < 11)    return 'text-red-600 font-semibold';
        if ($this->score < 14)    return 'text-amber-600 font-semibold';
        if ($this->score < 18)    return 'text-emerald-600 font-semibold';
        return 'text-emerald-700 font-bold';
    }

    // ── Puntos de integración (Juan + Jhon los llaman desde sus controllers) ─
    //
    // JUAN — llámalo en Docente/SubmissionController cuando califica:
    //   Grade::recordFromSubmission($submission);
    //   (donde $submission está con score, task_id, user_id, graded_by, graded_at)
    //
    // JHON — llámalo en EvaluationAttempt cuando finaliza y tiene score:
    //   Grade::recordFromAttempt($attempt);
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * @param  object  $submission  instancia de App\Models\Submission
     */
    public static function recordFromSubmission($submission): ?self
    {
        if ($submission->score === null) return null;

        $gradeItem = GradeItem::where('type', GradeItem::TYPE_TASK)
            ->where('reference_id', $submission->task_id)
            ->first();

        if (! $gradeItem) return null;

        return static::updateOrCreate(
            ['grade_item_id' => $gradeItem->id, 'user_id' => $submission->user_id],
            [
                'score'      => $submission->score,
                'graded_by'  => $submission->graded_by,
                'graded_at'  => $submission->graded_at ?? now(),
                'comments'   => $submission->feedback,
            ]
        );
    }

    /**
     * @param  object  $attempt  instancia de App\Models\EvaluationAttempt
     */
    public static function recordFromAttempt($attempt): ?self
    {
        if ($attempt->score === null) return null;

        $gradeItem = GradeItem::where('type', GradeItem::TYPE_EVALUATION)
            ->where('reference_id', $attempt->evaluation_id)
            ->first();

        if (! $gradeItem) return null;

        return static::updateOrCreate(
            ['grade_item_id' => $gradeItem->id, 'user_id' => $attempt->user_id],
            [
                'score'     => $attempt->score,
                'graded_at' => $attempt->submitted_at ?? now(),
            ]
        );
    }
}
