@extends('layouts.app')

@section('title', 'Mi Aula')

@section('content')
<div class="space-y-6">

    {{-- Welcome --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Bienvenido, {{ auth()->user()->name }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cursos activos</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $enrollments->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Comunicados</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $latestAnnouncements->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- My courses --}}
        <div class="lg:col-span-2">
            <h2 class="text-sm font-semibold text-gray-900 mb-3">Mis cursos</h2>
            <div class="space-y-3">
                @forelse($enrollments as $enrollment)
                @php $course = $enrollment->course; @endphp
                <a href="{{ route('alumno.courses.show', $course) }}"
                   class="block bg-white rounded-xl border border-gray-200 p-4 hover:border-primary-300 hover:shadow-sm transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $course->name }}</p>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $course->code }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <div class="flex gap-4 mt-3 text-xs text-gray-500">
                        @if($course->teacher) <span>{{ $course->teacher->name }}</span> @endif
                        <span>{{ $course->weeks_count }} semanas</span>
                        @if($course->semester) <span>Sem. {{ $course->semester }} — {{ $course->year }}</span> @endif
                    </div>
                </a>
                @empty
                <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="text-gray-400 text-sm">No está matriculado en ningún curso activo.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Announcements --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-900">Comunicados</h2>
                <a href="{{ route('alumno.intranet') }}" class="text-xs text-primary-600 hover:underline">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($latestAnnouncements as $ann)
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-900 leading-tight">{{ $ann->title }}</p>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($ann->content), 80) }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $ann->published_at->diffForHumans() }}</p>
                </div>
                @empty
                <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                    <p class="text-gray-400 text-sm">Sin comunicados nuevos.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
