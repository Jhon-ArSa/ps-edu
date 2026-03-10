@extends('layouts.app')

@section('title', 'Matrículas')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 transition-colors">Dashboard</a>
    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-semibold text-gray-700">Matrículas</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Gestión de Matrículas</h1>
            <p class="text-sm text-gray-500 mt-0.5">Administre las matrículas de alumnos en los cursos</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg font-medium">
                {{ $enrollments->total() }} matrícula{{ $enrollments->total() !== 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.enrollments.index') }}"
          class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            {{-- Search --}}
            <div class="relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar alumno por nombre o correo..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-gray-50">
            </div>

            {{-- Course filter --}}
            <select name="course_id"
                    class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-gray-50">
                <option value="">Todos los cursos</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }} ({{ $course->code }})
                    </option>
                @endforeach
            </select>

            {{-- Status filter --}}
            <div class="flex gap-2">
                <select name="status"
                        class="flex-1 px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent bg-gray-50">
                    <option value="">Todos los estados</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
                <button type="submit"
                        class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-colors shrink-0">
                    Filtrar
                </button>
                @if(request()->hasAny(['search', 'course_id', 'status']))
                    <a href="{{ route('admin.enrollments.index') }}"
                       class="px-3 py-2.5 text-gray-500 hover:text-gray-700 text-sm font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shrink-0">
                        Limpiar
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @if($enrollments->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-semibold">No se encontraron matrículas</p>
                <p class="text-gray-400 text-sm mt-1">Intente con otros filtros de búsqueda</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider px-5 py-3.5">Alumno</th>
                            <th class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider px-4 py-3.5">Curso</th>
                            <th class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider px-4 py-3.5 hidden md:table-cell">Docente</th>
                            <th class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider px-4 py-3.5 hidden lg:table-cell">Fecha Matrícula</th>
                            <th class="text-center text-xs font-bold text-gray-500 uppercase tracking-wider px-4 py-3.5">Estado</th>
                            <th class="text-right text-xs font-bold text-gray-500 uppercase tracking-wider px-5 py-3.5">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            {{-- Student --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-100 overflow-hidden shrink-0">
                                        @if($enrollment->student?->avatar)
                                            <img src="{{ $enrollment->student->avatar_url }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-blue-600 text-sm font-bold">
                                                {{ strtoupper(substr($enrollment->student?->name ?? '?', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $enrollment->student?->name ?? '(eliminado)' }}
                                        </p>
                                        <p class="text-xs text-gray-400 truncate">
                                            {{ $enrollment->student?->email ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Course --}}
                            <td class="px-4 py-3.5">
                                <p class="text-sm font-medium text-gray-900 truncate max-w-40">
                                    {{ $enrollment->course?->name ?? '(eliminado)' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $enrollment->course?->code ?? '—' }}
                                </p>
                            </td>

                            {{-- Teacher --}}
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <p class="text-sm text-gray-600 truncate max-w-36">
                                    {{ $enrollment->course?->teacher?->name ?? '—' }}
                                </p>
                            </td>

                            {{-- Enrolled at --}}
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <p class="text-sm text-gray-600">
                                    {{ $enrollment->enrolled_at?->format('d/m/Y') ?? $enrollment->created_at->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ ($enrollment->enrolled_at ?? $enrollment->created_at)->diffForHumans() }}
                                </p>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $enrollment->status === 'active'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-red-100 text-red-600' }}">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        {{ $enrollment->status === 'active' ? 'bg-emerald-500' : 'bg-red-400' }}"></span>
                                    {{ $enrollment->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right">
                                <form method="POST"
                                      action="{{ route('admin.enrollments.toggle', $enrollment) }}"
                                      class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors
                                                {{ $enrollment->status === 'active'
                                                    ? 'border-red-200 text-red-600 hover:bg-red-50'
                                                    : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                        {{ $enrollment->status === 'active' ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($enrollments->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $enrollments->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
