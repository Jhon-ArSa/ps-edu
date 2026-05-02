@extends('layouts.app')

@section('title', 'Mi Aula')

@section('content')
<div class="space-y-6">

    {{-- Welcome hero EduBook-style --}}
    <div class="relative rounded-2xl overflow-hidden text-white shadow-2xl animate-fade-in-up"
         style="background: linear-gradient(135deg, #0c1a3a 0%, #172554 45%, #1e3a8a 100%);">
        {{-- Geometric shapes --}}
        <div class="absolute -top-6 -right-8 w-32 h-32 rounded-full" style="background: rgba(96,165,250,0.16);"></div>
        <div class="absolute top-3 right-12 w-10 h-10 rounded-xl rotate-6" style="background: linear-gradient(135deg, #f97316, #ef4444); opacity: 0.7;"></div>
        <div class="absolute top-14 right-5 w-8 h-8" style="background: #a78bfa; clip-path: polygon(50% 0%, 0% 100%, 100% 100%); opacity: 0.65;"></div>
        <div class="absolute top-2 right-32 w-5 h-5 rounded-full" style="background: #34d399; opacity: 0.6;"></div>
        <div class="absolute bottom-4 right-14 w-8 h-8 rounded-lg rotate-45" style="background: #fbbf24; opacity: 0.45;"></div>
        <div class="absolute -bottom-8 -right-2 w-40 h-40 rounded-full" style="background: rgba(96,165,250,0.08);"></div>

        <div class="relative px-8 py-7">
            <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-1">Mi Aula Virtual</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Bienvenido, {{ explode(' ', auth()->user()->name)[0] }}</h1>
            <p class="text-blue-300/70 text-sm mt-1.5">{{ now()->isoFormat('dddd D [de] MMMM [de] YYYY') }}</p>
            @if($enrollments->count() > 0)
            <p class="text-blue-200/80 text-xs mt-1">
                Matriculado en <strong class="text-white">{{ $enrollments->count() }}</strong> {{ $enrollments->count() === 1 ? 'curso' : 'cursos' }}
            </p>
            @endif
            <a href="{{ route('alumno.intranet') }}"
               class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white/12 hover:bg-white/20 backdrop-blur-sm rounded-xl text-sm font-semibold text-white border border-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9"/></svg>
                Ver intranet →
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="stat-card stat-card-emerald group animate-fade-in-up delay-1">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Cursos activos</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $enrollments->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/25 group-hover:shadow-emerald-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-orange group animate-fade-in-up delay-2">
            <div class="p-5 pt-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Comunicados</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-2 tracking-tight">{{ $latestAnnouncements->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-orange-500/25 group-hover:shadow-orange-500/40 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up delay-3">

        {{-- My courses --}}
        <div class="lg:col-span-2">
            <h2 class="text-sm font-bold text-gray-800 mb-3">Mis cursos</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($enrollments as $enrollment)
                @php
                    $course = $enrollment->course;
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
                <a href="{{ route('alumno.courses.show', $course) }}"
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
                        @if($course->cycle)
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/70 border border-white/80 text-gray-700">
                            Ciclo {{ $course->cycle }}
                        </span>
                        @endif
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/70 border border-white/80 text-gray-700">
                            {{ $course->weeks_count }} semanas
                        </span>
                    </div>

                    {{-- Bottom: teacher + button --}}
                    <div class="flex items-end justify-between">
                        <div>
                            @if($course->teacher)
                            <p class="text-sm font-extrabold text-gray-900">{{ explode(' ', $course->teacher->name)[0] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Docente</p>
                            @else
                            <p class="text-xs text-gray-400">Sin docente</p>
                            @endif
                        </div>
                        <span class="px-3 py-1.5 bg-gray-900 text-white text-xs font-bold rounded-full group-hover:bg-primary-700 transition-colors shrink-0">
                            Acceder
                        </span>
                    </div>
                </a>
                @empty
                <div class="col-span-full card">
                    <div class="p-10 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <p class="text-gray-400 text-sm">No está matriculado en ningún curso activo.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Announcements --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-gray-800">Comunicados</h2>
                <a href="{{ route('alumno.intranet') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">Ver todos</a>
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

{{-- Modales emergentes --}}
@foreach($popupAnnouncements as $popup)
    <x-announcement-modal :announcement="$popup" />
@endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ids = @json($popupAnnouncements->pluck('id'));
    const queue = ids.filter(id => !localStorage.getItem(`announcement_${id}_shown`) && !localStorage.getItem(`announcement_${id}_read`));
    let index = 0;
    function showNext() {
        if (index < queue.length) document.dispatchEvent(new CustomEvent(`show-popup-${queue[index++]}`));
    }
    document.addEventListener('popup-closed', () => setTimeout(showNext, 400));
    setTimeout(showNext, 800);
});
</script>
@endpush

@endsection
