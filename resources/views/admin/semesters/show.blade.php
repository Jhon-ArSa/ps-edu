@extends('layouts.app')

@section('title', 'Semestre ' . $semester->name)

@section('breadcrumb')
    <a href="{{ route('admin.semesters.index') }}" class="hover:text-primary-600">Semestres</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">{{ $semester->name }}</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Semester Header Card --}}
    <div class="relative rounded-2xl overflow-hidden animate-fade-in-up
                {{ $semester->is_active ? 'bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-700' : ($semester->is_planned ? 'bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700' : 'bg-gradient-to-br from-gray-500 via-gray-600 to-gray-700') }}">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-56 h-56 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative px-7 py-6 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 shrink-0">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-3xl font-extrabold tracking-tight">{{ $semester->name }}</h1>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm">
                                {{ $semester->status_label }}
                            </span>
                        </div>
                        <p class="text-white/70 text-sm mt-1">{{ $semester->date_range }} · {{ $semester->duration_weeks }} semanas</p>
                        @if($semester->description)
                            <p class="text-white/60 text-sm mt-1">{{ $semester->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.semesters.edit', $semester) }}"
                       class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 backdrop-blur-sm text-white text-sm font-semibold px-4 py-2.5 rounded-xl border border-white/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar
                    </a>
                    @if($semester->is_planned)
                    <form method="POST" action="{{ route('admin.semesters.activate', $semester) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-white text-emerald-700 text-sm font-bold px-4 py-2.5 rounded-xl transition-all hover:bg-emerald-50 shadow-lg"
                                onclick="return confirm('¿Activar este semestre? Los semestres anteriores serán cerrados.')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Activar semestre
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Progress bar for active --}}
            @if($semester->is_active)
            <div class="mt-5 max-w-md">
                <div class="flex items-center justify-between text-xs text-white/70 mb-1.5">
                    <span>Progreso del semestre</span>
                    <span class="font-bold text-white">{{ $semester->progress_percent }}%</span>
                </div>
                <div class="h-2.5 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full transition-all duration-500" style="width: {{ $semester->progress_percent }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up delay-1">
        <div class="stat-card stat-card-emerald group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Cursos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['total_courses'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">{{ $stats['active_courses'] }} activos</p>
            </div>
        </div>
        <div class="stat-card stat-card-blue group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Alumnos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['total_students'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">matrículas activas</p>
            </div>
        </div>
        <div class="stat-card stat-card-violet group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Docentes</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $stats['total_teachers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-violet-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">asignados este semestre</p>
            </div>
        </div>
        <div class="stat-card stat-card-amber group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Duración</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $semester->duration_weeks }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">semanas</p>
            </div>
        </div>
    </div>

    {{-- Courses in this semester --}}
    <div class="card animate-fade-in-up delay-2">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Cursos del semestre</h2>
                    <p class="text-xs text-gray-400">{{ $courses->count() }} cursos asignados</p>
                </div>
            </div>
            <a href="{{ route('admin.courses.create') }}?semester_id={{ $semester->id }}"
               class="inline-flex items-center gap-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo curso
            </a>
        </div>

        @if($courses->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Curso</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Docente</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Alumnos</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($courses as $course)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-gray-900">{{ $course->name }}</p>
                            <p class="text-xs text-gray-400">{{ $course->code }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-gray-600 hidden md:table-cell">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center shrink-0">
                                    <span class="text-primary-600 text-[10px] font-bold">{{ strtoupper(substr($course->teacher->name ?? '?', 0, 1)) }}</span>
                                </div>
                                {{ $course->teacher->name ?? '—' }}
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                                {{ $course->students_count }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $course->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.courses.show', $course) }}" title="Ver"
                                   class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.courses.edit', $course) }}" title="Editar"
                                   class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-12 text-center">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <p class="text-sm text-gray-500 font-medium">Aún no hay cursos en este semestre</p>
            <p class="text-xs text-gray-400 mt-1">Crea cursos y asígnalos a este período académico.</p>
        </div>
        @endif
    </div>

</div>
@endsection
