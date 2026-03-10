@extends('layouts.app')

@section('title', $course->name)

@section('breadcrumb')
    <a href="{{ route('docente.courses.index') }}" class="hover:text-primary-600">Mis Cursos</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">{{ $course->name }}</span>
@endsection

@section('content')
<div class="space-y-5" x-data="courseManager()">

    {{-- Course Header --}}
    <div class="bg-gradient-to-r from-primary-700 to-primary-900 rounded-xl p-6 text-white shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $course->status === 'active' ? 'bg-emerald-400/20 text-emerald-200 ring-1 ring-emerald-400/30' : 'bg-white/10 text-white/60 ring-1 ring-white/20' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-400' : 'bg-white/40' }}"></span>
                        {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span class="text-xs font-mono text-primary-200">{{ $course->code }}</span>
                </div>
                <h1 class="text-2xl font-bold text-white leading-tight truncate">{{ $course->name }}</h1>
                @if($course->description)
                <p class="mt-1 text-primary-200 text-sm line-clamp-2">{{ $course->description }}</p>
                @endif
            </div>
            <div class="flex flex-col items-end gap-2 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white">{{ $course->weeks->count() }}</p>
                        <p class="text-xs text-primary-200">Semanas</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white">{{ $course->students->count() }}</p>
                        <p class="text-xs text-primary-200">Alumnos</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white">{{ $course->weeks->sum(fn($w) => $w->materials->count()) }}</p>
                        <p class="text-xs text-primary-200">Materiales</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-white border border-gray-200 rounded-xl p-1">
        <button @click="activeTab = 'content'"
                :class="activeTab === 'content' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Contenido del Curso</span>
            <span class="bg-white/20 text-xs px-1.5 py-0.5 rounded-full" :class="activeTab === 'content' ? 'text-white' : 'bg-gray-100 text-gray-500'">
                {{ $course->weeks->count() }}
            </span>
        </button>
        <button @click="activeTab = 'students'"
                :class="activeTab === 'students' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Estudiantes</span>
            <span class="text-xs px-1.5 py-0.5 rounded-full" :class="activeTab === 'students' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500'">
                {{ $course->students->count() }}
            </span>
        </button>
        <a href="{{ route('docente.grades.index', $course) }}"
           class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all text-gray-600 hover:bg-gray-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h4"/>
            </svg>
            <span>Calificaciones</span>
        </a>

    {{-- Content Tab --}}
    <div x-show="activeTab === 'content'" class="space-y-4">

        {{-- Add Week --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-sm font-semibold text-gray-900">Agregar nueva semana</span>
                <span class="text-xs text-gray-400">({{ $course->weeks->count() }}/16)</span>
            </div>
            <form method="POST" action="{{ route('docente.courses.weeks.store', $course) }}" class="flex gap-3">
                @csrf
                <div class="flex-1">
                    <input type="text" name="title" placeholder="Título de la semana (ej: Introducción a la materia)"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                </div>
                <button type="submit"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-colors whitespace-nowrap shadow-sm">
                    + Agregar Semana
                </button>
            </form>
        </div>

        {{-- Weeks --}}
        @forelse($course->weeks->sortBy('number') as $week)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm"
             x-data="{ expanded: {{ $loop->first ? 'true' : 'false' }}, editingWeek: false, weekTab: 'materials' }">

            {{-- Week Header --}}
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <button @click="expanded = !expanded" class="flex items-center gap-3 flex-1 text-left">
                    <div class="w-8 h-8 rounded-full bg-primary-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                        {{ $week->number }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-primary-600 uppercase tracking-wider">Semana {{ $week->number }}</span>
                            <span x-show="!editingWeek" class="text-sm font-semibold text-gray-900">{{ $week->title ?? '' }}</span>
                        </div>
                        <div class="flex items-center gap-3 mt-0.5">
                            <span class="text-xs text-gray-400">
                                <span class="font-medium text-blue-600">{{ $week->materials->count() }}</span> materiales
                            </span>
                            <span class="text-gray-300">·</span>
                            <span class="text-xs text-gray-400">
                                <span class="font-medium text-violet-600">{{ $week->tasks->count() }}</span> tareas
                            </span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                <div class="flex items-center gap-1 shrink-0">
                    <button @click="editingWeek = !editingWeek; expanded = true"
                            class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                            title="Editar semana">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <form method="POST" action="{{ route('docente.courses.weeks.destroy', [$course, $week]) }}"
                          onsubmit="return confirm('¿Eliminar la Semana {{ $week->number }} y todos sus materiales y tareas?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar semana">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Edit week form --}}
            <div x-show="editingWeek" x-cloak class="px-5 py-4 bg-amber-50 border-b border-amber-100">
                <form method="POST" action="{{ route('docente.courses.weeks.update', [$course, $week]) }}"
                      class="space-y-3">
                    @csrf @method('PUT')
                    <div class="flex gap-3">
                        <input type="text" name="title" value="{{ $week->title }}" placeholder="Título de la semana"
                               class="flex-1 px-3 py-2 rounded-lg border border-amber-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 bg-white">
                        <button type="submit" class="px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-lg hover:bg-amber-600 transition-colors">
                            Guardar
                        </button>
                        <button type="button" @click="editingWeek = false" class="px-4 py-2 bg-white border border-amber-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                    </div>
                    <textarea name="description" rows="2" placeholder="Descripción de la semana (objetivos, temas, etc.)"
                              class="w-full px-3 py-2 rounded-lg border border-amber-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 bg-white resize-none">{{ $week->description }}</textarea>
                </form>
            </div>

            {{-- Expanded content --}}
            <div x-show="expanded">
                @if($week->description)
                <div class="px-5 py-3 bg-blue-50/40 border-b border-gray-100">
                    <p class="text-sm text-gray-600 flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $week->description }}
                    </p>
                </div>
                @endif

                {{-- Sub-tabs: Materials / Tasks --}}
                <div class="flex gap-1 px-5 pt-4 pb-0">
                    <button @click="weekTab = 'materials'"
                            :class="weekTab === 'materials' ? 'border-blue-500 text-blue-700 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold border-b-2 rounded-t-lg transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Materiales
                        <span class="px-1.5 py-0.5 rounded-full text-xs font-bold"
                              :class="weekTab === 'materials' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'">
                            {{ $week->materials->count() }}
                        </span>
                    </button>
                    <button @click="weekTab = 'tasks'"
                            :class="weekTab === 'tasks' ? 'border-violet-500 text-violet-700 bg-violet-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold border-b-2 rounded-t-lg transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Tareas
                        <span class="px-1.5 py-0.5 rounded-full text-xs font-bold"
                              :class="weekTab === 'tasks' ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500'">
                            {{ $week->tasks->count() }}
                        </span>
                    </button>
                </div>
                <div class="h-px bg-gray-100 mx-5"></div>

                {{-- ─── MATERIALS SECTION ──────────────────────────────────────────────── --}}
                <div x-show="weekTab === 'materials'">

                    {{-- Material list --}}
                    <div class="divide-y divide-gray-50">
                        @forelse($week->materials->sortBy('order') as $material)
                        <div class="flex items-start gap-3 px-5 py-3.5 group hover:bg-gray-50/60 transition-colors"
                             x-data="{ editMat: false }">

                            {{-- Type icon --}}
                            @if($material->type === 'file')
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            @elseif($material->type === 'video')
                            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            @else
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                            @endif

                            <div class="flex-1 min-w-0" x-show="!editMat">
                                <p class="text-sm font-semibold text-gray-900">{{ $material->title }}</p>
                                @if($material->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $material->description }}</p>
                                @endif
                                <div class="mt-1">
                                    @if($material->type === 'file')
                                        <a href="{{ $material->download_url }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 hover:underline font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Descargar
                                        </a>
                                    @elseif($material->type === 'link' || $material->type === 'video')
                                        <a href="{{ $material->url }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs text-primary-600 hover:underline truncate max-w-xs font-medium">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Abrir enlace
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Edit material inline --}}
                            <form x-show="editMat" x-cloak method="POST"
                                  action="{{ route('docente.courses.materials.update', [$course, $week, $material]) }}"
                                  class="flex-1 flex gap-2 items-center">
                                @csrf @method('PUT')
                                <input type="text" name="title" value="{{ $material->title }}"
                                       class="flex-1 px-2.5 py-1.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                                <button type="submit" class="px-3 py-1.5 bg-primary-600 text-white text-xs font-semibold rounded-lg hover:bg-primary-700">OK</button>
                                <button type="button" @click="editMat = false" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs rounded-lg hover:bg-gray-200">✕</button>
                            </form>

                            <div class="flex items-center gap-0.5 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="editMat = !editMat" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Editar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('docente.courses.materials.destroy', [$course, $week, $material]) }}"
                                      onsubmit="return confirm('¿Eliminar este material?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="px-5 py-6 text-center">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-xs text-gray-400">Sin materiales. Agrega el primero abajo.</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- Add material --}}
                    <div class="px-5 py-4 bg-blue-50/40 border-t border-blue-100/60" x-data="{ type: 'file' }">
                        <p class="text-xs font-bold text-blue-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar material
                        </p>
                        <form method="POST" action="{{ route('docente.courses.materials.store', [$course, $week]) }}"
                              enctype="multipart/form-data" class="space-y-3">
                            @csrf

                            <div class="flex gap-2">
                                <label class="flex-1">
                                    <input type="radio" name="type" value="file" x-model="type" class="sr-only peer">
                                    <div class="peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 cursor-pointer text-center px-3 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors">
                                        📄 Archivo
                                    </div>
                                </label>
                                <label class="flex-1">
                                    <input type="radio" name="type" value="link" x-model="type" class="sr-only peer">
                                    <div class="peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 cursor-pointer text-center px-3 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors">
                                        🔗 Enlace
                                    </div>
                                </label>
                                <label class="flex-1">
                                    <input type="radio" name="type" value="video" x-model="type" class="sr-only peer">
                                    <div class="peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 cursor-pointer text-center px-3 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors">
                                        🎬 Video
                                    </div>
                                </label>
                            </div>

                            <input type="text" name="title" required placeholder="Título del material *"
                                   class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white">

                            <div x-show="type === 'file'">
                                <input type="file" name="file"
                                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div x-show="type !== 'file'">
                                <input type="url" name="url" placeholder="https://…"
                                       class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-white">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Subir material
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ─── TASKS SECTION ──────────────────────────────────────────────────── --}}
                <div x-show="weekTab === 'tasks'" x-cloak>

                    {{-- Task list --}}
                    <div class="divide-y divide-gray-50">
                        @forelse($week->tasks as $task)
                        <div class="px-5 py-4 group hover:bg-gray-50/60 transition-colors"
                             x-data="{ showDetails: false, editTask: false }">

                            <div class="flex items-start gap-3">
                                {{-- Task icon --}}
                                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0" x-show="!editTask">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-semibold text-gray-900">{{ $task->title }}</p>
                                        @php $badge = $task->due_date_badge; @endphp
                                        <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full font-medium {{ $badge['class'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                            {{ $task->max_score }} pts
                                        </span>
                                        @if($task->status === 'inactive')
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Inactiva</span>
                                        @endif
                                    </div>
                                    @if($task->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ $task->description }}</p>
                                    @endif
                                    @if($task->instructions || $task->file_path)
                                    <button @click="showDetails = !showDetails"
                                            class="mt-2 text-xs text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1">
                                        <svg class="w-3 h-3 transition-transform" :class="showDetails ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                        <span x-text="showDetails ? 'Ocultar detalles' : 'Ver instrucciones'"></span>
                                    </button>
                                    <div x-show="showDetails" x-cloak class="mt-2 space-y-2">
                                        @if($task->instructions)
                                        <div class="p-3 bg-violet-50 rounded-lg text-xs text-gray-700 whitespace-pre-line">{{ $task->instructions }}</div>
                                        @endif
                                        @if($task->file_path)
                                        <a href="{{ Storage::url($task->file_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 text-xs text-violet-600 hover:underline font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Descargar guía/anexo
                                        </a>
                                        @endif
                                    </div>
                                    @endif
                                </div>

                                {{-- Edit task inline --}}
                                <form x-show="editTask" x-cloak method="POST"
                                      action="{{ route('docente.courses.tasks.update', [$course, $week, $task]) }}"
                                      enctype="multipart/form-data"
                                      class="flex-1 space-y-3">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="col-span-2">
                                            <input type="text" name="title" value="{{ $task->title }}" required
                                                   placeholder="Título de la tarea *"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white">
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-600 font-medium block mb-1">Fecha límite</label>
                                            <input type="datetime-local" name="due_date" value="{{ $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '' }}"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white">
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-600 font-medium block mb-1">Puntaje máximo</label>
                                            <input type="number" name="max_score" value="{{ $task->max_score }}" min="1" max="1000"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white">
                                        </div>
                                        <div class="col-span-2">
                                            <textarea name="description" rows="2" placeholder="Descripción breve"
                                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white resize-none">{{ $task->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="px-4 py-2 bg-violet-600 text-white text-xs font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                                            Guardar cambios
                                        </button>
                                        <button type="button" @click="editTask = false" class="px-4 py-2 bg-gray-100 text-gray-600 text-xs rounded-lg hover:bg-gray-200 transition-colors">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>

                                <div class="flex items-center gap-0.5 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="editTask = !editTask" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Editar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('docente.courses.tasks.destroy', [$course, $week, $task]) }}"
                                          onsubmit="return confirm('¿Eliminar la tarea «{{ $task->title }}»?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-5 py-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-violet-50 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 font-medium">Sin tareas asignadas</p>
                            <p class="text-xs text-gray-400 mt-0.5">Usa el formulario de abajo para crear la primera tarea.</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- Add task --}}
                    <div class="px-5 py-4 bg-violet-50/40 border-t border-violet-100/60"
                         x-data="{ showForm: false }">
                        <button @click="showForm = !showForm"
                                class="w-full flex items-center justify-between text-xs font-bold text-violet-700 uppercase tracking-wider py-1 hover:text-violet-900 transition-colors">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Agregar tarea
                            </span>
                            <svg class="w-4 h-4 transition-transform" :class="showForm ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="showForm" x-cloak class="mt-4">
                            <form method="POST" action="{{ route('docente.courses.tasks.store', [$course, $week]) }}"
                                  enctype="multipart/form-data" class="space-y-3">
                                @csrf

                                <div>
                                    <input type="text" name="title" required placeholder="Título de la tarea *"
                                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent bg-white">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs text-gray-600 font-semibold block mb-1">Fecha y hora límite</label>
                                        <input type="datetime-local" name="due_date"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-600 font-semibold block mb-1">Puntaje máximo</label>
                                        <input type="number" name="max_score" value="20" min="1" max="1000"
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white">
                                    </div>
                                </div>

                                <div>
                                    <textarea name="description" rows="2" placeholder="Descripción breve de la tarea (opcional)"
                                              class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white resize-none"></textarea>
                                </div>

                                <div>
                                    <textarea name="instructions" rows="3" placeholder="Instrucciones detalladas (rúbrica, pasos a seguir, formato, etc.)"
                                              class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white resize-none"></textarea>
                                </div>

                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <div class="flex-1">
                                        <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip"
                                               class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                        <p class="text-xs text-gray-400 mt-0.5">Adjuntar guía o rúbrica (PDF, Word, PPT, ZIP — máx. 20 MB)</p>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="showForm = false"
                                            class="px-4 py-2 bg-white border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-5 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Crear tarea
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-primary-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 mb-1">El curso no tiene semanas aún</h3>
            <p class="text-xs text-gray-400">Usa el formulario de arriba para agregar la primera semana.</p>
        </div>
        @endforelse
    </div>

    {{-- ─── STUDENTS TAB ────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'students'" class="space-y-4" x-cloak>

        {{-- Search & enroll --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5"
             x-data="{
                query: '',
                results: [],
                loading: false,
                searchUrl: '{{ route('docente.courses.students.search', $course) }}',
                async search() {
                    if (this.query.length < 2) { this.results = []; return; }
                    this.loading = true;
                    const url = new URL(this.searchUrl);
                    url.searchParams.set('q', this.query);
                    const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } });
                    this.results = await res.json();
                    this.loading = false;
                }
             }">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">Matricular alumno</h3>
            </div>
            <div class="flex gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="query"
                           @input.debounce.400ms="search()"
                           class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent"
                           placeholder="Buscar por nombre, email o DNI…">
                    <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="w-4 h-4 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div x-show="results.length > 0" class="mt-2 border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                <template x-for="student in results" :key="student.id">
                    <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 text-xs font-bold shrink-0"
                                 x-text="student.name.charAt(0).toUpperCase()"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900" x-text="student.name"></p>
                                <p class="text-xs text-gray-400" x-text="student.email"></p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('docente.courses.students.enroll', $course) }}">
                            @csrf
                            <input type="hidden" name="user_id" :value="student.id">
                            <button type="submit" class="px-3 py-1.5 bg-primary-600 text-white text-xs font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                                Matricular
                            </button>
                        </form>
                    </div>
                </template>
            </div>
            <p x-show="query.length >= 2 && results.length === 0 && !loading" class="mt-2 text-xs text-gray-400 text-center py-3">
                No se encontraron alumnos disponibles para matricular.
            </p>
        </div>

        {{-- Enrolled students --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-900">Alumnos matriculados</h3>
                </div>
                <span class="text-xs font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-full">
                    {{ $course->students->count() }} alumnos
                </span>
            </div>
            @if($course->students->isEmpty())
            <div class="p-10 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">Sin alumnos matriculados</p>
                <p class="text-xs text-gray-400 mt-0.5">Busca alumnos arriba para matricularlos.</p>
            </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($course->students as $student)
                @php $enrollment = $course->enrollments->firstWhere('user_id', $student->id); @endphp
                <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50/60 transition-colors">
                    <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-sm font-bold shrink-0">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $student->name }}</p>
                        <p class="text-xs text-gray-400">{{ $student->email }}</p>
                    </div>
                    @if($student->alumnoProfile?->dni)
                    <span class="text-xs text-gray-400 font-mono">{{ $student->alumnoProfile->dni }}</span>
                    @endif
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $enrollment && $enrollment->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $enrollment && $enrollment->status === 'active' ? 'Activo' : 'Baja' }}
                    </span>
                    @if($enrollment && $enrollment->status === 'active')
                    <form method="POST" action="{{ route('docente.courses.students.unenroll', [$course, $student]) }}"
                          onsubmit="return confirm('¿Dar de baja a {{ $student->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Dar de baja">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function courseManager() {
    return {
        activeTab: 'content',
    };
}
</script>
@endpush
