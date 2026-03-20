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
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $materialStats['total'] }}</p>
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
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden xl:table-cell">Año de ingreso</th>
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
                        <td class="px-5 py-3 hidden xl:table-cell">
                            @if($enrollment->student->alumnoProfile?->promotion_year)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-200">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.7"/><path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.7" stroke-linecap="round"/></svg>
                                    <span class="text-sm font-bold text-indigo-700">{{ $enrollment->student->alumnoProfile->promotion_year }}</span>
                                </span>
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
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

    {{-- Contenido del Aula Virtual --}}
    <div class="card animate-fade-in-up delay-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/25">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Contenido del Aula Virtual</h2>
                    <p class="text-xs text-gray-400">Materiales subidos por el docente</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.courses.materials.csv', $course) }}"
                   class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-all shadow-sm hover:shadow-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar CSV
                </a>
                <a href="{{ route('admin.courses.materials.print', $course) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-all shadow-sm hover:shadow-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimir/PDF
                </a>
            </div>
        </div>

        {{-- Stats de materiales por tipo con íconos --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 p-6 bg-gradient-to-br from-violet-50/30 via-blue-50/20 to-emerald-50/30">
            <div class="bg-white rounded-xl border-2 border-gray-100 p-4 hover:border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1.5">{{ $materialStats['total'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">recursos</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg shadow-gray-500/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border-2 border-blue-100 p-4 hover:border-blue-200 hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Archivos</p>
                        <p class="text-3xl font-extrabold text-blue-600 mt-1.5">{{ $materialStats['file'] }}</p>
                        <p class="text-xs text-blue-500 mt-1">documentos</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border-2 border-emerald-100 p-4 hover:border-emerald-200 hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Enlaces</p>
                        <p class="text-3xl font-extrabold text-emerald-600 mt-1.5">{{ $materialStats['link'] }}</p>
                        <p class="text-xs text-emerald-500 mt-1">links</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border-2 border-red-100 p-4 hover:border-red-200 hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-400 uppercase tracking-wider">Videos</p>
                        <p class="text-3xl font-extrabold text-red-600 mt-1.5">{{ $materialStats['video'] }}</p>
                        <p class="text-xs text-red-500 mt-1">multimedia</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros con mejor diseño --}}
        <div class="px-6 py-4 bg-white border-b border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <label class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtrar:
                </label>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.courses.show', $course) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ !$typeFilter ? 'bg-gray-900 text-white shadow-lg shadow-gray-900/20' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 hover:border-gray-300' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Todos
                    </a>
                    <a href="{{ route('admin.courses.show', ['course' => $course, 'type_filter' => 'file']) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $typeFilter === 'file' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'bg-white text-gray-600 hover:bg-blue-50 border border-blue-200 hover:border-blue-300 hover:text-blue-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Archivos ({{ $materialStats['file'] }})
                    </a>
                    <a href="{{ route('admin.courses.show', ['course' => $course, 'type_filter' => 'link']) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $typeFilter === 'link' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'bg-white text-gray-600 hover:bg-emerald-50 border border-emerald-200 hover:border-emerald-300 hover:text-emerald-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        Enlaces ({{ $materialStats['link'] }})
                    </a>
                    <a href="{{ route('admin.courses.show', ['course' => $course, 'type_filter' => 'video']) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $typeFilter === 'video' ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-white text-gray-600 hover:bg-red-50 border border-red-200 hover:border-red-300 hover:text-red-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Videos ({{ $materialStats['video'] }})
                    </a>
                </div>
                @if($typeFilter)
                <button onclick="window.location.href='{{ route('admin.courses.show', $course) }}'"
                        class="ml-auto inline-flex items-center gap-1 text-xs text-gray-500 hover:text-red-600 font-medium transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Limpiar
                </button>
                @endif
            </div>
        </div>

        {{-- Lista de semanas con mejor diseño --}}
        @if($weeks->isEmpty())
            <div class="py-16 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm text-gray-600 font-semibold">
                    @if($typeFilter)
                        No hay materiales del tipo seleccionado
                    @else
                        El docente aún no ha subido contenido
                    @endif
                </p>
                <p class="text-xs text-gray-400 mt-1.5">
                    @if($typeFilter)
                        Prueba con otro filtro o limpia la selección
                    @else
                        Los materiales aparecerán aquí cuando el docente comience a crear semanas
                    @endif
                </p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($weeks->sortBy('number') as $week)
                <div class="px-6 py-5 hover:bg-gradient-to-r hover:from-violet-50/30 hover:to-transparent transition-colors" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                    {{-- Semana header mejorado --}}
                    <button @click="open = !open" class="w-full flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center shrink-0 shadow-lg shadow-violet-500/30 group-hover:shadow-violet-500/50 transition-all">
                                    <span class="text-white font-bold text-base">{{ $week->number }}</span>
                                </div>
                                @if($week->materials->count() > 0)
                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-amber-500 rounded-full border-2 border-white flex items-center justify-center shadow-lg">
                                    <span class="text-white text-[9px] font-bold">{{ $week->materials->count() }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-bold text-gray-900 group-hover:text-violet-700 transition-colors">Semana {{ $week->number }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $week->title ?? 'Sin título asignado' }}</p>
                                @if($week->description)
                                <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ $week->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-violet-100 text-violet-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                {{ $week->materials->count() }}
                            </span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-violet-500 transition-all" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>

                    {{-- Materiales de la semana mejorados --}}
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="mt-4 space-y-2.5 pl-16">
                        @forelse($week->materials->sortBy('order') as $material)
                        <div class="relative flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-200 hover:border-{{ $material->type === 'file' ? 'blue' : ($material->type === 'link' ? 'emerald' : 'red') }}-300 hover:shadow-lg transition-all group">
                            {{-- Indicador de tipo con ícono SVG --}}
                            @if($material->type === 'file')
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20 group-hover:shadow-lg group-hover:shadow-blue-500/30 transition-all">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            @elseif($material->type === 'link')
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/20 group-hover:shadow-lg group-hover:shadow-emerald-500/30 transition-all">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </div>
                            @else
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shrink-0 shadow-md shadow-red-500/20 group-hover:shadow-lg group-hover:shadow-red-500/30 transition-all">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            @endif

                            {{-- Contenido del material --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap mb-1">
                                            <p class="text-sm font-bold text-gray-900 group-hover:text-{{ $material->type === 'file' ? 'blue' : ($material->type === 'link' ? 'emerald' : 'red') }}-700 transition-colors">{{ $material->title }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                                {{ $material->type === 'file' ? 'bg-blue-100 text-blue-700 border border-blue-200' : ($material->type === 'link' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200') }}">
                                                {{ $material->type === 'file' ? 'Archivo' : ($material->type === 'link' ? 'Enlace' : 'Video') }}
                                            </span>
                                        </div>
                                        @if($material->description)
                                        <p class="text-xs text-gray-500 leading-relaxed mb-2">{{ $material->description }}</p>
                                        @endif
                                        <div class="flex items-center gap-3 text-xs text-gray-400">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $material->created_at->format('d/m/Y') }}
                                            </span>
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $material->created_at->format('H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Botón de acción más prominente --}}
                            <div class="shrink-0 flex flex-col gap-2">
                                @if($material->type === 'file')
                                    <a href="{{ $material->download_url }}" target="_blank" download
                                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-md hover:shadow-lg hover:shadow-blue-500/30 group-hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Descargar
                                    </a>
                                @else
                                    <a href="{{ $material->url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r {{ $material->type === 'link' ? 'from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800' : 'from-red-600 to-red-700 hover:from-red-700 hover:to-red-800' }} text-white text-xs font-bold rounded-lg transition-all shadow-md hover:shadow-lg hover:shadow-{{ $material->type === 'link' ? 'emerald' : 'red' }}-500/30 group-hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Abrir
                                    </a>
                                @endif
                            </div>

                            {{-- Decoración sutil --}}
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br {{ $material->type === 'file' ? 'from-blue-500/5' : ($material->type === 'link' ? 'from-emerald-500/5' : 'from-red-500/5') }} to-transparent rounded-br-xl pointer-events-none"></div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="text-xs text-gray-400 font-medium">Sin materiales en esta semana</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
