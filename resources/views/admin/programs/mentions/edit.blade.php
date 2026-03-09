@extends('layouts.app')

@section('title', 'Editar Mención')

@section('breadcrumb')
    <a href="{{ route('admin.programs.index') }}" class="hover:text-primary-600">Programas</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.programs.show', $program) }}" class="hover:text-primary-600">{{ $program->code }}</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Editar mención</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="mentionEditForm()">

    {{-- Info banner --}}
    <div class="bg-amber-50 border border-amber-200/60 rounded-xl p-4 flex items-start gap-3 animate-fade-in-up">
        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-800">Editando mención: {{ $mention->name }}</p>
            <p class="text-xs text-amber-600 mt-0.5">Programa: {{ $program->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.programs.mentions.update', [$program, $mention]) }}" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Card 1: Datos de la mención --}}
        <div class="card animate-fade-in-up delay-1">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-rose-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Información de la mención</h2>
                    <p class="text-xs text-gray-400">Nombre y descripción de la especialización</p>
                </div>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="form-label">Nombre de la mención <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $mention->name) }}" required placeholder="Ej: Gestión Educativa"
                           class="form-input w-full">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Descripción</label>
                    <textarea name="description" rows="3" placeholder="Descripción breve de la mención..." class="form-input w-full resize-none">{{ old('description', $mention->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all flex-1"
                               :class="status === 'active' ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="status" value="active" x-model="status" class="sr-only">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0" :class="status === 'active' ? 'ring-4 ring-emerald-100' : 'opacity-30'"></span>
                            <div>
                                <p class="text-sm font-bold" :class="status === 'active' ? 'text-emerald-700' : 'text-gray-500'">Activo</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all flex-1"
                               :class="status === 'inactive' ? 'border-gray-400 bg-gray-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="status" value="inactive" x-model="status" class="sr-only">
                            <span class="w-3 h-3 rounded-full bg-gray-400 shrink-0" :class="status === 'inactive' ? 'ring-4 ring-gray-200' : 'opacity-30'"></span>
                            <div>
                                <p class="text-sm font-bold" :class="status === 'inactive' ? 'text-gray-700' : 'text-gray-500'">Inactivo</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Plan de estudios --}}
        <div class="card animate-fade-in-up delay-2">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">Plan de estudios</h2>
                        <p class="text-xs text-gray-400">Cursos organizados por semestre · <span x-text="items.length"></span> cursos</p>
                    </div>
                </div>
                <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Agregar curso
                </button>
            </div>

            <div class="px-6 py-5 space-y-3">
                <template x-if="items.length === 0">
                    <div class="py-8 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Aún no hay cursos en el plan de estudios</p>
                        <p class="text-xs text-gray-400 mt-1">Haz clic en "Agregar curso" para comenzar.</p>
                    </div>
                </template>

                <template x-for="(item, index) in items" :key="index">
                    <div class="flex items-start gap-3 bg-gray-50 rounded-xl border border-gray-200 p-4 transition-all hover:border-gray-300">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center shrink-0 mt-1">
                            <span class="text-blue-600 text-[10px] font-extrabold" x-text="index + 1"></span>
                        </div>
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-12 gap-3">
                            {{-- Semestre --}}
                            <div class="sm:col-span-2">
                                <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Sem.</label>
                                <select :name="'items[' + index + '][semester_number]'" x-model="item.semester_number"
                                        class="form-input w-full text-sm mt-1">
                                    @if($program->has_propedeutic)
                                    <option value="0">0 - Prop.</option>
                                    @endif
                                    @for($i = 1; $i <= $program->duration_semesters; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            {{-- Nombre --}}
                            <div class="sm:col-span-6">
                                <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Nombre del curso</label>
                                <input type="text" :name="'items[' + index + '][course_name]'" x-model="item.course_name"
                                       class="form-input w-full text-sm mt-1" placeholder="Ej: Metodología de la investigación">
                            </div>
                            {{-- Créditos --}}
                            <div class="sm:col-span-2">
                                <label class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Créd.</label>
                                <input type="number" :name="'items[' + index + '][credits]'" x-model="item.credits"
                                       class="form-input w-full text-sm mt-1" min="1" max="30" placeholder="—">
                            </div>
                            {{-- Electivo --}}
                            <div class="sm:col-span-2 flex items-end pb-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="hidden" :name="'items[' + index + '][is_elective]'" value="0">
                                    <input type="checkbox" :name="'items[' + index + '][is_elective]'" value="1" x-model="item.is_elective"
                                           class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                                    <span class="text-xs font-medium text-gray-500">Electivo</span>
                                </label>
                            </div>
                        </div>
                        <button type="button" @click="removeItem(index)"
                                class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all shrink-0 mt-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            @error('items') <p class="px-6 pb-4 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between animate-fade-in-up delay-3">
            <a href="{{ route('admin.programs.show', $program) }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al programa
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-sm font-bold px-6 py-3 rounded-xl shadow-lg shadow-amber-500/25 transition-all hover:shadow-xl hover:shadow-amber-500/30 hover:-translate-y-0.5 active:translate-y-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Guardar cambios
            </button>
        </div>
    </form>
</div>

<script>
function mentionEditForm() {
    return {
        status: '{{ old('status', $mention->status) }}',
        items: @json(old('items', $mention->curriculumItems->map(fn($i) => [
            'semester_number' => (string) $i->semester_number,
            'course_name' => $i->course_name,
            'credits' => $i->credits ? (string) $i->credits : '',
            'is_elective' => (bool) $i->is_elective,
        ])->values())),
        addItem() {
            this.items.push({
                semester_number: '1',
                course_name: '',
                credits: '',
                is_elective: false
            });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        }
    };
}
</script>
@endsection
