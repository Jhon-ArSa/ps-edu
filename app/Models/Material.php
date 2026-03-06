<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'week_id', 'type', 'title', 'description',
        'file_path', 'url', 'order',
    ];

    public function week()
    {
        return $this->belongsTo(Week::class);
    }

    public function getDownloadUrlAttribute(): ?string
    {
        return $this->type === 'file' && $this->file_path
            ? asset('storage/' . $this->file_path)
            : $this->url;
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->url) return null;

        // YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $this->url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return $this->url;
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'file'  => '📄',
            'video' => '🎬',
            'link'  => '🔗',
            default => '📎',
        };
    }
}
