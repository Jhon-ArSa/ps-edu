<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    protected $fillable = ['course_id', 'number', 'title', 'description'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class)->orderBy('order');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('created_at');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class)->orderBy('created_at');
    }
}
