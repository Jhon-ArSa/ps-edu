@extends('layouts.app')

@section('title', 'Nuevo Anuncio Emergente')

@section('breadcrumb')
    <a href="{{ route('docente.announcements.index') }}" class="hover:text-orange-600">Mis Anuncios</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-700 font-medium">Nuevo</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-5" x-data="docenteAnnouncementForm()">

    <form method="POST" action="{{ route('docente.announcements.store') }}"
          enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Card 1: Contenido --}}
        <div class="card animate-fade-in-up">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Contenido del anuncio</h2>
                    <p class="text-xs text-gray-400">Mensaje que verán los alumnos al ingresar a su cuenta</p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="form-label">Título <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="Ej: Cambio de horario — Semana 5"
                           class="form-input w-full">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Mensaje <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="5" required
                              placeholder="Escribe el contenido del anuncio..."
                              class="form-input w-full resize-none">{{ old('content') }}</textarea>
                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Card 2: Imagen --}}
        <div class="card animate-fade-in-up delay-1">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Imagen</h2>
                    <p class="text-xs text-gray-400">Opcional — JPG, PNG, WEBP (máx. 2 MB)</p>
                </div>
            </div>
            <div class="px-6 py-5">
                <div x-show="imagePreview" x-cloak class="mb-4">
                    <div class="relative inline-block">
                        <img :src="imagePreview" class="w-full max-w-md h-48 object-cover rounded-xl border-2 border-gray-200 shadow-lg" alt="Vista previa">
                        <button type="button" @click="removeImage()"
                                class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-sm font-bold transition-all hover:scale-110 shadow-lg">
                            &times;
                        </button>
                    </div>
                </div>
                <div x-show="!imagePreview" class="relative">
                    <input type="file" name="image" accept="image/jpg,image/jpeg,image/png,image/webp"
                           @change="handleImage($event)"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="border-2 border-dashed border-gray-300 hover:border-violet-400 rounded-xl p-8 text-center transition-all bg-gray-50 hover:bg-violet-50">
                        <div class="flex flex-col items-center space-y-3">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-600">Arrastra tu imagen aquí</p>
                                <p class="text-xs text-gray-400">o <span class="text-violet-600 font-medium">haz clic para seleccionar</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Card 3: Cursos destinatarios --}}
        <div class="card animate-fade-in-up delay-2">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Cursos destinatarios <span class="text-red-500">*</span></h2>
                    <p class="text-xs text-gray-400">Solo los alumnos matriculados en los cursos seleccionados verán este anuncio</p>
                </div>
            </div>
            <div class="px-6 py-5">
                @if($programs->isEmpty())
                <div class="text-center py-6 text-gray-400">
                    <p class="text-sm font-medium">No tienes cursos asignados a ningún programa.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($programs as $program)
                    @php $programCourseIds = $program->courses->pluck('id')->toArray(); @endphp
                    <div x-data="programGroup({{ json_encode($programCourseIds) }}, selectedCourses)"
                         class="border border-gray-200 rounded-xl overflow-hidden">

                        {{-- Program header --}}
                        <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors text-left">
                            <div class="flex items-center gap-3">
                                {{-- Select-all checkbox for this program --}}
                                <div @click.stop>
                                    <input type="checkbox"
                                           :checked="allSelected"
                                           :indeterminate="someSelected && !allSelected"
                                           @change="toggleAll($event.target.checked)"
                                           class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $program->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $program->code }} · {{ $program->courses->count() }} {{ $program->courses->count() === 1 ? 'curso' : 'cursos' }}</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Courses list --}}
                        <div x-show="open" x-collapse class="divide-y divide-gray-100">
                            @foreach($program->courses as $course)
                            <label class="flex items-center gap-3 px-5 py-3 hover:bg-blue-50/50 cursor-pointer transition-colors"
                                   :class="selectedCourses.includes({{ $course->id }}) ? 'bg-blue-50' : ''">
                                <input type="checkbox"
                                       name="course_ids[]"
                                       value="{{ $course->id }}"
                                       x-model="selectedCourses"
                                       :value="{{ $course->id }}"
                                       {{ collect(old('course_ids', []))->contains($course->id) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800">{{ $course->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $course->code }}</p>
                                </div>
                                <div x-show="selectedCourses.includes({{ $course->id }})"
                                     class="w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Counter --}}
                <p class="mt-3 text-xs text-gray-500">
                    <span x-text="selectedCourses.length" class="font-semibold text-blue-600"></span>
                    curso(s) seleccionado(s)
                </p>

                @error('course_ids') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                @endif
            </div>
        </div>

        {{-- Card 4: Publicación --}}
        <div class="card animate-fade-in-up delay-3">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Programación</h2>
                    <p class="text-xs text-gray-400">¿Cuándo debe mostrarse el anuncio?</p>
                </div>
            </div>
            <div class="px-6 py-5">
                <label class="form-label">Fecha de publicación <span class="text-gray-400 font-normal">(opcional)</span></label>
                <input type="datetime-local" name="published_at" value="{{ old('published_at') }}"
                       class="form-input w-full">
                <p class="text-xs text-gray-400 mt-1">Si no se especifica, se guarda como borrador.</p>
            </div>
        </div>

        {{-- Info --}}
        <div class="flex items-start gap-3 px-4 py-3 bg-orange-50 border border-orange-200 rounded-xl text-sm text-orange-700">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>Este anuncio aparecerá como <strong>ventana emergente</strong> al ingresar a su cuenta y al entrar a la intranet. Cada alumno lo verá una sola vez.</p>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('docente.announcements.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Cancelar
            </a>
            <div class="flex items-center gap-2">
                <button type="submit" name="publish_now" value="0"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Guardar borrador
                </button>
                <button type="submit" name="publish_now" value="1"
                        :disabled="selectedCourses.length === 0 || {{ $programs->isEmpty() ? 'true' : 'false' }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl shadow-lg shadow-orange-500/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Publicar ahora
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function docenteAnnouncementForm() {
    return {
        imagePreview: null,
        selectedCourses: @json(array_map('intval', old('course_ids', []))),
        handleImage(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (ev) => this.imagePreview = ev.target.result;
                reader.readAsDataURL(file);
            }
        },
        removeImage() {
            this.imagePreview = null;
            const input = this.$el.querySelector('input[name="image"]');
            if (input) input.value = '';
        }
    }
}

function programGroup(programCourseIds, selectedCoursesRef) {
    return {
        open: true,
        get allSelected() {
            return programCourseIds.every(id => selectedCoursesRef.includes(id));
        },
        get someSelected() {
            return programCourseIds.some(id => selectedCoursesRef.includes(id));
        },
        toggleAll(checked) {
            if (checked) {
                programCourseIds.forEach(id => {
                    if (!selectedCoursesRef.includes(id)) selectedCoursesRef.push(id);
                });
            } else {
                programCourseIds.forEach(id => {
                    const idx = selectedCoursesRef.indexOf(id);
                    if (idx > -1) selectedCoursesRef.splice(idx, 1);
                });
            }
        }
    }
}
</script>
@endsection
