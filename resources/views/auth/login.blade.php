@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('form-header')
<div class="mb-8 text-center">
    <p class="font-mono-custom mb-2 uppercase tracking-widest" style="font-size:10px;color:#22d3ee;letter-spacing:0.25em;">Portal de Acceso</p>
    <h2 style="font-size:1.6rem;font-weight:800;color:#d8e2ff;letter-spacing:-0.01em;">Iniciar Sesión</h2>
    <p class="mt-1 text-sm" style="color:rgba(197,198,205,0.6);">Ingresa tus credenciales institucionales</p>
</div>
@endsection

@section('content')
<div>
    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5"
        x-data="{ submitting: false }"
        @submit="if (submitting) { $event.preventDefault(); return; } submitting = true">
        @csrf

        {{-- Mensaje de sesión expirada --}}
        @if(session('error'))
            <div class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm"
                 style="background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);color:#fcd34d;">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Mensaje de contraseña restablecida --}}
        @if(session('status'))
            <div class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm"
                 style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);color:#6ee7b7;">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        {{-- Email --}}
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
                    autocomplete="email"
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

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="auth-label" style="margin-bottom:0;">Contraseña</label>
                <a href="{{ route('password.request') }}"
                   class="text-xs font-medium transition-colors"
                   style="color:rgba(34,211,238,0.7);"
                   onmouseover="this.style.color='#22d3ee'"
                   onmouseout="this.style.color='rgba(34,211,238,0.7)'">
                    ¿Olvidó su contraseña?
                </a>
            </div>
            <div x-data="{ show: false }" class="auth-input-wrap relative">
                <div class="auth-icon absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                    style="padding-left:2.75rem;padding-right:3rem;"
                    placeholder="••••••••"
                >
                <button type="button" @click="show = !show"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 transition-colors p-0.5"
                        style="color:rgba(197,198,205,0.4);"
                        onmouseover="this.style.color='rgba(34,211,238,0.7)'"
                        onmouseout="this.style.color='rgba(197,198,205,0.4)'">
                    <svg x-show="!show" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="flex items-center gap-1.5 mt-1.5 text-xs" style="color:rgba(252,165,165,0.9);">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Remember --}}
        <div class="flex items-center gap-2.5">
            <input id="remember" type="checkbox" name="remember"
                   class="w-4 h-4 rounded cursor-pointer"
                   style="accent-color:#22d3ee;border:1px solid rgba(34,211,238,0.3);background:rgba(255,255,255,0.05);">
            <label for="remember" class="text-sm cursor-pointer select-none" style="color:rgba(197,198,205,0.65);">
                Mantener sesión iniciada
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                :disabled="submitting"
                :class="submitting ? 'opacity-70 cursor-not-allowed' : ''"
                class="scan-btn relative w-full py-3 text-sm font-semibold rounded-xl transition-all duration-300"
                style="background:linear-gradient(135deg,#0284c7,#0e7490);color:#fff;border:1px solid rgba(34,211,238,0.3);box-shadow:0 4px 20px rgba(34,211,238,0.18);"
                onmouseover="if(!this.disabled){this.style.boxShadow='0 6px 28px rgba(34,211,238,0.32)';this.style.borderColor='rgba(34,211,238,0.55)';}"
                onmouseout="this.style.boxShadow='0 4px 20px rgba(34,211,238,0.18)';this.style.borderColor='rgba(34,211,238,0.3)';">
            <span class="scan-line"></span>
            <span x-show="!submitting" class="relative z-10">Ingresar al Campus Virtual</span>
            <span x-show="submitting" x-cloak class="relative z-10 flex items-center justify-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Ingresando...
            </span>
        </button>
    </form>

    {{-- Divider --}}
    <div class="mt-8 flex items-center gap-3">
        <div class="flex-1 h-px" style="background:rgba(34,211,238,0.12);"></div>
        <span class="font-mono-custom text-[10px] uppercase tracking-widest" style="color:rgba(197,198,205,0.35);">Campus Virtual</span>
        <div class="flex-1 h-px" style="background:rgba(34,211,238,0.12);"></div>
    </div>

    {{-- Info --}}
    <p class="mt-4 text-center text-xs" style="color:rgba(197,198,205,0.4);">
        ¿Problemas para ingresar? Contacte al
        <span style="color:rgba(34,211,238,0.6);">área de soporte técnico</span>.
    </p>
</div>
@endsection
