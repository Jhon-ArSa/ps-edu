<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeItem extends Model
{
    protected $fillable = [
        'course_id', 'name', 'type', 'reference_id', 'weight', 'max_score', 'order',
    ];

    // ── Tipos ────────────────────────────────────────────────────────────────

    const TYPE_TASK          = 'task';
    const TYPE_EVALUATION    = 'evaluation';
    const TYPE_PARTICIPATION = 'participation';
    const TYPE_ORAL          = 'oral';
    const TYPE_FINAL         = 'final';
    const TYPE_OTHER         = 'other';

    /** Tipos que el docente edita manualmente en la libreta */
    const MANUAL_TYPES = ['participation', 'oral', 'final', 'other'];

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isManual(): bool
    {
        return in_array($this->type, self::MANUAL_TYPES);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'task'          => 'Tarea',
            'evaluation'    => 'Evaluación',
            'participation' => 'Participación',
            'oral'          => 'Oral',
            'final'         => 'Final',
            default         => 'Otro',
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'task'          => 'bg-blue-100 text-blue-700',
            'evaluation'    => 'bg-violet-100 text-violet-700',
            'participation' => 'bg-emerald-100 text-emerald-700',
            'oral'          => 'bg-amber-100 text-amber-700',
            'final'         => 'bg-red-100 text-red-700',
            default         => 'bg-gray-100 text-gray-600',
        };
    }

    // ── Puntos de integración (Juan + Jhon los llaman desde sus controllers) ─
    //
    // JUAN — llámalo en Docente/TaskController@store (después de Task::create):
    //   GradeItem::syncFromTask($task);
    //
    // JHON — llámalo en Docente/EvaluationController cuando activa la evaluación:
    //   GradeItem::syncFromEvaluation($evaluation);
    // ─────────────────────────────────────────────────────────────────────────

    public static function syncFromTask(Task $task): self
    {
        $courseId = $task->week->course_id;

        return static::updateOrCreate(
            [
                'course_id'    => $courseId,
                'type'         => self::TYPE_TASK,
                'reference_id' => $task->id,
            ],
            [
                'name'      => $task->title,
                'max_score' => $task->max_score ?? 20.0,
                'weight'    => 0,
                'order'     => static::where('course_id', $courseId)->max('order') + 1,
            ]
        );
    }

    /**
     * Jhon: llamar cuando crea/activa una evaluación.
     * $evaluation debe tener cargada la relación $evaluation->week->course_id
     * y las propiedades: id, title, y opcionalmente max_score.
     *
     * @param  object  $evaluation  instancia de App\Models\Evaluation
     */
    public static function syncFromEvaluation($evaluation): self
    {
        $courseId = $evaluation->week->course_id;

        return static::updateOrCreate(
            [
                'course_id'    => $courseId,
                'type'         => self::TYPE_EVALUATION,
                'reference_id' => $evaluation->id,
            ],
            [
                'name'      => $evaluation->title,
                'max_score' => $evaluation->max_score ?? 20.0,
                'weight'    => 0,
                'order'     => static::where('course_id', $courseId)->max('order') + 1,
            ]
        );
    }
}
