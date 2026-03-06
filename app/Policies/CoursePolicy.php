<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function manage(User $user, Course $course): bool
    {
        return $user->isAdmin() || $course->teacher_id === $user->id;
    }

    public function view(User $user, Course $course): bool
    {
        if ($user->isAdmin())   return true;
        if ($user->isDocente()) return $course->teacher_id === $user->id;
        if ($user->isAlumno())  return $course->students()->where('users.id', $user->id)->exists();
        return false;
    }
}
