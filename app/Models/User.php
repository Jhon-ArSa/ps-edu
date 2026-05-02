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
        'failed_login_attempts', 'locked_until', 'last_failed_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password'   => 'hashed',
            'status'     => 'boolean',
            'locked_until' => 'datetime',
            'last_failed_login_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isDocente(): bool { return $this->role === 'docente'; }
    public function isAlumno(): bool  { return $this->role === 'alumno'; }

    /**
     * Verificar si la cuenta está bloqueada
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Bloquear la cuenta por X minutos
     */
    public function lockAccount(int $minutes = 30): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);

        // Log de bloqueo de cuenta
        \Log::channel('security')->warning('Cuenta bloqueada por intentos fallidos', [
            'user_id' => $this->id,
            'email' => $this->email,
            'locked_until' => $this->locked_until->toDateTimeString(),
            'ip' => request()->ip(),
        ]);
    }

    /**
     * Desbloquear la cuenta
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
            'last_failed_login_at' => null,
        ]);

        // Log de desbloqueo de cuenta
        \Log::channel('security')->info('Cuenta desbloqueada', [
            'user_id' => $this->id,
            'email' => $this->email,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * Incrementar intentos fallidos de login
     */
    public function incrementFailedLoginAttempts(): void
    {
        $this->increment('failed_login_attempts');
        $this->update(['last_failed_login_at' => now()]);

        // Bloquear cuenta después de 10 intentos fallidos
        if ($this->failed_login_attempts >= 10) {
            $this->lockAccount(30); // Bloquear por 30 minutos
        }
    }

    /**
     * Resetear intentos fallidos de login
     */
    public function resetFailedLoginAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'last_failed_login_at' => null,
        ]);
    }

    public function docenteProfile()  { return $this->hasOne(DocenteProfile::class); }
    public function alumnoProfile()   { return $this->hasOne(AlumnoProfile::class); }
    public function coursesTaught()   { return $this->hasMany(Course::class, 'teacher_id'); }
    public function enrollments()     { return $this->hasMany(Enrollment::class); }
    public function announcements()   { return $this->hasMany(Announcement::class, 'author_id'); }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset($this->avatar)
            : asset('images/default-avatar.png');
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token, $this->email));
    }
}
