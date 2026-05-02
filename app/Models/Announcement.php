<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'author_id', 'target_role', 'published_at', 'image_path', 'is_popup'];

    // target_role: 'all' | 'docente' | 'alumno' | 'admin'

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        $fullPath = public_path($this->image_path);
        if (!file_exists($fullPath)) {
            return null;
        }

        return asset($this->image_path);
    }

    public function hasValidImage(): bool
    {
        return $this->image_path && file_exists(public_path($this->image_path));
    }

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_popup' => 'boolean'];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'announcement_programs');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'announcement_courses');
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

    public function scopePopup($query)
    {
        return $query->where('is_popup', true);
    }
}
