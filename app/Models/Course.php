<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name', 'code', 'description', 'teacher_id', 'semester_id',
        'program_id', 'program', 'cycle', 'year', 'semester', 'status',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function semesterPeriod()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function programBelongs()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function weeks()
    {
        return $this->hasMany(Week::class)->orderBy('number');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
                    ->withPivot('status', 'enrolled_at')
                    ->wherePivot('status', 'active');
    }

    public function gradeItems()
    {
        return $this->hasMany(GradeItem::class)->orderBy('order');
    }
}
