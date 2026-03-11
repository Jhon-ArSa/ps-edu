<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id', 'question_id', 'selected_options',
        'text_answer', 'score', 'is_correct',
    ];

    protected $casts = [
        'selected_options' => 'array',
        'score'            => 'float',
        'is_correct'       => 'boolean',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(EvaluationAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(EvaluationQuestion::class, 'question_id');
    }
}
