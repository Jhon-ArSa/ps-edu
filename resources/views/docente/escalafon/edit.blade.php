@extends('layouts.app')

@section('title', 'Editar Escalafón')

@section('breadcrumb')
    <a href="{{ route('docente.escalafon.show') }}" class="hover:text-primary-600">Escalafón</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Editar</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Editar perfil profesional</h2>

        <form method="POST" action="{{ route('docente.escalafon.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título (Ej: Dr., Mg., Lic.)</label>
                    <input type="text" name="title" value="{{ old('title', $profile->title) }}" maxlength="20"
                           class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('title') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                           placeholder="Ej: Dr.">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grado académico</label>
                    <input type="text" name="degree" value="{{ old('degree', $profile->degree) }}"
                           class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('degree') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                           placeholder="Ej: Doctor en Ciencias de la Educación">
                    @error('degree') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                    <input type="text" name="specialty" value="{{ old('specialty', $profile->specialty) }}"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                           placeholder="Ej: Didáctica y Currículum">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría docente</label>
                    <input type="text" name="category" value="{{ old('category', $profile->category) }}"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                           placeholder="Ej: Principal, Asociado, Auxiliar">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Años de servicio</label>
                    <input type="number" name="years_of_service" value="{{ old('years_of_service', $profile->years_of_service) }}"
                           min="0" max="60"
                           class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('years_of_service') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    @error('years_of_service') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perfil profesional / Biografía</label>
                <textarea name="bio" rows="4" maxlength="2000"
                          class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('bio') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none"
                          placeholder="Breve descripción de su experiencia y logros académicos…">{{ old('bio', $profile->bio) }}</textarea>
                @error('bio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-400 mt-1">Máximo 2000 caracteres</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('docente.escalafon.show') }}"
                   class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
