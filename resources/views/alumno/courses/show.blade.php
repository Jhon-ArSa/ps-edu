@extends('layouts.app')

@section('title', $course->name)

@section('breadcrumb')
    <a href="{{ route('alumno.dashboard') }}" class="hover:text-primary-600">Mis Cursos</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">{{ $course->name }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-5" x-data="alumnoCoursePage()">

    {{-- ═══════════════════════════════════════════════════════════════════════
         COURSE HERO HEADER
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="relative bg-gradient-to-br from-primary-700 via-primary-800 to-indigo-900 rounded-2xl overflow-hidden shadow-lg animate-fade-in-up">
        {{-- Decorative elements --}}
        <div class="absolute inset-0 opacity-[0.04]" style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fff' fill-opacity='1' fill-rule='evenodd'%3E%3Cpath d='M0 38.59l2.83-2.83 1.41 1.41L1.41 40H0v-1.41zM0 1.4l2.83 2.83 1.41-1.41L1.41 0H0v1.41zM38.59 40l-2.83-2.83 1.41-1.41L40 38.59V40h-1.41zM40 1.41l-2.83 2.83-1.41-1.41L38.59 0H40v1.41zM20 18.6l2.83-2.83 1.41 1.41L21.41 20l2.83 2.83-1.41 1.41L20 21.41l-2.83 2.83-1.41-1.41L18.59 20l-2.83-2.83 1.41-1.41L20 18.59z'/%3E%3C/g%3E%3C/svg%3E&quot;)"></div>
        <div class="absolute -top-20 -right-20 w-56 h-56 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-16 -left-16 w-44 h-44 bg-primary-400/10 rounded-full blur-3xl"></div>

        <div class="relative px-6 py-6 sm:px-8">
            {{-- Top badges --}}
            <div class="flex items-center gap-2 flex-wrap mb-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-400/20 text-emerald-200 ring-1 ring-emerald-400/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Matriculado
                </span>
                <span class="text-xs font-mono text-primary-200/80 bg-white/10 px-2.5 py-1 rounded-md">{{ $course->code }}</span>
                @if($course->semesterPeriod)
                <span class="inline-flex items-center gap-1 text-xs text-primary-200 bg-white/10 px-2.5 py-1 rounded-md">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $course->semesterPeriod->name }}
                </span>
                @elseif($course->semester)
                <span class="inline-flex items-center gap-1 text-xs text-primary-200 bg-white/10 px-2.5 py-1 rounded-md">
                    Semestre {{ $course->semester }} — {{ $course->year }}
                </span>
                @endif
                @if($course->programBelongs)
                <span class="inline-flex items-center gap-1 text-xs text-primary-200 bg-white/10 px-2.5 py-1 rounded-md">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $course->programBelongs->name }}
                </span>
                @elseif($course->program)
                <span class="inline-flex items-center gap-1 text-xs text-primary-200 bg-white/10 px-2.5 py-1 rounded-md">
                    {{ $course->program }}
                </span>
                @endif
            </div>

            {{-- Title & teacher --}}
            <h1 class="text-2xl lg:text-3xl font-extrabold text-white leading-tight tracking-tight">{{ $course->name }}</h1>

            @if($course->teacher)
            <div class="flex items-center gap-2.5 mt-3">
                <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-white text-sm font-bold ring-2 ring-white/20">
                    {{ strtoupper(substr($course->teacher->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-white/90">{{ $course->teacher->name }}</p>
                    <p class="text-xs text-primary-300">Docente del curso</p>
                </div>
            </div>
            @endif

            @if($course->description)
            <p class="mt-3 text-sm text-primary-200/80 max-w-2xl leading-relaxed">{{ $course->description }}</p>
            @endif

            {{-- Quick action --}}
            <div class="flex items-center gap-2 mt-5 pt-4 border-t border-white/10">
                <a href="{{ route('alumno.grades.show', $course) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-lg backdrop-blur-sm border border-white/10 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Ver mis calificaciones
                </a>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         STATS DASHBOARD — progress cards
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 animate-fade-in-up delay-1">
        {{-- Materials --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['totalMaterials'] }}</p>
                    <p class="text-[11px] text-gray-400 font-medium">Materiales</p>
                </div>
            </div>
        </div>
        {{-- Tasks --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['totalTasks'] }}</p>
                    <p class="text-[11px] text-gray-400 font-medium">Tareas</p>
                </div>
            </div>
        </div>
        {{-- Submitted --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['submitted'] }}</p>
                    <p class="text-[11px] text-gray-400 font-medium">Entregadas</p>
                </div>
            </div>
        </div>
        {{-- Pending --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $stats['pending'] > 0 ? 'bg-amber-50' : 'bg-emerald-50' }} flex items-center justify-center shrink-0">
                    @if($stats['pending'] > 0)
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    <p class="text-[11px] {{ $stats['pending'] > 0 ? 'text-amber-500 font-semibold' : 'text-gray-400' }} font-medium">Pendientes</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         PROGRESS BAR (overall submission progress)
    ═══════════════════════════════════════════════════════════════════════ --}}
    @if($stats['totalTasks'] > 0)
    <div class="bg-white rounded-xl border border-gray-200 p-5 animate-fade-in-up delay-1">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Mi progreso
            </span>
            <span class="text-xs font-bold text-primary-600">{{ $stats['submitted'] }} / {{ $stats['totalTasks'] }} tareas entregadas</span>
        </div>
        @php $pct = $stats['totalTasks'] > 0 ? round(($stats['submitted'] / $stats['totalTasks']) * 100) : 0; @endphp
        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-700 ease-out
                {{ $pct === 100 ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : 'bg-gradient-to-r from-primary-400 to-primary-600' }}"
                 style="width: {{ $pct }}%"></div>
        </div>
        <div class="flex items-center justify-between mt-2 text-xs text-gray-400">
            <span>{{ $pct }}% completado</span>
            @if($stats['graded'] > 0)
            <span class="flex items-center gap-1">
                <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ $stats['graded'] }} calificada{{ $stats['graded'] > 1 ? 's' : '' }}
            </span>
            @endif
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════
         TABS: Contenido / Tareas
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 rounded-xl p-1.5 flex gap-1 animate-fade-in-up delay-1">
        <button @click="activeTab = 'content'"
                :class="activeTab === 'content' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-lg transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            Contenido
            <span class="text-xs px-1.5 py-0.5 rounded-full" :class="activeTab === 'content' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500'">{{ $course->weeks->count() }}</span>
        </button>
        <button @click="activeTab = 'tasks'"
                :class="activeTab === 'tasks' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-lg transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Mis Tareas
            <span class="text-xs px-1.5 py-0.5 rounded-full" :class="activeTab === 'tasks' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500'">{{ $stats['totalTasks'] }}</span>
        </button>
        <a href="{{ route('alumno.forum.index', $course) }}"
           class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-lg text-gray-500 hover:text-teal-700 hover:bg-teal-50 transition-all ml-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            Foro
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         CONTENT TAB — Weeks with materials
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'content'" class="space-y-4">
        {{-- Expand / Collapse controls --}}
        @if($course->weeks->count() > 1)
        <div class="flex justify-end gap-1.5">
            <button @click="$dispatch('expand-all')"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-500 hover:text-primary-600 hover:bg-primary-50 hover:border-primary-200 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                Expandir todas
            </button>
            <button @click="$dispatch('collapse-all')"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/></svg>
                Colapsar todas
            </button>
        </div>
        @endif

        @forelse($course->weeks->sortBy('number') as $week)
        @php
            $weekMats  = $week->materials->count();
            $weekTasks = $week->tasks->where('status', 'active')->count();
            $weekEvals = $week->evaluations->count();
            $weekSubmittedCount = $week->tasks->where('status', 'active')->filter(fn($t) => $submissions->has($t->id))->count();
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow"
             x-data="{ expanded: {{ $loop->first ? 'true' : 'false' }} }"
             @expand-all.window="expanded = true"
             @collapse-all.window="expanded = false">

            {{-- Week header --}}
            <button @click="expanded = !expanded"
                    class="w-full flex items-center gap-3 px-5 py-4 hover:bg-gray-50/60 transition-colors text-left bg-gradient-to-r from-gray-50/80 to-white">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-sm">
                    {{ $week->number }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-primary-600 uppercase tracking-wider">Semana {{ $week->number }}</span>
                        @if($week->title)
                        <span class="text-sm font-semibold text-gray-900">{{ $week->title }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                            <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="font-medium text-blue-600">{{ $weekMats }}</span> materiales
                        </span>
                        @if($weekTasks > 0)
                        <span class="text-gray-200">·</span>
                        <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                            <svg class="w-3 h-3 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span class="font-medium text-violet-600">{{ $weekSubmittedCount }}/{{ $weekTasks }}</span> entregadas
                        </span>
                        @endif
                        @if($weekEvals > 0)
                        <span class="text-gray-200">·</span>
                        <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                            <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span class="font-medium text-amber-600">{{ $weekEvals }}</span> {{ $weekEvals === 1 ? 'evaluación' : 'evaluaciones' }}
                        </span>
                        @endif
                    </div>
                </div>
                <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Week content --}}
            <div x-show="expanded"
                 x-transition:enter="transition-all duration-200 ease-out"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0">

                @if($week->description)
                <div class="px-5 py-3 bg-blue-50/40 border-t border-gray-100">
                    <p class="text-sm text-gray-600 flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $week->description }}
                    </p>
                </div>
                @endif

                {{-- Materials --}}
                @if($week->materials->isEmpty() && $weekTasks === 0 && $weekEvals === 0)
                <div class="px-5 py-6 text-center border-t border-gray-100">
                    <p class="text-xs text-gray-400">Sin contenido disponible en esta semana.</p>
                </div>
                @else

                @if($week->materials->isNotEmpty())
                <div class="border-t border-gray-100">
                    <div class="px-5 py-2.5 bg-gray-50/50">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Materiales ({{ $weekMats }})
                        </p>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($week->materials->sortBy('order') as $material)
                        <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50/60 transition-colors group">
                            @if($material->type === 'file')
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            @elseif($material->type === 'video')
                            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            @else
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $material->title }}</p>
                                @if($material->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $material->description }}</p>
                                @endif
                                <div class="mt-1.5">
                                    @if($material->type === 'file')
                                        <a href="{{ $material->download_url }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-700 hover:underline font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Descargar archivo
                                        </a>
                                    @elseif($material->type === 'video')
                                        <a href="{{ $material->url }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 text-xs text-red-600 hover:text-red-700 hover:underline font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Ver video
                                        </a>
                                    @else
                                        <a href="{{ $material->url }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 text-xs text-emerald-600 hover:text-emerald-700 hover:underline font-medium">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            Abrir enlace
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Quick action arrow on hover --}}
                            <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity self-center">
                                @if($material->type === 'file')
                                <a href="{{ $material->download_url }}" target="_blank" class="p-1.5 text-gray-300 hover:text-blue-500 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                @else
                                <a href="{{ $material->url ?? $material->download_url }}" target="_blank" class="p-1.5 text-gray-300 hover:text-primary-500 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Tasks in content tab --}}
                @include('alumno.courses._week-tasks', ['week' => $week, 'course' => $course, 'submissions' => $submissions, 'evalAttempts' => $evalAttempts])

                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-primary-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Aún no hay contenido disponible</h3>
            <p class="text-xs text-gray-400">El docente aún no ha publicado materiales para este curso.</p>
        </div>
        @endforelse
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         TASKS TAB — All tasks in a flat list grouped by status
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'tasks'" x-cloak class="space-y-4">
        @php
            $allTasks = $course->weeks->sortBy('number')
                ->pluck('tasks')->flatten()
                ->where('status', 'active');
            $pendingTasks  = $allTasks->filter(fn($t) => !$submissions->has($t->id) && !$t->isExpired());
            $submittedTasks = $allTasks->filter(fn($t) => $submissions->has($t->id) && !$submissions->get($t->id)->isGraded());
            $gradedTasks   = $allTasks->filter(fn($t) => $submissions->has($t->id) && $submissions->get($t->id)->isGraded());
            $expiredTasks  = $allTasks->filter(fn($t) => !$submissions->has($t->id) && $t->isExpired());
        @endphp

        {{-- Pending tasks --}}
        @if($pendingTasks->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-5 py-3.5 bg-amber-50/50 border-b border-amber-100/60 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-bold text-amber-800">Pendientes de entrega</h3>
                <span class="ml-auto text-xs font-bold text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">{{ $pendingTasks->count() }}</span>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($pendingTasks as $task)
                @php $week = $course->weeks->firstWhere('id', $task->week_id); @endphp
                <div class="px-5 py-4 hover:bg-gray-50/60 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-gray-900">{{ $task->title }}</p>
                                @if($week)
                                <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">S{{ $week->number }}</span>
                                @endif
                                @php $badge = $task->due_date_badge; @endphp
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 font-medium">{{ $task->max_score }} pts</span>
                            </div>
                            @if($task->description)
                            <p class="text-xs text-gray-500 mt-1">{{ $task->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Submitted (awaiting grade) --}}
        @if($submittedTasks->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-5 py-3.5 bg-blue-50/50 border-b border-blue-100/60 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-bold text-blue-800">Entregadas — esperando calificación</h3>
                <span class="ml-auto text-xs font-bold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">{{ $submittedTasks->count() }}</span>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($submittedTasks as $task)
                @php
                    $submission = $submissions->get($task->id);
                    $week = $course->weeks->firstWhere('id', $task->week_id);
                @endphp
                <div class="px-5 py-4 hover:bg-gray-50/60 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-gray-900">{{ $task->title }}</p>
                                @if($week)
                                <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">S{{ $week->number }}</span>
                                @endif
                                <span class="text-xs px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 font-medium">{{ $task->max_score }} pts</span>
                            </div>
                            <p class="text-xs text-blue-600 mt-1">
                                <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Entregada el {{ $submission->submitted_at?->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Graded --}}
        @if($gradedTasks->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-5 py-3.5 bg-emerald-50/50 border-b border-emerald-100/60 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <h3 class="text-sm font-bold text-emerald-800">Calificadas</h3>
                <span class="ml-auto text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">{{ $gradedTasks->count() }}</span>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($gradedTasks as $task)
                @php
                    $submission = $submissions->get($task->id);
                    $week = $course->weeks->firstWhere('id', $task->week_id);
                @endphp
                <div class="px-5 py-4 hover:bg-gray-50/60 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="px-2.5 py-1.5 bg-emerald-50 rounded-xl text-center shrink-0 mt-0.5 border border-emerald-100">
                            <p class="text-lg font-bold {{ $submission->score_color }}">{{ $submission->score }}</p>
                            <p class="text-[10px] text-gray-400">/{{ $task->max_score }}</p>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-gray-900">{{ $task->title }}</p>
                                @if($week)
                                <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">S{{ $week->number }}</span>
                                @endif
                            </div>
                            @if($submission->feedback)
                            <p class="text-xs text-emerald-700 mt-1 bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-emerald-100">
                                <span class="font-semibold">Retroalimentación:</span> {{ $submission->feedback }}
                            </p>
                            @endif
                            <p class="text-[11px] text-gray-400 mt-1">Calificada el {{ $submission->graded_at?->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Expired (not submitted) --}}
        @if($expiredTasks->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-5 py-3.5 bg-red-50/50 border-b border-red-100/60 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-bold text-red-700">Vencidas — no entregadas</h3>
                <span class="ml-auto text-xs font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">{{ $expiredTasks->count() }}</span>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($expiredTasks as $task)
                @php $week = $course->weeks->firstWhere('id', $task->week_id); @endphp
                <div class="px-5 py-4 hover:bg-gray-50/60 transition-colors opacity-70">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-gray-600 line-through">{{ $task->title }}</p>
                                @if($week)
                                <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">S{{ $week->number }}</span>
                                @endif
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-medium">Vencida</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 font-medium">{{ $task->max_score }} pts</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- All done! --}}
        @if($allTasks->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-10 text-center">
            <div class="w-14 h-14 rounded-full bg-violet-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">No hay tareas asignadas aún</p>
            <p class="text-xs text-gray-400 mt-0.5">Las tareas aparecerán aquí cuando el docente las publique.</p>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function alumnoCoursePage() {
    return {
        activeTab: 'content',
    };
}
</script>
@endpush
