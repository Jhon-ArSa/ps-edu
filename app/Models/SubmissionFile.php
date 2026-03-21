<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionFile extends Model
{
    protected $fillable = [
        'submission_id', 'file_path', 'original_filename',
        'file_size', 'mime_type', 'order',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    // ── Accessors ────────────────────────────────────────────────

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return number_format($bytes / 1024, 1) . ' KB';
        } else {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
    }

    public function getFileExtensionAttribute(): string
    {
        return pathinfo($this->original_filename, PATHINFO_EXTENSION);
    }
}