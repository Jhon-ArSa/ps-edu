@extends('layouts.app')

@section('title', 'Mis Cursos')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Mis Cursos</span>
@endsection

@section('content')
<div class="space-y-4">

    <div>
        <h1 class="text-xl font-bold text-gray-900">Mis Cursos</h1>
        <p class="text-sm text-gray-500">{{ $courses->total() }} cursos asignados</p>
    </div>

    @if($courses->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-gray-500 font-medium">No tiene cursos asignados</p>
        <p class="text-gray-400 text-sm mt-1">Contacte al administrador para que le asigne cursos.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($courses as $course)
        <a href="{{ route('docente.courses.show', $course) }}"
           class="bg-white rounded-xl border border-gray-200 p-5 hover:border-primary-300 hover:shadow-md transition-all block">
            <div class="flex items-start justify-between gap-2 mb-3">
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 leading-tight">{{ $course->name }}</h3>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $course->code }}</p>
                </div>
                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $course->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                </span>
            </div>

            @if($course->description)
            <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $course->description }}</p>
            @endif

            <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-gray-100">
                <div class="flex gap-3">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        {{ $course->active_students }} alumnos
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $course->weeks_count }} semanas
                    </span>
                </div>
                @if($course->semester)
                    <span>Sem. {{ $course->semester }} — {{ $course->year }}</span>
                @endif
            </div>
        </a>
        @endforeach
    </div>

    @if($courses->hasPages())
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        {{ $courses->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
