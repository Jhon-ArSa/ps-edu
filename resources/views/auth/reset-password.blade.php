@extends('layouts.auth')

@section('title', 'Nueva Contraseña')

@section('content')
<h2 class="text-xl font-bold text-gray-900 mb-1">Nueva contraseña</h2>
<p class="text-gray-500 text-sm mb-6">Ingrese su nueva contraseña para completar el proceso.</p>

<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $email ?? '') }}"
            class="w-full px-3 py-2.5 rounded-lg border text-sm
                   {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}
                   focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent"
        >
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
        <div x-data="{ show: false }" class="relative">
            <input id="password" :type="show ? 'text' : 'password'" name="password"
                   class="w-full pr-10 px-3 py-2.5 rounded-lg border text-sm
                          {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                          focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent"
                   placeholder="Mínimo 8 caracteres">
            <button type="button" @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
        <input id="password_confirmation" type="password" name="password_confirmation"
               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent"
               placeholder="Repita la nueva contraseña">
    </div>

    <button type="submit"
            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
        Restablecer contraseña
    </button>
</form>
@endsection
