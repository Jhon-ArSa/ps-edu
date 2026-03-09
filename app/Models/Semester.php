<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Semester extends Model
{
    protected $fillable = [
        'name', 'year', 'period', 'start_date', 'end_date',
        'status', 'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'year'       => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeChronological($query)
    {
        return $query->orderBy('year')->orderByRaw("FIELD(period, 'I', 'II')");
    }

    public function scopeReverseChronological($query)
    {
        return $query->orderByDesc('year')->orderByRaw("FIELD(period, 'II', 'I')");
    }

    // ── Static helpers ───────────────────────────────────────────────────

    public static function getActive(): ?self
    {
        return Cache::remember('semester_active', 300, function () {
            return static::where('status', 'active')->first();
        });
    }

    public static function clearActiveCache(): void
    {
        Cache::forget('semester_active');
    }

    // ── Accessors ────────────────────────────────────────────────────────

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsClosedAttribute(): bool
    {
        return $this->status === 'closed';
    }

    public function getIsPlannedAttribute(): bool
    {
        return $this->status === 'planned';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'  => 'En curso',
            'closed'  => 'Finalizado',
            'planned' => 'Planificado',
            default   => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'  => 'emerald',
            'closed'  => 'gray',
            'planned' => 'blue',
            default   => 'gray',
        };
    }

    public function getDateRangeAttribute(): string
    {
        return $this->start_date->isoFormat('D MMM YYYY') . ' — ' . $this->end_date->isoFormat('D MMM YYYY');
    }

    public function getDurationWeeksAttribute(): int
    {
        return (int) $this->start_date->diffInWeeks($this->end_date);
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->is_closed) return 100;
        if ($this->is_planned) return 0;

        $total = $this->start_date->diffInDays($this->end_date);
        if ($total <= 0) return 0;

        $elapsed = $this->start_date->diffInDays(now());
        return min(100, max(0, round(($elapsed / $total) * 100, 1)));
    }

    /**
     * Calculate which "cycle number" this semester is within a 3-year (6-semester) program.
     * E.g., if the program started 2026-I: 2026-I=1, 2026-II=2, 2027-I=3, ... 2028-II=6
     */
    public function getCycleNumberInProgramAttribute(): int
    {
        $firstSemester = static::chronological()->first();
        if (!$firstSemester) return 1;

        $yearDiff = $this->year - $firstSemester->year;
        $periodOffset = $this->period === 'II' ? 1 : 0;
        $firstPeriodOffset = $firstSemester->period === 'II' ? 1 : 0;

        return ($yearDiff * 2) + $periodOffset - $firstPeriodOffset + 1;
    }
}
