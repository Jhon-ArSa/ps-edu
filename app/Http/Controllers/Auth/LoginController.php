<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Buscar el usuario por email
        $user = \App\Models\User::where('email', $request->email)->first();

        // Verificar si la cuenta está bloqueada
        if ($user && $user->isLocked()) {
            $minutes = now()->diffInMinutes($user->locked_until);
            return back()->withErrors([
                'email' => "Su cuenta está bloqueada temporalmente por múltiples intentos fallidos. Intente nuevamente en {$minutes} minuto(s).",
            ])->onlyInput('email');
        }

        // Rate limiting: 5 intentos por 5 minutos por IP
        $key = 'login_attempts:' . $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 5;

        if (cache()->has($key) && cache()->get($key) >= $maxAttempts) {
            $seconds = cache()->get($key . ':timer') - time();
            return back()->withErrors([
                'email' => "Demasiados intentos de inicio de sesión. Por favor, intente nuevamente en {$seconds} segundos.",
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->status) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Su cuenta está desactivada. Contacte al administrador.',
                ])->onlyInput('email');
            }

            // Limpiar intentos fallidos al login exitoso
            cache()->forget($key);
            cache()->forget($key . ':timer');
            $user->resetFailedLoginAttempts();

            // Log de login exitoso
            \Log::channel('security')->info('Login exitoso', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $request->session()->regenerate();
            return $this->redirectByRole($user->role);
        }

        // Incrementar contador de intentos fallidos por IP
        $attempts = cache()->get($key, 0) + 1;
        cache()->put($key, $attempts, now()->addMinutes($decayMinutes));
        
        if ($attempts === 1) {
            cache()->put($key . ':timer', time() + ($decayMinutes * 60), now()->addMinutes($decayMinutes));
        }

        // Incrementar intentos fallidos en la cuenta del usuario
        if ($user) {
            $user->incrementFailedLoginAttempts();
        }

        // Log de login fallido
        \Log::channel('security')->warning('Intento de login fallido', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'attempts' => $attempts,
            'user_agent' => $request->userAgent(),
        ]);

        $remainingAttempts = $maxAttempts - $attempts;
        $message = $remainingAttempts > 0
            ? "Las credenciales proporcionadas no son correctas. Le quedan {$remainingAttempts} intento(s)."
            : "Demasiados intentos fallidos. Su acceso ha sido bloqueado temporalmente.";

        return back()->withErrors([
            'email' => $message,
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(string $role)
    {
        return match($role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'docente' => redirect()->route('docente.dashboard'),
            'alumno'  => redirect()->route('alumno.dashboard'),
            default   => redirect()->route('login'),
        };
    }
}
