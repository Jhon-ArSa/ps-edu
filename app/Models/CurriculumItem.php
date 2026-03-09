<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumItem extends Model
{
    protected $fillable = [
        'program_id', 'mention_id', 'semester_number',
        'course_name', 'credits', 'is_elective', 'order',
    ];

    protected $casts = [
        'semester_number' => 'integer',
        'credits'         => 'integer',
        'is_elective'     => 'boolean',
        'order'           => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function mention(): BelongsTo
    {
        return $this->belongsTo(Mention::class);
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getSemesterLabelAttribute(): string
    {
        if ($this->semester_number === 0) {
            return 'Propedéutico';
        }
        return 'Semestre ' . $this->semester_number;
    }
}
