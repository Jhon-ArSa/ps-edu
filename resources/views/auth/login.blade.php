@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Iniciar Sesión</h2>
    <p class="text-gray-500 text-sm mb-7">Ingrese sus credenciales para acceder al Campus Virtual</p>

    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
            <div class="relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    autofocus
                    class="w-full pl-10 pr-4 py-3 rounded-xl border text-sm transition-all
                           {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                           focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent focus:bg-white"
                    placeholder="correo@institución.edu.pe"
                >
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="text-sm font-medium text-gray-700">Contraseña</label>
                <a href="{{ route('password.request') }}" class="text-xs text-primary-600 hover:text-primary-800 font-medium hover:underline">
                    ¿Olvidó su contraseña?
                </a>
            </div>
            <div x-data="{ show: false }" class="relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    class="w-full pl-10 pr-12 py-3 rounded-xl border text-sm transition-all
                           {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                           focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent focus:bg-white"
                    placeholder="••••••••"
                >
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Remember --}}
        <div class="flex items-center gap-2.5">
            <input id="remember" type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-400">
            <label for="remember" class="text-sm text-gray-600 cursor-pointer select-none">Mantener sesión iniciada</label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white font-semibold py-3 rounded-xl text-sm transition-all shadow-sm hover:shadow-md">
            Ingresar al Campus Virtual
        </button>
    </form>

    {{-- Divider --}}
    <div class="mt-6 flex items-center gap-3">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-xs text-gray-400">Sistema de gestión académica</span>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    {{-- Info --}}
    <p class="mt-4 text-center text-xs text-gray-400">
        ¿Problemas para ingresar? Contacte al área de soporte técnico.
    </p>
</div>
@endsection
