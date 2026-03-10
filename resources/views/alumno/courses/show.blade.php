@extends('layouts.app')

@section('title', $course->name)

@section('breadcrumb')
    <a href="{{ route('alumno.dashboard') }}" class="hover:text-primary-600">Mis Cursos</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">{{ $course->name }}</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Course header --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h1 class="text-xl font-bold text-gray-900">{{ $course->name }}</h1>
        <p class="text-sm text-gray-400 font-mono mt-0.5">{{ $course->code }}</p>

        <div class="flex flex-wrap gap-4 mt-4 text-sm text-gray-500">
            @if($course->teacher)
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ $course->teacher->name }}
            </div>
            @endif
            @if($course->program)
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ $course->program }}
            </div>
            @endif
            @if($course->semester)
            <div>Semestre {{ $course->semester }} — {{ $course->year }}</div>
            @endif
        </div>

        @if($course->description)
        <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ $course->description }}</p>
        @endif

        {{-- Acceso rápido a calificaciones --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <a href="{{ route('alumno.grades.show', $course) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h4"/>
                </svg>
                Ver mis calificaciones
            </a>
        </div>
    </div>

    {{-- Weeks & Materials --}}
    @if($course->weeks->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-10 text-center">
        <p class="text-gray-400">El docente aún no ha subido materiales para este curso.</p>
    </div>
    @else
    @foreach($course->weeks->sortBy('number') as $week)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden"
         x-data="{ expanded: {{ $loop->first ? 'true' : 'false' }} }">

        {{-- Week header --}}
        <button @click="expanded = !expanded"
                class="w-full flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-colors text-left">
            <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <div>
                <span class="text-xs font-bold text-primary-600 uppercase tracking-widest">Semana {{ $week->number }}</span>
                @if($week->title)
                <span class="ml-2 text-sm font-semibold text-gray-900">{{ $week->title }}</span>
                @endif
            </div>
            <span class="ml-auto text-xs text-gray-400">{{ $week->materials->count() }} material(es)</span>
        </button>

        {{-- Materials --}}
        <div x-show="expanded" x-transition:enter="transition-all duration-200">
            @if($week->description)
            <p class="px-5 py-3 text-sm text-gray-500 border-t border-gray-50 border-b">{{ $week->description }}</p>
            @endif

            @if($week->materials->isEmpty())
            <p class="px-5 py-4 text-xs text-gray-400 text-center border-t border-gray-50">Sin materiales en esta semana.</p>
            @else
            <div class="divide-y divide-gray-50 border-t border-gray-100">
                @foreach($week->materials->sortBy('order') as $material)
                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                    <span class="text-xl shrink-0">{{ $material->icon }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $material->title }}</p>
                        @if($material->description)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $material->description }}</p>
                        @endif

                        <div class="mt-1.5">
                            @if($material->type === 'file')
                                <a href="{{ $material->download_url }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs text-primary-600 hover:text-primary-800 font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Descargar archivo
                                </a>
                            @elseif($material->type === 'video')
                                <a href="{{ $material->url }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs text-primary-600 hover:text-primary-800 font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Ver video
                                </a>
                            @elseif($material->type === 'link')
                                <a href="{{ $material->url }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs text-primary-600 hover:text-primary-800 font-medium truncate max-w-sm">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Abrir enlace
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endforeach
    @endif

</div>
@endsection
