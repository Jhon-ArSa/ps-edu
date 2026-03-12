@extends('layouts.app')

@section('title', 'Editar Comunicado')

@section('breadcrumb')
    <a href="{{ route('admin.announcements.index') }}" class="hover:text-primary-600">Comunicados</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Editar</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Editar comunicado</h2>

        <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required
                       class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('title') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido <span class="text-red-500">*</span></label>
                <textarea name="content" rows="6" required
                          class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('content') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none">{{ old('content', $announcement->content) }}</textarea>
                @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Imagen del comunicado <span class="text-gray-400 font-normal">(opcional, máx. 2 MB)</span></label>
                @if($announcement->image_path)
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ $announcement->image_url }}" alt="Imagen actual" class="h-16 w-auto rounded-lg border border-gray-200 object-cover">
                    <label class="flex items-center gap-1.5 text-xs text-red-600 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded text-red-500">
                        Eliminar imagen actual
                    </label>
                </div>
                @endif
                <input type="file" name="image" accept="image/jpg,image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destinatarios <span class="text-red-500">*</span></label>
                    <select name="target_role" required
                            class="w-full px-3 py-2.5 rounded-lg border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                        <option value="all"     {{ old('target_role', $announcement->target_role) === 'all'     ? 'selected' : '' }}>Todos</option>
                        <option value="docente" {{ old('target_role', $announcement->target_role) === 'docente' ? 'selected' : '' }}>Solo docentes</option>
                        <option value="alumno"  {{ old('target_role', $announcement->target_role) === 'alumno'  ? 'selected' : '' }}>Solo alumnos</option>
                        <option value="admin"   {{ old('target_role', $announcement->target_role) === 'admin'   ? 'selected' : '' }}>Solo admin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de publicación</label>
                    <input type="datetime-local" name="published_at"
                           value="{{ old('published_at', optional($announcement->published_at)->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.announcements.index') }}"
                   class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" name="publish_now" value="0"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Guardar borrador
                </button>
                <button type="submit" name="publish_now" value="1"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Publicar ahora
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
