<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mention extends Model
{
    protected $fillable = [
        'program_id', 'name', 'description', 'order', 'status',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function curriculumItems(): HasMany
    {
        return $this->hasMany(CurriculumItem::class)->orderBy('semester_number')->orderBy('order');
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}
