@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="space-y-6 max-w-7xl">

    {{-- Welcome --}}
    <div class="animate-fade-in-up">
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Bienvenido, {{ auth()->user()->name }}</h1>
        <p class="text-gray-400 text-sm mt-1">Panel docente — {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="stat-card stat-card-emerald group animate-fade-in-up delay-1">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Mis cursos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $courses->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/25 group-hover:shadow-emerald-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-blue group animate-fade-in-up delay-2">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Alumnos activos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $courses->sum('active_students') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/25 group-hover:shadow-blue-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-violet group col-span-2 lg:col-span-1 animate-fade-in-up delay-3">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Semanas configuradas</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $courses->sum('weeks_count') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-violet-500/25 group-hover:shadow-violet-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up delay-4">

        {{-- My courses --}}
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-gray-800">Mis cursos</h2>
                <a href="{{ route('docente.courses.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($courses as $course)
                <a href="{{ route('docente.courses.show', $course) }}"
                   class="course-card group">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 truncate group-hover:text-primary-700 transition-colors">{{ $course->name }}</p>
                                <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $course->code }}</p>
                            </div>
                            <span class="badge-{{ $course->status === 'active' ? 'green' : 'gray' }} shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full {{ $course->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <div class="flex gap-4 mt-3 text-xs text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z"/></svg>
                                {{ $course->active_students }} alumnos
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $course->weeks_count }} semanas
                            </span>
                            @if($course->semester)
                            <span class="flex items-center gap-1">Sem. {{ $course->semester }} - {{ $course->year }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                @empty
                <div class="card">
                    <div class="p-10 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                            </svg>
                        </div>
                        <p class="text-gray-400 text-sm">No hay cursos asignados.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Announcements --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-gray-800">Comunicados recientes</h2>
                <a href="{{ route('docente.intranet') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($latestAnnouncements as $ann)
                <div class="card hover:-translate-y-0.5 transition-all duration-200">
                    <div class="p-5">
                        <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $ann->title }}</p>
                        <p class="text-xs text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($ann->content), 80) }}</p>
                        <p class="text-[11px] text-gray-300 mt-2 font-medium">{{ $ann->published_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="card">
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159"/>
                            </svg>
                        </div>
                        <p class="text-gray-400 text-sm">Sin comunicados nuevos.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
