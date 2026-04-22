@extends('layouts.auth')

@section('title', 'Recuperar Contraseña')

@section('form-header')
<div class="mb-8 text-center">
    <p class="font-mono-custom mb-2 uppercase tracking-widest" style="font-size:10px;color:#22d3ee;letter-spacing:0.25em;">Recuperación de Acceso</p>
    <h2 style="font-size:1.6rem;font-weight:800;color:#d8e2ff;letter-spacing:-0.01em;">Recuperar contraseña</h2>
    <p class="mt-1 text-sm" style="color:rgba(197,198,205,0.6);">Ingrese su correo y le enviaremos instrucciones para restablecer su contraseña.</p>
</div>
@endsection

@section('content')
<div>
    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-medium mb-6 transition-colors"
       style="color:rgba(197,198,205,0.6);"
       onmouseover="this.style.color='#22d3ee'"
       onmouseout="this.style.color='rgba(197,198,205,0.6)'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Volver al inicio de sesión
    </a>

    @if(session('status'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg px-4 py-3 text-sm mb-5">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="auth-label">Correo electrónico</label>
            <div class="auth-input-wrap relative">
                <div class="auth-icon absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    autofocus
                    class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                    style="padding-left:2.75rem;"
                    placeholder="correo@institución.edu.pe"
                >
            </div>
            @error('email')
                <p class="flex items-center gap-1.5 mt-1.5 text-xs" style="color:rgba(252,165,165,0.9);">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button type="submit"
                class="scan-btn relative w-full py-3 text-sm font-semibold rounded-xl transition-all duration-300"
                style="background:linear-gradient(135deg,#0284c7,#0e7490);color:#fff;border:1px solid rgba(34,211,238,0.3);box-shadow:0 4px 20px rgba(34,211,238,0.18);"
                onmouseover="this.style.boxShadow='0 6px 28px rgba(34,211,238,0.32)';this.style.borderColor='rgba(34,211,238,0.55)';"
                onmouseout="this.style.boxShadow='0 4px 20px rgba(34,211,238,0.18)';this.style.borderColor='rgba(34,211,238,0.3)';">
            <span class="scan-line"></span>
            <span class="relative z-10">Enviar enlace de recuperación</span>
        </button>
    </form>
</div>
@endsection
