<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'author_id', 'target_role', 'published_at', 'image_path'];

    // target_role: 'all' | 'docente' | 'alumno' | 'admin'

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isPublished(): bool
    {
        return $this->published_at && $this->published_at->isPast();
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('target_role', 'all')->orWhere('target_role', $role);
        });
    }
}
