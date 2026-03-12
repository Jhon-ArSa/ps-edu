@extends('layouts.app')

@section('title', 'Mis Cursos')

@section('content')
<div class="space-y-6">

    {{-- Cabecera --}}
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Mis Cursos</h1>
        <p class="text-gray-400 text-sm mt-1">{{ $enrollments->count() }} curso{{ $enrollments->count() !== 1 ? 's' : '' }} activo{{ $enrollments->count() !== 1 ? 's' : '' }}</p>
    </div>

    @if($enrollments->isEmpty())
        <div class="card">
            <div class="p-16 text-center">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm font-medium">No estás matriculado en ningún curso activo.</p>
                <p class="text-gray-400 text-xs mt-1">Comunícate con el administrador para gestionar tu matrícula.</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($enrollments as $enrollment)
            @php $course = $enrollment->course; @endphp
            <a href="{{ route('alumno.courses.show', $course) }}"
               class="course-card group flex flex-col">
                <div class="p-5 flex-1">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 leading-snug group-hover:text-primary-700 transition-colors">{{ $course->name }}</p>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $course->code }}</p>
                        </div>
                        <div class="w-9 h-9 bg-primary-50 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-primary-100 transition-colors">
                            <svg class="w-[18px] h-[18px] text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>

                    @if($course->description)
                        <p class="text-xs text-gray-500 leading-relaxed line-clamp-2 mb-3">{{ $course->description }}</p>
                    @endif

                    <div class="flex flex-wrap gap-3 text-xs text-gray-400 mt-auto">
                        @if($course->teacher)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $course->teacher->name }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            {{ $course->weeks_count }} semana{{ $course->weeks_count !== 1 ? 's' : '' }}
                        </span>
                        @if($course->semester && $course->year)
                            <span>Sem. {{ $course->semester }} · {{ $course->year }}</span>
                        @endif
                    </div>
                </div>

                <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <span class="text-[11px] text-gray-400">Matriculado {{ $enrollment->enrolled_at?->diffForHumans() }}</span>
                </div>
            </a>
            @endforeach
        </div>
    @endif

</div>
@endsection
