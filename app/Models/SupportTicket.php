<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id', 'subject', 'category', 'message', 'status',
        'priority', 'assigned_to', 'admin_notes', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ── Accessors ────────────────────────────────────────────────

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'tecnico' => 'Técnico',
            'academico' => 'Académico',
            'acceso' => 'Acceso',
            'otro' => 'Otro',
            default => $this->category,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open' => 'Abierto',
            'in_progress' => 'En progreso',
            'resolved' => 'Resuelto',
            'closed' => 'Cerrado',
            default => $this->status,
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'low' => 'Baja',
            'medium' => 'Media',
            'high' => 'Alta',
            'urgent' => 'Urgente',
            default => $this->priority,
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'open' => ['label' => 'Abierto', 'class' => 'bg-red-100 text-red-700'],
            'in_progress' => ['label' => 'En progreso', 'class' => 'bg-amber-100 text-amber-700'],
            'resolved' => ['label' => 'Resuelto', 'class' => 'bg-green-100 text-green-700'],
            'closed' => ['label' => 'Cerrado', 'class' => 'bg-gray-100 text-gray-500'],
            default => ['label' => $this->status, 'class' => 'bg-gray-100 text-gray-500'],
        };
    }

    public function getPriorityBadgeAttribute(): array
    {
        return match($this->priority) {
            'low' => ['label' => 'Baja', 'class' => 'bg-gray-100 text-gray-600'],
            'medium' => ['label' => 'Media', 'class' => 'bg-blue-100 text-blue-700'],
            'high' => ['label' => 'Alta', 'class' => 'bg-orange-100 text-orange-700'],
            'urgent' => ['label' => 'Urgente', 'class' => 'bg-red-100 text-red-700'],
            default => ['label' => $this->priority, 'class' => 'bg-gray-100 text-gray-500'],
        };
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }
}