@extends('layouts.app')

@section('title', 'Configuración')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Configuración</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-1">Configuración del sistema</h2>
        <p class="text-sm text-gray-500 mb-6">Datos institucionales que aparecen en el sistema.</p>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la institución <span class="text-red-500">*</span></label>
                <input type="text" name="institution_name"
                       value="{{ old('institution_name', $settings['institution_name'] ?? '') }}" required maxlength="255"
                       class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('institution_name') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                       placeholder="Ej: Facultad de Educación">
                @error('institution_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Acrónimo <span class="text-red-500">*</span></label>
                <input type="text" name="institution_acronym"
                       value="{{ old('institution_acronym', $settings['institution_acronym'] ?? '') }}" required maxlength="20"
                       class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('institution_acronym') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                       placeholder="Ej: FAEDU">
                @error('institution_acronym') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo <span class="text-gray-400 font-normal">(opcional)</span></label>
                <input type="text" name="institution_subtitle"
                       value="{{ old('institution_subtitle', $settings['institution_subtitle'] ?? '') }}" maxlength="255"
                       class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                       placeholder="Ej: Posgrado — Sistema de Gestión Académica">
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <p class="text-sm text-amber-800">
                    <strong>Nota:</strong> Estos cambios se reflejan en el encabezado del sistema y los correos electrónicos. Requieren limpiar la caché para tomar efecto inmediato.
                </p>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-100">
                <button type="submit"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Guardar configuración
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
