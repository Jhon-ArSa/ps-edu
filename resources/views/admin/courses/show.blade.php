@extends('layouts.app')

@section('title', $course->name)

@section('breadcrumb')
    <a href="{{ route('admin.courses.index') }}" class="hover:text-primary-600">Cursos</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">{{ $course->name }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <h1 class="text-xl font-bold text-gray-900">{{ $course->name }}</h1>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $course->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <p class="text-gray-400 text-sm font-mono">{{ $course->code }}</p>
                @if($course->description)
                    <p class="text-gray-600 text-sm mt-2">{{ $course->description }}</p>
                @endif
            </div>
            <a href="{{ route('admin.courses.edit', $course) }}"
               class="shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-primary-50 hover:bg-primary-100 text-primary-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
        </div>

        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-gray-100 text-sm">
            <div>
                <dt class="text-gray-400">Docente</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $course->teacher->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Programa</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $course->program ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Ciclo / Semestre</dt>
                <dd class="font-medium text-gray-900 mt-0.5">
                    {{ $course->cycle ? 'Ciclo '.$course->cycle : '—' }}
                    {{ $course->semester ? '— Sem. '.$course->semester : '' }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Año</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $course->year ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $course->enrollments->where('status','active')->count() }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Alumnos matriculados</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $course->weeks->count() }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Semanas</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $course->weeks->sum(fn($w) => $w->materials->count()) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Materiales</p>
        </div>
    </div>

    {{-- Students enrolled --}}
    @if($course->enrollments->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Alumnos matriculados</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($course->enrollments as $enrollment)
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 text-xs font-bold shrink-0">
                    {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $enrollment->student->name }}</p>
                    <p class="text-xs text-gray-400">Matriculado el {{ $enrollment->enrolled_at->format('d/m/Y') }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $enrollment->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $enrollment->status === 'active' ? 'Activo' : 'Baja' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
