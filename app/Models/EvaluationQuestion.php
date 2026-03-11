<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationQuestion extends Model
{
    protected $fillable = [
        'evaluation_id', 'type', 'text', 'points', 'explanation', 'order',
    ];

    protected $casts = [
        'points' => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(EvaluationOption::class, 'question_id')->orderBy('order');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isAutoGraded(): bool
    {
        return in_array($this->type, ['multiple_one', 'multiple_many', 'true_false']);
    }

    /**
     * Calcular puntaje automático dado los IDs de opciones seleccionadas.
     * @param  array<int> $selectedOptionIds
     */
    public function autoScore(array $selectedOptionIds): float
    {
        if (!$this->isAutoGraded()) {
            return 0.0;
        }

        $correctIds = $this->options->where('is_correct', true)->pluck('id')->toArray();

        if ($this->type === 'multiple_one' || $this->type === 'true_false') {
            // Exactamente una opción correcta
            return count(array_intersect($selectedOptionIds, $correctIds)) === 1
                ? (float) $this->points
                : 0.0;
        }

        if ($this->type === 'multiple_many') {
            // Puntuación parcial: proporción de correctas seleccionadas menos incorrectas
            $totalCorrect  = count($correctIds);
            if ($totalCorrect === 0) return 0.0;

            $correctHits   = count(array_intersect($selectedOptionIds, $correctIds));
            $incorrectHits = count(array_diff($selectedOptionIds, $correctIds));
            $ratio         = max(0, ($correctHits - $incorrectHits) / $totalCorrect);
            return round($ratio * $this->points, 1);
        }

        return 0.0;
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'multiple_one'  => 'Opción múltiple (una)',
            'multiple_many' => 'Opción múltiple (varias)',
            'true_false'    => 'Verdadero / Falso',
            'short'         => 'Respuesta corta',
            default         => $this->type,
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'multiple_one'  => 'bg-blue-100 text-blue-700',
            'multiple_many' => 'bg-indigo-100 text-indigo-700',
            'true_false'    => 'bg-amber-100 text-amber-700',
            'short'         => 'bg-violet-100 text-violet-700',
            default         => 'bg-gray-100 text-gray-600',
        };
    }
}
