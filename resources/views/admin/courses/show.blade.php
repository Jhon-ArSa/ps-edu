@extends('layouts.app')

@section('title', $course->name)

@section('breadcrumb')
    <a href="{{ route('admin.courses.index') }}" class="hover:text-primary-600">Cursos</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">{{ $course->name }}</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header card with gradient --}}
    <div class="relative rounded-2xl overflow-hidden animate-fade-in-up
                {{ $course->status === 'active' ? 'bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800' : 'bg-gradient-to-br from-gray-500 via-gray-600 to-gray-700' }}">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-56 h-56 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative px-7 py-6 text-white">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 flex-wrap mb-2">
                        <h1 class="text-2xl font-extrabold tracking-tight">{{ $course->name }}</h1>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm">
                            <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-300 animate-pulse' : 'bg-gray-300' }}"></span>
                            {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <p class="text-white/60 text-sm font-mono">{{ $course->code }}</p>
                    @if($course->description)
                        <p class="text-white/70 text-sm mt-2 max-w-xl">{{ $course->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.courses.edit', $course) }}"
                       class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 backdrop-blur-sm text-white text-sm font-semibold px-4 py-2.5 rounded-xl border border-white/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar
                    </a>
                </div>
            </div>

            {{-- Info pills --}}
            <div class="flex flex-wrap gap-3 mt-5 pt-5 border-t border-white/15">
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Docente</p>
                        <p class="text-sm font-semibold">{{ $course->teacher->name ?? '—' }}</p>
                    </div>
                </div>
                @if($course->programBelongs)
                <a href="{{ route('admin.programs.show', $course->programBelongs) }}" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10 transition-colors">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Programa</p>
                        <p class="text-sm font-semibold">{{ $course->programBelongs->name }}</p>
                    </div>
                </a>
                @elseif($course->program)
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Programa</p>
                        <p class="text-sm font-semibold">{{ $course->program }}</p>
                    </div>
                </div>
                @endif
                @if($course->cycle || $course->semester)
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Ciclo / Período</p>
                        <p class="text-sm font-semibold">
                            {{ $course->cycle ? 'Ciclo '.$course->cycle : '' }}{{ $course->cycle && $course->semester ? ' — ' : '' }}{{ $course->semester ? 'Sem. '.$course->semester : '' }}
                        </p>
                    </div>
                </div>
                @endif
                @if($course->year)
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.7"/><path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Año</p>
                        <p class="text-sm font-semibold">{{ $course->year }}</p>
                    </div>
                </div>
                @endif
                @if($course->semesterPeriod)
                <a href="{{ route('admin.semesters.show', $course->semesterPeriod) }}" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-white/10 transition-colors">
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.7"/><path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <div>
                        <p class="text-[10px] text-white/50 uppercase tracking-wider">Semestre académico</p>
                        <p class="text-sm font-semibold">{{ $course->semesterPeriod->name }}
                            @if($course->semesterPeriod->is_active) <span class="text-emerald-300 text-[10px]">● Activo</span> @endif
                        </p>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up delay-1">
        <div class="stat-card stat-card-blue group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Alumnos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $course->enrollments->where('status','active')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">matriculados activos</p>
            </div>
        </div>
        <div class="stat-card stat-card-emerald group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Semanas</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $course->weeks->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.7"/><path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.7" stroke-linecap="round"/></svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">programadas</p>
            </div>
        </div>
        <div class="stat-card stat-card-violet group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Materiales</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $course->weeks->sum(fn($w) => $w->materials->count()) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-violet-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">archivos subidos</p>
            </div>
        </div>
        <div class="stat-card stat-card-amber group">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Bajas</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $course->enrollments->where('status','dropped')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">retiros registrados</p>
            </div>
        </div>
    </div>

    {{-- Docente card --}}
    @if($course->teacher)
    <div class="card animate-fade-in-up delay-2">
        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100">
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h2 class="text-sm font-bold text-gray-800">Docente responsable</h2>
        </div>
        <div class="px-6 py-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/20 shrink-0">
                    @if($course->teacher->avatar)
                        <img src="{{ $course->teacher->avatar_url }}" class="w-full h-full rounded-2xl object-cover">
                    @else
                        <span class="text-white text-lg font-bold">{{ strtoupper(substr($course->teacher->name, 0, 2)) }}</span>
                    @endif
                </div>
                <div>
                    <p class="text-base font-bold text-gray-900">{{ $course->teacher->name }}</p>
                    <p class="text-sm text-gray-500">{{ $course->teacher->email }}</p>
                    @if($course->teacher->phone)
                    <p class="text-sm text-gray-400 mt-0.5">{{ $course->teacher->phone }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Students enrolled --}}
    <div class="card animate-fade-in-up delay-3">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Alumnos matriculados</h2>
                    <p class="text-xs text-gray-400">{{ $course->enrollments->where('status','active')->count() }} activos · {{ $course->enrollments->where('status','dropped')->count() }} retirados</p>
                </div>
            </div>
            <a href="{{ route('admin.courses.edit', $course) }}#students"
               class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Gestionar alumnos
            </a>
        </div>
        @if($course->enrollments->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Alumno</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Matriculado</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($course->enrollments->sortByDesc(fn($e) => $e->status === 'active') as $enrollment)
                    <tr class="hover:bg-gray-50 transition-colors {{ $enrollment->status === 'dropped' ? 'opacity-50' : '' }}">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center shrink-0">
                                    @if($enrollment->student->avatar)
                                        <img src="{{ $enrollment->student->avatar_url }}" class="w-full h-full rounded-lg object-cover">
                                    @else
                                        <span class="text-blue-600 text-[10px] font-bold">{{ strtoupper(substr($enrollment->student->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $enrollment->student->name }}</p>
                                    @if($enrollment->student->dni)
                                    <p class="text-xs text-gray-400">DNI: {{ $enrollment->student->dni }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-500 hidden md:table-cell">{{ $enrollment->student->email }}</td>
                        <td class="px-5 py-3 text-gray-400 hidden lg:table-cell">{{ $enrollment->enrolled_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $enrollment->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $enrollment->status === 'active' ? 'bg-emerald-500' : 'bg-red-400' }}"></span>
                                {{ $enrollment->status === 'active' ? 'Activo' : 'Baja' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-12 text-center">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
            </div>
            <p class="text-sm text-gray-500 font-medium">No hay alumnos matriculados</p>
            <p class="text-xs text-gray-400 mt-1">Edite el curso para agregar alumnos a la matrícula.</p>
        </div>
        @endif
    </div>

</div>
@endsection
