<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Task extends Model
{
    protected $fillable = [
        'week_id', 'title', 'description', 'instructions',
        'due_date', 'max_score', 'file_path', 'status',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function week()
    {
        return $this->belongsTo(Week::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function isExpired(): bool
    {
        return $this->due_date !== null && $this->due_date->isPast();
    }

    public function getDueDateBadgeAttribute(): array
    {
        if (!$this->due_date) return ['label' => 'Sin fecha límite', 'class' => 'bg-gray-100 text-gray-500'];
        if ($this->isExpired()) return ['label' => 'Vencida ' . $this->due_date->format('d/m/Y'), 'class' => 'bg-red-100 text-red-700'];
        if ($this->due_date->isToday()) return ['label' => 'Hoy ' . $this->due_date->format('H:i'), 'class' => 'bg-orange-100 text-orange-700'];
        if ($this->due_date->diffInDays() <= 3) return ['label' => 'Vence ' . $this->due_date->format('d/m/Y'), 'class' => 'bg-amber-100 text-amber-700'];
        return ['label' => 'Hasta ' . $this->due_date->format('d/m/Y'), 'class' => 'bg-blue-100 text-blue-700'];
    }
}
