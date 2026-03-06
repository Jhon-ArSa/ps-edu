<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'dni', 'phone', 'avatar', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status'   => 'boolean',
        ];
    }

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isDocente(): bool { return $this->role === 'docente'; }
    public function isAlumno(): bool  { return $this->role === 'alumno'; }

    public function docenteProfile()  { return $this->hasOne(DocenteProfile::class); }
    public function alumnoProfile()   { return $this->hasOne(AlumnoProfile::class); }
    public function coursesTaught()   { return $this->hasMany(Course::class, 'teacher_id'); }
    public function enrollments()     { return $this->hasMany(Enrollment::class); }
    public function announcements()   { return $this->hasMany(Announcement::class, 'author_id'); }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('images/default-avatar.png');
    }
}
