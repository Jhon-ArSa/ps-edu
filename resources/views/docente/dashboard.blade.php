@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="space-y-6">

    {{-- Welcome --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Bienvenido, {{ auth()->user()->name }}</h1>
        <p class="text-gray-500 text-sm mt-1">Panel docente — {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Mis cursos</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $courses->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Alumnos activos</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $courses->sum('active_students') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 col-span-2 lg:col-span-1">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Semanas configuradas</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $courses->sum('weeks_count') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- My courses --}}
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-900">Mis cursos</h2>
                <a href="{{ route('docente.courses.index') }}" class="text-xs text-primary-600 hover:underline">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($courses as $course)
                <a href="{{ route('docente.courses.show', $course) }}"
                   class="block bg-white rounded-xl border border-gray-200 p-4 hover:border-primary-300 hover:shadow-sm transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $course->name }}</p>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $course->code }}</p>
                        </div>
                        <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $course->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="flex gap-4 mt-3 text-xs text-gray-500">
                        <span>{{ $course->active_students }} alumnos</span>
                        <span>{{ $course->weeks_count }} semanas</span>
                        @if($course->semester) <span>Sem. {{ $course->semester }} - {{ $course->year }}</span> @endif
                    </div>
                </a>
                @empty
                <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                    <p class="text-gray-400 text-sm">No hay cursos asignados.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Announcements --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-900">Comunicados recientes</h2>
                <a href="{{ route('docente.intranet') }}" class="text-xs text-primary-600 hover:underline">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($latestAnnouncements as $ann)
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <p class="text-sm font-medium text-gray-900 leading-tight">{{ $ann->title }}</p>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($ann->content), 80) }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $ann->published_at->diffForHumans() }}</p>
                </div>
                @empty
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <p class="text-gray-400 text-sm">Sin comunicados nuevos.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
