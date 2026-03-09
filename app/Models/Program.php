<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Program extends Model
{
    protected $fillable = [
        'name', 'code', 'degree_type', 'description',
        'duration_semesters', 'has_propedeutic', 'total_credits',
        'resolution', 'coordinator_id', 'status',
    ];

    protected $casts = [
        'duration_semesters' => 'integer',
        'total_credits'      => 'integer',
        'has_propedeutic'    => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function mentions(): HasMany
    {
        return $this->hasMany(Mention::class)->orderBy('order')->orderBy('name');
    }

    public function curriculumItems(): HasMany
    {
        return $this->hasMany(CurriculumItem::class)->orderBy('semester_number')->orderBy('order');
    }

    public function enrollments(): HasManyThrough
    {
        return $this->hasManyThrough(Enrollment::class, Course::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // ── Accessors ────────────────────────────────────────────────────────

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getDegreeTypeLabelAttribute(): string
    {
        return match ($this->degree_type) {
            'maestria'              => 'Maestría',
            'doctorado'             => 'Doctorado',
            'segunda_especialidad'  => 'Segunda Especialidad',
            'diplomado'             => 'Diplomado',
            default                 => ucfirst($this->degree_type),
        };
    }

    public function getDurationYearsAttribute(): string
    {
        $years = $this->duration_semesters / 2;
        if ($years == (int) $years) {
            return (int) $years . ' ' . ((int) $years === 1 ? 'año' : 'años');
        }
        return $years . ' años';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
            default    => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'emerald',
            'inactive' => 'gray',
            default    => 'gray',
        };
    }
}
