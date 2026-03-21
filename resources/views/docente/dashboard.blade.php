@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="space-y-6">

    {{-- Welcome hero EduBook-style --}}
    <div class="relative rounded-2xl overflow-hidden text-white shadow-2xl animate-fade-in-up"
         style="background: linear-gradient(135deg, #0c1a3a 0%, #172554 45%, #1e3a8a 100%);">
        {{-- Geometric shapes --}}
        <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full" style="background: rgba(96,165,250,0.15);"></div>
        <div class="absolute top-4 right-12 w-10 h-10 rounded-xl rotate-12" style="background: linear-gradient(135deg, #10b981, #059669); opacity: 0.7;"></div>
        <div class="absolute top-14 right-5 w-8 h-8" style="background: #f59e0b; clip-path: polygon(50% 0%, 0% 100%, 100% 100%); opacity: 0.6;"></div>
        <div class="absolute top-2 right-32 w-6 h-6 rounded-full" style="background: #a78bfa; opacity: 0.55;"></div>
        <div class="absolute bottom-4 right-14 w-9 h-9 rounded-lg rotate-45" style="background: #06b6d4; opacity: 0.4;"></div>
        <div class="absolute -bottom-8 right-2 w-40 h-40 rounded-full" style="background: rgba(167,139,250,0.08);"></div>

        <div class="relative px-8 py-7">
            <p class="text-emerald-300 text-xs font-semibold uppercase tracking-widest mb-1">Portal Docente</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Bienvenido, {{ explode(' ', auth()->user()->name)[0] }}</h1>
            <p class="text-blue-300/70 text-sm mt-1.5">{{ now()->isoFormat('dddd D [de] MMMM [de] YYYY') }}</p>
            <a href="{{ route('docente.courses.index') }}"
               class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white/12 hover:bg-white/20 backdrop-blur-sm rounded-xl text-sm font-semibold text-white border border-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/></svg>
                Mis cursos →
            </a>
        </div>
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

        {{-- My courses grouped by program --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-800">Mis cursos por programa</h2>
                <a href="{{ route('docente.courses.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">Ver todos</a>
            </div>

            @forelse($coursesByProgram as $programData)
            <div class="space-y-3">
                {{-- Program header --}}
                <div class="flex items-center gap-3 px-1">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/25">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 truncate">{{ $programData['program_name'] }}</h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            @if($programData['program_code'])
                            <span class="text-xs text-gray-400 font-mono">{{ $programData['program_code'] }}</span>
                            @endif
                            @if($programData['degree_type'])
                            <span class="text-xs text-primary-600 font-medium">{{ $programData['degree_type'] }}</span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $programData['courses']->count() }} {{ $programData['courses']->count() === 1 ? 'curso' : 'cursos' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Courses grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($programData['courses'] as $course)
                    @php
                        $palettes = [
                            ['bg' => '#fde8d8', 'ring' => '#f97316'],
                            ['bg' => '#d4f0e8', 'ring' => '#10b981'],
                            ['bg' => '#e8d8f5', 'ring' => '#8b5cf6'],
                            ['bg' => '#d8eaf5', 'ring' => '#3b82f6'],
                            ['bg' => '#f5d8e8', 'ring' => '#ec4899'],
                            ['bg' => '#eaf5d8', 'ring' => '#84cc16'],
                        ];
                        $p = $palettes[$loop->index % count($palettes)];
                    @endphp
                    <a href="{{ route('docente.courses.show', $course) }}"
                       class="group relative flex flex-col rounded-2xl p-5 overflow-hidden transition-all duration-200 hover:shadow-2xl hover:-translate-y-1"
                       style="background-color: {{ $p['bg'] }};">

                        {{-- Top: semester / code + book icon --}}
                        <div class="flex items-center justify-between mb-4">
                            @if($course->semester && $course->year)
                            <span class="text-xs font-semibold text-gray-500">Sem. {{ $course->semester }} &middot; {{ $course->year }}</span>
                            @else
                            <span class="text-xs font-semibold text-gray-500 font-mono tracking-wide">{{ $course->code }}</span>
                            @endif
                            <div class="w-9 h-9 rounded-xl bg-white/70 flex items-center justify-center backdrop-blur-sm shadow-sm">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Course name + letter avatar --}}
                        <div class="flex items-start justify-between gap-3 mb-4 flex-1">
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-bold text-gray-400 mb-1 font-mono tracking-wider uppercase">{{ $course->code }}</p>
                                <h3 class="text-base font-extrabold text-gray-900 leading-snug line-clamp-2">{{ $course->name }}</h3>
                            </div>
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 text-lg font-extrabold text-white shadow-md"
                                 style="background-color: {{ $p['ring'] }};">
                                {{ strtoupper(mb_substr($course->name, 0, 1)) }}
                            </div>
                        </div>

                        {{-- Tags --}}
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/70 border border-white/80 text-gray-700">
                                {{ $course->status === 'active' ? 'Activo' : 'Inactivo' }}
                            </span>
                            @if($course->cycle)
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/70 border border-white/80 text-gray-700">
                                Ciclo {{ $course->cycle }}
                            </span>
                            @endif
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/70 border border-white/80 text-gray-700">
                                {{ $course->weeks_count }} semanas
                            </span>
                        </div>

                        {{-- Bottom: alumnos + ver button --}}
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-sm font-extrabold text-gray-900">{{ $course->active_students }} alumnos</p>
                                @if($course->semesterPeriod)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $course->semesterPeriod->name }}</p>
                                @endif
                            </div>
                            <span class="px-3 py-1.5 bg-gray-900 text-white text-xs font-bold rounded-full group-hover:bg-primary-700 transition-colors shrink-0">
                                Ver curso
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
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

        {{-- Announcements --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-gray-800">Comunicados recientes</h2>
                <a href="{{ route('docente.intranet') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($latestAnnouncements as $ann)
                <div class="card hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">
                    @if($ann->image_path)
                    <img src="{{ $ann->image_url }}" alt="{{ $ann->title }}" class="w-full h-28 object-cover">
                    @endif
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

{{-- Modal emergente para anuncios importantes --}}
@if($latestAnnouncements->count() > 0)
    <x-announcement-modal :announcement="$latestAnnouncements->first()" />
@endif

@endsection
