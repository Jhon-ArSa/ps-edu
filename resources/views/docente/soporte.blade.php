@extends('layouts.app')

@section('title', 'Soporte')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Soporte</span>
@endsection

@section('content')
<div class="max-w-xl mx-auto space-y-5">

    <div>
        <h1 class="text-xl font-bold text-gray-900">Centro de Soporte</h1>
        <p class="text-sm text-gray-500">Envíe sus consultas o incidencias al equipo de administración.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('docente.soporte.send') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Asunto <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                       class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('subject') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                       placeholder="Describa brevemente su consulta">
                @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje <span class="text-red-500">*</span></label>
                <textarea name="message" rows="6" required maxlength="2000"
                          class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('message') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none"
                          placeholder="Detalle su consulta o problema…">{{ old('message') }}</textarea>
                @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-400 mt-1">Máximo 2000 caracteres</p>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-100">
                <button type="submit"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Enviar mensaje
                </button>
            </div>
        </form>
    </div>

    <div class="bg-primary-50 border border-primary-100 rounded-xl p-4">
        <h3 class="text-sm font-semibold text-primary-800 mb-1">Información de soporte</h3>
        <p class="text-sm text-primary-700">
            Su mensaje será recibido por el equipo de administración del sistema.
            Recibirá respuesta a través de su correo institucional:
            <strong>{{ auth()->user()->email }}</strong>
        </p>
    </div>

</div>
@endsection
