<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;

class LogSecurityEvents
{
    /**
     * Registrar login exitoso
     */
    public function handleLogin(Login $event): void
    {
        Log::channel('security')->info('Login exitoso', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'role' => $event->user->role,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Registrar logout
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            Log::channel('security')->info('Logout', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
                'timestamp' => now()->toDateTimeString(),
            ]);
        }
    }

    /**
     * Registrar intento de login fallido
     */
    public function handleFailed(Failed $event): void
    {
        Log::channel('security')->warning('Intento de login fallido', [
            'email' => $event->credentials['email'] ?? 'desconocido',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Registrar bloqueo de cuenta
     */
    public function handleLockout(Lockout $event): void
    {
        Log::channel('security')->error('Cuenta bloqueada por intentos fallidos', [
            'email' => $event->request->input('email'),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Registrar recuperación de contraseña
     */
    public function handlePasswordReset(PasswordReset $event): void
    {
        Log::channel('security')->info('Contraseña restablecida', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Registrar los eventos
     */
    public function subscribe($events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
            Lockout::class => 'handleLockout',
            PasswordReset::class => 'handlePasswordReset',
        ];
    }
}
