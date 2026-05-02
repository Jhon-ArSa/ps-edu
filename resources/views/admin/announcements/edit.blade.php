@extends('layouts.app')

@section('title', 'Editar Comunicado')

@section('breadcrumb')
    <a href="{{ route('admin.announcements.index') }}" class="hover:text-primary-600">Comunicados</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Editar</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="announcementEditForm()">

    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Card 1: Contenido --}}
        <div class="card animate-fade-in-up">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Contenido del comunicado</h2>
                    <p class="text-xs text-gray-400">Modifica el mensaje para la comunidad</p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="form-label">Titulo <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required
                           placeholder="Ej: Inicio de matrículas para el semestre 2026-I"
                           class="form-input w-full">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Contenido <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="6" required placeholder="Escribe el contenido del comunicado..."
                              class="form-input w-full resize-none">{{ old('content', $announcement->content) }}</textarea>
                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Card 2: Imagen --}}
        <div class="card animate-fade-in-up delay-1">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Imagen del comunicado</h2>
                    <p class="text-xs text-gray-400">Opcional - JPG, PNG, WEBP (max. 2 MB)</p>
                </div>
            </div>
            <div class="px-6 py-5">
                {{-- Imagen actual --}}
                <div x-show="hasCurrentImage && !removeCurrentImage" x-cloak class="mb-4">
                    <div class="relative inline-block">
                        <img src="{{ $announcement->image_url }}" class="w-full max-w-md h-48 object-cover rounded-xl border-2 border-gray-200 shadow-lg" alt="Imagen actual">
                        <button type="button" @click="removeCurrentImage = true"
                                class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-sm font-bold transition-all hover:scale-110 shadow-lg">
                            &times;
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Imagen actual del comunicado</p>
                </div>
                <input type="hidden" name="remove_image" :value="removeCurrentImage && !imagePreview ? '1' : '0'">

                {{-- Preview nueva imagen --}}
                <div x-show="imagePreview" x-cloak class="mb-4">
                    <div class="relative inline-block">
                        <img :src="imagePreview" class="w-full max-w-md h-48 object-cover rounded-xl border-2 border-gray-200 shadow-lg" alt="Vista previa">
                        <button type="button" @click="removeNewImage()"
                                class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-sm font-bold transition-all hover:scale-110 shadow-lg">
                            &times;
                        </button>
                    </div>
                    <p class="text-xs text-emerald-500 mt-2 font-medium">Nueva imagen seleccionada</p>
                </div>

                {{-- Drop zone --}}
                <div x-show="!imagePreview && (removeCurrentImage || !hasCurrentImage)" class="relative">
                    <input type="file" name="image" accept="image/jpg,image/jpeg,image/png,image/webp"
                           @change="handleImage($event)"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="border-2 border-dashed border-gray-300 hover:border-primary-400 rounded-xl p-8 text-center transition-all bg-gray-50 hover:bg-primary-50">
                        <div class="flex flex-col items-center space-y-3">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-600">Arrastra tu imagen aqui</p>
                                <p class="text-xs text-gray-400">o <span class="text-primary-600 font-medium">haz clic para seleccionar</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botón para cambiar imagen cuando ya hay una actual --}}
                <div x-show="hasCurrentImage && !removeCurrentImage && !imagePreview" class="mt-3">
                    <label class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-violet-600 bg-violet-50 hover:bg-violet-100 rounded-xl cursor-pointer transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Cambiar imagen
                        <input type="file" name="image" accept="image/jpg,image/jpeg,image/png,image/webp"
                               @change="handleImage($event)" class="hidden">
                    </label>
                </div>

                @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Card 3: Configuracion --}}
        <div class="card animate-fade-in-up delay-2">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Configuracion</h2>
                    <p class="text-xs text-gray-400">Destinatarios y programacion de publicacion</p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-4">
                {{-- Target role cards --}}
                <div>
                    <label class="form-label">Destinatarios <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <label class="relative flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 cursor-pointer transition-all text-center"
                               :class="targetRole === 'all' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="target_role" value="all" x-model="targetRole" class="sr-only">
                            <svg class="w-5 h-5" :class="targetRole === 'all' ? 'text-blue-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span class="text-xs font-bold" :class="targetRole === 'all' ? 'text-blue-700' : 'text-gray-500'">Todos</span>
                        </label>
                        <label class="relative flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 cursor-pointer transition-all text-center"
                               :class="targetRole === 'docente' ? 'border-violet-400 bg-violet-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="target_role" value="docente" x-model="targetRole" class="sr-only">
                            <svg class="w-5 h-5" :class="targetRole === 'docente' ? 'text-violet-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0H8m8 0a2 2 0 012 2v6M8 6a2 2 0 00-2 2v6"/></svg>
                            <span class="text-xs font-bold" :class="targetRole === 'docente' ? 'text-violet-700' : 'text-gray-500'">Docentes</span>
                        </label>
                        <label class="relative flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 cursor-pointer transition-all text-center"
                               :class="targetRole === 'alumno' ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="target_role" value="alumno" x-model="targetRole" class="sr-only">
                            <svg class="w-5 h-5" :class="targetRole === 'alumno' ? 'text-emerald-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
                            <span class="text-xs font-bold" :class="targetRole === 'alumno' ? 'text-emerald-700' : 'text-gray-500'">Alumnos</span>
                        </label>
                        <label class="relative flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 cursor-pointer transition-all text-center"
                               :class="targetRole === 'admin' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="target_role" value="admin" x-model="targetRole" class="sr-only">
                            <svg class="w-5 h-5" :class="targetRole === 'admin' ? 'text-red-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                            <span class="text-xs font-bold" :class="targetRole === 'admin' ? 'text-red-700' : 'text-gray-500'">Admin</span>
                        </label>
                    </div>
                </div>

                {{-- Publication date --}}
                <div>
                    <label class="form-label">Fecha de publicacion <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="datetime-local" name="published_at"
                           value="{{ old('published_at', optional($announcement->published_at)->format('Y-m-d\TH:i')) }}"
                           class="form-input w-full">
                    <p class="text-xs text-gray-400 mt-1">Si no se especifica, quedara como borrador.</p>
                </div>

                {{-- Popup / Emergente toggle --}}
                <div class="flex items-center justify-between p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-orange-300 transition-colors"
                     :class="isPopup ? 'border-orange-300 bg-orange-50' : ''">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors"
                             :class="isPopup ? 'bg-orange-100' : 'bg-gray-100'">
                            <svg class="w-5 h-5 transition-colors" :class="isPopup ? 'text-orange-500' : 'text-gray-400'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" :class="isPopup ? 'text-orange-800' : 'text-gray-700'">
                                Mostrar como anuncio emergente
                            </p>
                            <p class="text-xs" :class="isPopup ? 'text-orange-600' : 'text-gray-400'">
                                Aparecerá como ventana emergente al entrar a la intranet
                            </p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_popup" value="0">
                        <input type="checkbox" name="is_popup" value="1" x-model="isPopup" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-2 animate-fade-in-up delay-3">
            <a href="{{ route('admin.announcements.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Cancelar
            </a>
            <div class="flex items-center gap-2">
                <button type="submit" name="publish_now" value="0"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Guardar borrador
                </button>
                <button type="submit" name="publish_now" value="1"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Publicar ahora
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function announcementEditForm() {
    return {
        targetRole: '{{ old('target_role', $announcement->target_role) }}',
        isPopup: {{ old('is_popup', $announcement->is_popup) ? 'true' : 'false' }},
        imagePreview: null,
        hasCurrentImage: {{ $announcement->image_path && $announcement->hasValidImage() ? 'true' : 'false' }},
        removeCurrentImage: false,
        handleImage(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.imagePreview = ev.target.result;
                    this.removeCurrentImage = true;
                };
                reader.readAsDataURL(file);
            }
        },
        removeNewImage() {
            this.imagePreview = null;
            const inputs = this.$el.querySelectorAll('input[name="image"]');
            inputs.forEach(input => input.value = '');
        }
    }
}
</script>
@endsection
