@extends('layouts.app')

@section('title', 'Mis Cursos')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Mis Cursos</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header con filtro --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Mis Cursos</h1>
            <p class="text-sm text-gray-500">{{ $allCourses->count() }} cursos asignados en {{ $coursesByProgram->count() }} {{ $coursesByProgram->count() === 1 ? 'programa' : 'programas' }}</p>
        </div>

        @if($programs->count() > 1)
        <form method="GET" action="{{ route('docente.courses.index') }}" class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600">Filtrar por programa:</label>
            <select name="program_id" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-400 min-w-[200px]">
                <option value="">Todos los programas</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                        {{ $program->name }}
                    </option>
                @endforeach
            </select>
            @if(request('program_id'))
            <a href="{{ route('docente.courses.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Limpiar</a>
            @endif
        </form>
        @endif
    </div>

    @if($coursesByProgram->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-gray-500 font-medium">No tiene cursos asignados</p>
        <p class="text-gray-400 text-sm mt-1">Contacte al administrador para que le asigne cursos.</p>
    </div>
    @else

    {{-- Cursos agrupados por programa --}}
    <div class="space-y-8">
        @foreach($coursesByProgram as $programData)
        <div class="space-y-4">
            {{-- Program header --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/25 shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-bold text-gray-900">{{ $programData['program_name'] }}</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            @if($programData['program_code'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-100 text-primary-700">
                                {{ $programData['program_code'] }}
                            </span>
                            @endif
                            @if($programData['degree_type'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-violet-100 text-violet-700">
                                {{ $programData['degree_type'] }}
                            </span>
                            @endif
                            <span class="text-sm text-gray-500">
                                {{ $programData['courses']->count() }} {{ $programData['courses']->count() === 1 ? 'curso' : 'cursos' }}
                            </span>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-2">
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ $programData['courses']->sum('active_students') }}</p>
                            <p class="text-xs text-gray-500">alumnos</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Courses grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($programData['courses'] as $course)
                @php
                    $palettes = [
                        ['bg' => '#fde8d8', 'ring' => '#f97316'],
                        ['bg' => '#d4f0e8', 'ring' => '#10b981'],
                        ['bg' => '#e8d8f5', 'ring' => '#8b5cf6'],
                        ['bg' => '#d8eaf5', 'ring' => '#3b82f6'],
                        ['bg' => '#f5d8e8', 'ring' => '#ec4899'],
                        ['bg' => '#eaf5d8', 'ring' => '#84cc16'],
                    ];
                    $p = $palettes[$loop->index % count($palettes)];
                @endphp
                <a href="{{ route('docente.courses.show', $course) }}"
                   class="group relative flex flex-col rounded-2xl p-5 overflow-hidden transition-all duration-200 hover:shadow-2xl hover:-translate-y-1"
                   style="background-color: {{ $p['bg'] }};">

                    {{-- Top: semester / code + book icon --}}
                    <div class="flex items-center justify-between mb-4">
                        @if($course->semesterPeriod)
                        <span class="text-xs font-semibold text-gray-500">{{ $course->semesterPeriod->name }}</span>
                        @elseif($course->semester && $course->year)
                        <span class="text-xs font-semibold text-gray-500">Sem. {{ $course->semester }} &middot; {{ $course->year }}</span>
                        @else
                        <span class="text-xs font-semibold text-gray-500 font-mono tracking-wide">{{ $course->code }}</span>
                        @endif
                        <div class="w-9 h-9 rounded-xl bg-white/70 flex items-center justify-center backdrop-blur-sm shadow-sm">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Course name + letter avatar --}}
                    <div class="flex items-start justify-between gap-3 mb-4 flex-1">
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] font-bold text-gray-400 mb-1 font-mono tracking-wider uppercase">{{ $course->code }}</p>
                            <h3 class="text-lg font-extrabold text-gray-900 leading-snug line-clamp-2">{{ $course->name }}</h3>
                            @if($course->description)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-1 leading-relaxed">{{ $course->description }}</p>
                            @endif
                        </div>
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 text-xl font-extrabold text-white shadow-md"
                             style="background-color: {{ $p['ring'] }};">
                            {{ strtoupper(mb_substr($course->name, 0, 1)) }}
                        </div>
                    </div>

                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-1.5 mb-5">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/70 border border-white/80 text-gray-700">
                            {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                        </span>
                        @if($course->cycle)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/70 border border-white/80 text-gray-700">
                            Ciclo {{ $course->cycle }}
                        </span>
                        @endif
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/70 border border-white/80 text-gray-700">
                            {{ $course->weeks_count }} {{ $course->weeks_count === 1 ? 'semana' : 'semanas' }}
                        </span>
                    </div>

                    {{-- Bottom: alumnos + ver button --}}
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-base font-extrabold text-gray-900">{{ $course->active_students }} alumnos</p>
                        </div>
                        <span class="px-4 py-2 bg-gray-900 text-white text-xs font-bold rounded-full group-hover:bg-primary-700 transition-colors shrink-0">
                            Ver curso
                        </span>
                    </div>

                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    @endif

</div>
@endsection
