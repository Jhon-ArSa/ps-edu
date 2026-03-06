@extends('layouts.auth')

@section('title', 'Recuperar Contraseña')

@section('content')
<a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600 mb-4">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Volver al inicio de sesión
</a>

<h2 class="text-xl font-bold text-gray-900 mb-1">Recuperar contraseña</h2>
<p class="text-gray-500 text-sm mb-6">Ingrese su correo y le enviaremos instrucciones para restablecer su contraseña.</p>

@if(session('status'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            autofocus
            class="w-full px-3 py-2.5 rounded-lg border text-sm transition-colors
                   {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}
                   focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent"
            placeholder="correo@institución.edu.pe"
        >
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
        Enviar enlace de recuperación
    </button>
</form>
@endsection
