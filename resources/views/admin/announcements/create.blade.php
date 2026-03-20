@extends('layouts.app')

@section('title', 'Nuevo Comunicado')

@section('breadcrumb')
    <a href="{{ route('admin.announcements.index') }}" class="hover:text-primary-600">Comunicados</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Nuevo</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Crear comunicado</h2>

        <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('title') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido <span class="text-red-500">*</span></label>
                <textarea name="content" rows="6" required
                          class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('content') ? 'border-red-400' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none">{{ old('content') }}</textarea>
                @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Imagen del comunicado <span class="text-gray-400 font-normal">(opcional, máx. 2 MB)</span></label>

                {{-- Vista previa de imagen --}}
                <div id="image-preview-container" class="hidden mb-4">
                    <div class="relative inline-block">
                        <img id="image-preview" class="w-full max-w-md h-48 object-cover rounded-xl border-2 border-gray-200 shadow-lg" alt="Vista previa">
                        <button type="button" id="remove-image"
                                class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-sm font-bold transition-all duration-200 hover:scale-110 shadow-lg">
                            ×
                        </button>
                    </div>
                </div>

                {{-- Input de archivo con área de arrastre --}}
                <div class="relative">
                    <input type="file" id="image-input" name="image" accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-primary-400 rounded-xl p-8 text-center transition-all duration-200 bg-gray-50 hover:bg-primary-50">
                        <div class="flex flex-col items-center space-y-3">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-600">Arrastra tu imagen aquí</p>
                                <p class="text-xs text-gray-400">o <span class="text-primary-600 font-medium">haz clic para seleccionar</span></p>
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP (máx. 2MB)</p>
                            </div>
                        </div>
                    </div>
                </div>
                @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destinatarios <span class="text-red-500">*</span></label>
                    <select name="target_role" required
                            class="w-full px-3 py-2.5 rounded-lg border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                        <option value="all"     {{ old('target_role', 'all') === 'all'     ? 'selected' : '' }}>Todos</option>
                        <option value="docente" {{ old('target_role') === 'docente' ? 'selected' : '' }}>Solo docentes</option>
                        <option value="alumno"  {{ old('target_role') === 'alumno'  ? 'selected' : '' }}>Solo alumnos</option>
                        <option value="admin"   {{ old('target_role') === 'admin'   ? 'selected' : '' }}>Solo admin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de publicación <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="datetime-local" name="published_at"
                           value="{{ old('published_at') }}"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    <p class="text-xs text-gray-400 mt-1">Si no se especifica, quedará como borrador.</p>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image-input');
    const dropZone = document.getElementById('drop-zone');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImage = document.getElementById('image-preview');
    const removeButton = document.getElementById('remove-image');

    // Función para mostrar vista previa
    function showPreview(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
                dropZone.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }

    // Función para limpiar vista previa
    function clearPreview() {
        previewImage.src = '';
        previewContainer.classList.add('hidden');
        dropZone.style.display = 'block';
        imageInput.value = '';
    }

    // Manejar selección de archivo
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            showPreview(file);
        }
    });

    // Manejar arrastrar y soltar
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-primary-400', 'bg-primary-100');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-primary-400', 'bg-primary-100');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-primary-400', 'bg-primary-100');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            imageInput.files = files;
            showPreview(files[0]);
        }
    });

    // Botón para eliminar imagen
    removeButton.addEventListener('click', function() {
        clearPreview();
    });
});
</script>

@endsection
