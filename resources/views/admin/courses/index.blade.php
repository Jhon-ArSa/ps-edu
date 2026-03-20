@extends('layouts.app')

@section('title', 'Cursos')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Cursos</span>
@endsection

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Gestión de Cursos</h1>
            <p class="text-sm text-gray-500">{{ $courses->total() }} cursos registrados</p>
        </div>
        <a href="{{ route('admin.courses.create') }}"
           class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo curso
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.courses.index') }}"
          class="flex flex-col sm:flex-row gap-3 bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                   placeholder="Buscar por nombre o código…">
        </div>
        <select name="status"
                class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-400">
            <option value="">Todos los estados</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Activos</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
        </select>
        <select name="semester_id"
                class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-400">
            <option value="">Todos los semestres</option>
            @foreach($semesters as $sem)
                <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
            @endforeach
        </select>
        <select name="program_id"
                class="px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-400">
            <option value="">Todos los programas</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">Filtrar</button>
        @if(request()->hasAny(['search', 'status', 'semester_id', 'program_id']))
            <a href="{{ route('admin.courses.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">Limpiar</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Curso</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Docente</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden xl:table-cell">Programa</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Semestre</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Ciclo / Sem.</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($courses as $course)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-900">{{ $course->name }}</p>
                            <p class="text-xs text-gray-400">{{ $course->code }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600 hidden md:table-cell">{{ $course->teacher->name ?? '—' }}</td>
                        <td class="px-5 py-3 hidden xl:table-cell">
                            @if($course->programBelongs)
                                <div class="flex flex-col gap-1">
                                    <a href="{{ route('admin.programs.show', $course->programBelongs) }}"
                                       class="text-sm font-medium text-gray-900 hover:text-primary-600 transition-colors line-clamp-1">
                                        {{ $course->programBelongs->name }}
                                    </a>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold w-fit
                                        {{ $course->programBelongs->degree_type === 'maestria' ? 'bg-blue-100 text-blue-700 border border-blue-200' :
                                           ($course->programBelongs->degree_type === 'doctorado' ? 'bg-purple-100 text-purple-700 border border-purple-200' :
                                           ($course->programBelongs->degree_type === 'segunda_especialidad' ? 'bg-orange-100 text-orange-700 border border-orange-200' : 'bg-teal-100 text-teal-700 border border-teal-200')) }}">
                                        @if($course->programBelongs->degree_type === 'maestria')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                        @elseif($course->programBelongs->degree_type === 'doctorado')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                        @elseif($course->programBelongs->degree_type === 'segunda_especialidad')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        @else
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        @endif
                                        {{ $course->programBelongs->degree_type_label }}
                                    </span>
                                </div>
                            @elseif($course->program)
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm text-gray-600 line-clamp-1">{{ $course->program }}</span>
                                    <span class="text-xs text-gray-400 italic">Sin programa asignado</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500 hidden lg:table-cell">
                            @if($course->semesterPeriod)
                                <a href="{{ route('admin.semesters.show', $course->semesterPeriod) }}" class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full {{ $course->semesterPeriod->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }} hover:underline">
                                    {{ $course->semesterPeriod->name }}
                                </a>
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500 hidden lg:table-cell">
                            @if($course->cycle)
                                Ciclo {{ $course->cycle }}
                                @if($course->semester) — Sem. {{ $course->semester }} @endif
                            @else —
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $course->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.courses.show', $course) }}" title="Ver"
                                   class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.courses.edit', $course) }}" title="Editar"
                                   class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                      data-confirm="¿Eliminar este curso?">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Eliminar"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            No se encontraron cursos con los filtros aplicados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($courses->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $courses->links() }}</div>
        @endif
    </div>

</div>
@endsection
