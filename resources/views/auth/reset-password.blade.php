@extends('layouts.auth')

@section('title', 'Nueva Contraseña')

@section('form-header')
<div class="mb-8 text-center">
    <p class="font-mono-custom mb-2 uppercase tracking-widest" style="font-size:10px;color:#22d3ee;letter-spacing:0.25em;">Restablecimiento de Contraseña</p>
    <h2 style="font-size:1.6rem;font-weight:800;color:#d8e2ff;letter-spacing:-0.01em;">Nueva contraseña</h2>
    <p class="mt-1 text-sm" style="color:rgba(197,198,205,0.6);">Ingrese su nueva contraseña para completar el proceso.</p>
</div>
@endsection

@section('content')
<div>
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

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
                    value="{{ old('email', $email ?? '') }}"
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

        <div>
            <label for="password" class="auth-label">Nueva contraseña</label>
            <div x-data="{ show: false }" class="auth-input-wrap relative">
                <div class="auth-icon absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" :type="show ? 'text' : 'password'" name="password"
                       class="auth-input {{ $errors->has('password') ? 'error' : '' }}"
                       style="padding-left:2.75rem;padding-right:3rem;"
                       placeholder="Mínimo 8 caracteres">
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

        <div>
            <label for="password_confirmation" class="auth-label">Confirmar contraseña</label>
            <div class="auth-input-wrap relative">
                <div class="auth-icon absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="auth-input"
                       style="padding-left:2.75rem;"
                       placeholder="Repita la nueva contraseña">
            </div>
        </div>

        <button type="submit"
                class="scan-btn relative w-full py-3 text-sm font-semibold rounded-xl transition-all duration-300"
                style="background:linear-gradient(135deg,#0284c7,#0e7490);color:#fff;border:1px solid rgba(34,211,238,0.3);box-shadow:0 4px 20px rgba(34,211,238,0.18);"
                onmouseover="this.style.boxShadow='0 6px 28px rgba(34,211,238,0.32)';this.style.borderColor='rgba(34,211,238,0.55)';"
                onmouseout="this.style.boxShadow='0 4px 20px rgba(34,211,238,0.18)';this.style.borderColor='rgba(34,211,238,0.3)';">
            <span class="scan-line"></span>
            <span class="relative z-10">Restablecer contraseña</span>
        </button>
    </form>
</div>
@endsection
