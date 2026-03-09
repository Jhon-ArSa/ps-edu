@extends('layouts.app')

@section('title', 'Editar Curso')

@section('breadcrumb')
    <a href="{{ route('admin.courses.index') }}" class="hover:text-primary-600">Cursos</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Editar: {{ $course->name }}</span>
@endsection

@section('content')
<div x-data="courseEditForm()" class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Editar curso</h1>
            <p class="text-sm text-gray-500 mt-0.5">Modifique los datos del curso, cambie el docente o actualice la matrícula.</p>
        </div>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $course->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
            {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ═══════════════════════════════════════════════════════════
             SECCIÓN 1: INFORMACIÓN GENERAL
        ═══════════════════════════════════════════════════════════ --}}
        <div class="card animate-fade-in-up">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Información general</h2>
                    <p class="text-xs text-gray-400">Datos básicos del curso</p>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del curso <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $course->name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('name') ? 'border-red-400 ring-2 ring-red-100' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-shadow">
                        @error('name') <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Código único <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $course->code) }}" required maxlength="30"
                               class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('code') ? 'border-red-400 ring-2 ring-red-100' : 'border-gray-300' }} text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-shadow">
                        @error('code') <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado <span class="text-red-500">*</span></label>
                        <div class="flex gap-3">
                            <label class="flex-1 relative cursor-pointer">
                                <input type="radio" name="status" value="active" {{ old('status', $course->status) === 'active' ? 'checked' : '' }} class="peer sr-only">
                                <div class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-medium text-gray-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-all">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Activo
                                </div>
                            </label>
                            <label class="flex-1 relative cursor-pointer">
                                <input type="radio" name="status" value="inactive" {{ old('status', $course->status) === 'inactive' ? 'checked' : '' }} class="peer sr-only">
                                <div class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-medium text-gray-600 peer-checked:border-gray-500 peer-checked:bg-gray-50 peer-checked:text-gray-700 transition-all">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span> Inactivo
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 resize-none transition-shadow">{{ old('description', $course->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             SECCIÓN 2: PROGRAMA ACADÉMICO
        ═══════════════════════════════════════════════════════════ --}}
        <div class="card animate-fade-in-up delay-1">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Programa académico</h2>
                    <p class="text-xs text-gray-400">Vincula el curso a un programa, ciclo y semestre</p>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Programa de posgrado</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/>
                            </svg>
                            <select name="program_id"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-shadow appearance-none">
                                <option value="">Sin programa asignado</option>
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->id }}" {{ old('program_id', $course->program_id) == $prog->id ? 'selected' : '' }}>
                                        {{ $prog->name }} ({{ $prog->degree_type_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('program_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Ciclo</label>
                        <select name="cycle"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-shadow">
                            <option value="">—</option>
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}" {{ old('cycle', $course->cycle) == $i ? 'selected' : '' }}>Ciclo {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Año</label>
                        <input type="number" name="year" value="{{ old('year', $course->year) }}" min="2000" max="2100"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-shadow">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Semestre académico</label>
                        <select name="semester_id"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-shadow">
                            <option value="">Sin asignar</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ old('semester_id', $course->semester_id) == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->name }}
                                    @if($sem->is_active) — ✦ Activo @endif
                                    @if($sem->is_planned) — Planificado @endif
                                </option>
                            @endforeach
                        </select>
                        @error('semester_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Período</label>
                        <div class="flex gap-3">
                            <label class="flex-1 relative cursor-pointer">
                                <input type="radio" name="semester" value="I" {{ old('semester', $course->semester) === 'I' ? 'checked' : '' }} class="peer sr-only">
                                <div class="flex flex-col items-center gap-1 px-4 py-3 rounded-xl border-2 border-gray-200 text-sm peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:text-primary-700 transition-all">
                                    <span class="text-lg font-bold">I</span>
                                    <span class="text-[10px] text-gray-400 peer-checked:text-primary-500">Mar — Jul</span>
                                </div>
                            </label>
                            <label class="flex-1 relative cursor-pointer">
                                <input type="radio" name="semester" value="II" {{ old('semester', $course->semester) === 'II' ? 'checked' : '' }} class="peer sr-only">
                                <div class="flex flex-col items-center gap-1 px-4 py-3 rounded-xl border-2 border-gray-200 text-sm peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:text-primary-700 transition-all">
                                    <span class="text-lg font-bold">II</span>
                                    <span class="text-[10px] text-gray-400 peer-checked:text-primary-500">Ago — Dic</span>
                                </div>
                            </label>
                            <label class="flex-1 relative cursor-pointer">
                                <input type="radio" name="semester" value="" {{ !old('semester', $course->semester) ? 'checked' : '' }} class="peer sr-only">
                                <div class="flex flex-col items-center gap-1 px-4 py-3 rounded-xl border-2 border-gray-200 text-sm peer-checked:border-gray-500 peer-checked:bg-gray-50 peer-checked:text-gray-700 transition-all">
                                    <span class="text-lg font-bold">—</span>
                                    <span class="text-[10px] text-gray-400">Sin definir</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             SECCIÓN 3: DOCENTE ASIGNADO
        ═══════════════════════════════════════════════════════════ --}}
        <div class="card animate-fade-in-up delay-2" style="overflow: visible; z-index: 30; position: relative">
            <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Docente responsable</h2>
                    <p class="text-xs text-gray-400">Seleccione el docente que impartirá el curso</p>
                </div>
            </div>
            <div class="p-6">
                <div x-data="{ teacherSearch: '{{ old('teacher_id', $course->teacher_id) ? addslashes($teachers->firstWhere('id', old('teacher_id', $course->teacher_id))?->name ?? '') : '' }}', open: false }" class="relative" @click.away="open = false">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Buscar docente <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="teacherSearch" @focus="open = true" @input="open = true"
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-shadow"
                               placeholder="Escriba para buscar por nombre…"
                               autocomplete="off">
                    </div>

                    <div x-show="open" x-transition.opacity class="absolute z-20 mt-1.5 w-full bg-white rounded-xl border border-gray-200 shadow-xl shadow-gray-200/50 max-h-52 overflow-y-auto">
                        @foreach($teachers as $teacher)
                        <label x-show="!teacherSearch || '{{ strtolower($teacher->name) }}'.includes(teacherSearch.toLowerCase())"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-primary-50 cursor-pointer transition-colors border-b border-gray-50 last:border-0">
                            <input type="radio" name="teacher_id" value="{{ $teacher->id }}"
                                   {{ old('teacher_id', $course->teacher_id) == $teacher->id ? 'checked' : '' }}
                                   class="text-primary-600 focus:ring-primary-500"
                                   @click="open = false; teacherSearch = '{{ $teacher->name }}'">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center shrink-0">
                                <span class="text-amber-600 text-xs font-bold">{{ strtoupper(substr($teacher->name, 0, 2)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $teacher->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $teacher->email }}</p>
                            </div>
                            @if(old('teacher_id', $course->teacher_id) == $teacher->id)
                            <span class="text-[10px] bg-amber-200 text-amber-800 font-semibold px-2 py-0.5 rounded-full">Actual</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                    @error('teacher_id') <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             SECCIÓN 4: MATRÍCULA DE ALUMNOS
        ═══════════════════════════════════════════════════════════ --}}
        <div class="card animate-fade-in-up delay-3">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">Matrícula de alumnos</h2>
                        <p class="text-xs text-gray-400">Gestione los alumnos matriculados en este curso</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full" :class="selectedStudents.length ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400'"
                      x-text="selectedStudents.length + ' seleccionado' + (selectedStudents.length !== 1 ? 's' : '')"></span>
            </div>
            <div class="p-6 space-y-4">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="studentSearch"
                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-shadow"
                           placeholder="Buscar alumno por nombre, email o DNI…">
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <button type="button" @click="selectAllFiltered()" class="text-primary-600 hover:text-primary-700 font-medium transition-colors">Seleccionar todos</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" @click="selectedStudents = []" class="text-gray-500 hover:text-gray-700 font-medium transition-colors">Quitar todos</button>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden max-h-72 overflow-y-auto">
                    @forelse($students as $student)
                    <label x-show="matchStudent({{ json_encode(['name' => $student->name, 'email' => $student->email, 'dni' => $student->dni ?? '']) }})"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50/50 cursor-pointer transition-colors border-b border-gray-50 last:border-0"
                           :class="selectedStudents.includes('{{ $student->id }}') && 'bg-blue-50/70'">
                        <input type="checkbox" name="students[]" value="{{ $student->id }}"
                               x-model="selectedStudents"
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 shrink-0">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center shrink-0">
                            @if($student->avatar)
                                <img src="{{ $student->avatar_url }}" class="w-full h-full rounded-xl object-cover">
                            @else
                                <span class="text-blue-600 text-xs font-bold">{{ strtoupper(substr($student->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $student->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $student->email }}@if($student->alumnoProfile && $student->alumnoProfile->code) · {{ $student->alumnoProfile->code }}@endif</p>
                        </div>
                        @if(in_array($student->id, $enrolledIds))
                        <span class="text-[10px] bg-emerald-100 text-emerald-700 font-semibold px-2 py-0.5 rounded-full shrink-0">Matriculado</span>
                        @endif
                    </label>
                    @empty
                    <div class="py-10 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                        </div>
                        <p class="text-sm text-gray-400">No hay alumnos registrados en el sistema.</p>
                    </div>
                    @endforelse
                </div>

                <div x-show="selectedStudents.length > 0" x-transition class="flex flex-wrap gap-2 pt-1">
                    <template x-for="id in selectedStudents" :key="id">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">
                            <span x-text="getStudentName(id)"></span>
                            <button type="button" @click="selectedStudents = selectedStudents.filter(s => s !== id)" class="hover:text-blue-900 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </span>
                    </template>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             ACCIONES
        ═══════════════════════════════════════════════════════════ --}}
        <div class="flex items-center justify-between pt-2 animate-fade-in-up delay-4">
            <a href="{{ route('admin.courses.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Guardar cambios
            </button>
        </div>
    </form>
</div>

<script>
function courseEditForm() {
    return {
        studentSearch: '',
        selectedStudents: @json(old('students', $enrolledIds)).map(String),
        students: @json($studentsJson),

        matchStudent(s) {
            if (!this.studentSearch) return true;
            const q = this.studentSearch.toLowerCase();
            return s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q) || (s.dni && s.dni.toLowerCase().includes(q));
        },

        selectAllFiltered() {
            const q = this.studentSearch.toLowerCase();
            this.students.forEach(s => {
                if (!q || s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q) || (s.dni && s.dni.toLowerCase().includes(q))) {
                    if (!this.selectedStudents.includes(s.id)) this.selectedStudents.push(s.id);
                }
            });
        },

        getStudentName(id) {
            const s = this.students.find(s => s.id === id);
            return s ? s.name : '';
        }
    }
}
</script>
@endsection
